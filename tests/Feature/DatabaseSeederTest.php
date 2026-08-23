<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Department;
use App\Models\ServiceCategory;
use App\Models\ServiceType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder_creates_complete_and_consistent_demo_data(): void
    {
        $this->seed();

        $this->assertDatabaseCount('departments', 8);
        $this->assertDatabaseCount('service_categories', 15);
        $this->assertDatabaseCount('service_types', 30);

        $this->assertSame(1, User::query()->where('role', UserRole::SuperAdmin->value)->count());
        $this->assertSame(13, User::query()->where('role', UserRole::Manager->value)->count());
        $this->assertSame(23, User::query()->where('role', UserRole::Staff->value)->count());
        $this->assertSame(2, User::query()->where('role', UserRole::Citizen->value)->count());

        $this->assertSame(5, User::query()->availableDepartmentLeaders()->count());
        $this->assertSame(7, User::query()->availableDepartmentStaff()->count());

        $assignedManagerIds = Department::query()->pluck('leader_id');
        $this->assertCount(8, $assignedManagerIds->unique());
        $this->assertSame(8, User::query()->whereIn('id', $assignedManagerIds)->where('role', UserRole::Manager->value)->count());

        Department::query()->with(['leader', 'members', 'serviceTypes.staff.departments'])->get()->each(function (Department $department): void {
            $this->assertNotNull($department->name);
            $this->assertNotNull($department->code);
            $this->assertNotNull($department->address);
            $this->assertTrue($department->leader->isManager());
            $this->assertTrue($department->members->contains($department->leader));

            $departmentStaff = $department->members->filter(fn (User $user): bool => $user->isStaff());
            $this->assertCount(2, $departmentStaff);
            $this->assertNotEmpty($department->serviceTypes);

            $department->serviceTypes->each(function (ServiceType $service) use ($department): void {
                $this->assertSame($department->id, $service->responsible_department_id);
                $this->assertCount(2, $service->staff);
                $service->staff->each(function (User $staff) use ($department): void {
                    $this->assertTrue($staff->isStaff());
                    $this->assertTrue($staff->departments->contains($department));
                });
            });
        });

        ServiceCategory::query()->with('serviceTypes')->get()->each(function (ServiceCategory $category): void {
            $this->assertNotEmpty($category->name);
            $this->assertNotEmpty($category->code);
            $this->assertNotEmpty($category->description);
            $this->assertCount(2, $category->serviceTypes);
        });

        ServiceType::query()->get()->each(function (ServiceType $service): void {
            $this->assertNotEmpty($service->name);
            $this->assertNotEmpty($service->code);
            $this->assertNotEmpty($service->description);
            $this->assertNotEmpty($service->requirements);
            $this->assertNotEmpty($service->form_schema);
            $this->assertNotEmpty($service->document_requirements);
            $this->assertGreaterThan(0, $service->processing_time_days);
            $this->assertGreaterThanOrEqual(0, (float) $service->fee);
            $this->assertTrue($service->is_active);
        });

        User::query()
            ->whereIn('role', [UserRole::Manager->value, UserRole::Staff->value])
            ->whereDoesntHave('departments')
            ->get()
            ->each(function (User $user): void {
                $this->assertEmpty($user->ledDepartments);
                $this->assertEmpty($user->serviceTypes);
            });

        foreach (['manager@example.test', 'staff1@example.test', 'staff2@example.test', 'citizen1@example.test', 'citizen2@example.test'] as $email) {
            $user = User::query()->where('email', $email)->firstOrFail();

            $this->assertTrue(Hash::check('password', $user->password));
            $this->assertNotNull($user->date_of_birth);
            $this->assertNotEmpty($user->gender);
            $this->assertNotEmpty($user->phone);
            $this->assertNotEmpty($user->address);
            $this->assertTrue($user->is_active);
        }
    }
}
