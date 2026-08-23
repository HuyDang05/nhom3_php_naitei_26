<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Normalize statuses from the retired application workflow.
     *
     * Assignment is represented by assigned_staff_id in the current model, so
     * an assigned application remains received until processing is started.
     * The retired pending-approval step is now part of processing.
     */
    public function up(): void
    {
        DB::transaction(function (): void {
            DB::table('applications')
                ->where('status', 'assigned')
                ->update(['status' => 'received']);

            DB::table('applications')
                ->where('status', 'pending_approval')
                ->update(['status' => 'processing']);

            foreach (['from_status', 'to_status'] as $column) {
                DB::table('application_status_histories')
                    ->where($column, 'assigned')
                    ->update([$column => 'received']);

                DB::table('application_status_histories')
                    ->where($column, 'pending_approval')
                    ->update([$column => 'processing']);
            }
        });
    }

    public function down(): void
    {
        // The legacy values cannot be reconstructed unambiguously.
    }
};
