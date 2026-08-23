<?php

namespace App\Support\Application;

use App\Enums\ApplicationStatus;
use App\Enums\UserRole;
use App\Models\Application;
use App\Models\ApplicationDocument;
use App\Models\User;
use App\Notifications\ApplicationStatusNotification;
use Illuminate\Support\Facades\Log;
use Throwable;

final class ApplicationWorkflowNotifier
{
    public function applicationSubmitted(Application $application, User $actor): void
    {
        if (! $actor->isCitizen()) {
            return;
        }

        $this->notifyCitizen($application, ApplicationStatusNotification::submitted($application));
    }

    public function statusChanged(
        Application $application,
        User $actor,
        ?ApplicationStatus $from,
        ApplicationStatus $to,
        ?string $note = null,
    ): void {
        // Chỉ báo cho citizen các trạng thái công khai, ẩn Assigned/PendingApproval để tránh leak workflow nội bộ
        $citizenVisibleStatuses = [
            ApplicationStatus::Received,
            ApplicationStatus::Processing,
            ApplicationStatus::SupplementRequired,
            ApplicationStatus::Approved,
            ApplicationStatus::Rejected,
        ];

        if (in_array($to, $citizenVisibleStatuses, true)) {
            $this->notifyCitizen($application, ApplicationStatusNotification::statusChanged($application, $to));
        }

        // Khi staff gửi duyệt, báo cho manager phụ trách để manager thấy ngay trên dashboard/notifications
        if ($to === ApplicationStatus::PendingApproval) {
            $this->notifyManagersForPendingApproval($application);
        }
    }

    public function resultDocumentAvailable(Application $application, ApplicationDocument $document, User $actor): void
    {
        // Ẩn leak cho citizen: tài liệu kết quả chỉ được citizen thấy sau khi duyệt (Approved).
        // Trước đó (Processing/PendingApproval) việc upload result không được báo cho citizen
        // để tránh lộ "đã có kết quả" trước khi có quyết định chính thức.
        if (! $application->status->isTerminal()) {
            return;
        }

        $this->notifyCitizen($application, ApplicationStatusNotification::resultDocumentAvailable($application, $document));
    }

    private function notifyCitizen(Application $application, ApplicationStatusNotification $notification): void
    {
        $application->loadMissing('citizen');

        $citizen = $application->citizen;

        if ($citizen === null || ! $citizen->isCitizen() || ! $citizen->canAccessProtectedResources()) {
            return;
        }

        try {
            $citizen->notify($notification);
        } catch (Throwable $exception) {
            Log::warning('Could not create application workflow notification.', [
                'application_id' => $application->getKey(),
                'citizen_id' => $citizen->getKey(),
                'exception' => $exception::class,
                'message' => $exception->getMessage(),
            ]);
        }
    }

    private function notifyManagersForPendingApproval(Application $application): void
    {
        $application->loadMissing('serviceType');

        $departmentId = $application->serviceType?->responsible_department_id;

        if ($departmentId === null) {
            return;
        }

        $managers = User::query()
            ->where('role', UserRole::Manager->value)
            ->where('is_active', true)
            ->whereHas('ledDepartments', fn ($q) => $q->where('departments.id', $departmentId))
            ->get();

        if ($managers->isEmpty()) {
            return;
        }

        $notification = new ApplicationStatusNotification(
            applicationId: $application->getKey(),
            applicationCode: $application->application_code,
            event: 'application.pending_approval',
            title: 'Hồ sơ chờ duyệt',
            message: "Hồ sơ {$application->application_code} đã được cán bộ gửi chờ duyệt.",
            status: ApplicationStatus::PendingApproval,
            url: "/admin/applications/{$application->getKey()}",
        );

        foreach ($managers as $manager) {
            try {
                $manager->notify($notification);
            } catch (Throwable $exception) {
                Log::warning('Could not notify manager for pending approval.', [
                    'application_id' => $application->getKey(),
                    'manager_id' => $manager->getKey(),
                    'exception' => $exception::class,
                    'message' => $exception->getMessage(),
                ]);
            }
        }
    }
}
