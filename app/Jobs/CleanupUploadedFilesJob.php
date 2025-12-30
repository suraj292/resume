<?php

namespace App\Jobs;

use App\Models\ResumeAnalysis;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class CleanupUploadedFilesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Delete files older than this many days
     */
    const RETENTION_DAYS = 7;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('Starting cleanup of uploaded files...');

        $this->cleanupResumeAnalysisFiles();
        
        Log::info('Cleanup completed.');
    }

    /**
     * Clean up ResumeAnalysis files
     */
    protected function cleanupResumeAnalysisFiles(): void
    {
        // 1. Find records with files older than retention period
        $cutoffDate = Carbon::now()->subDays(self::RETENTION_DAYS);
        
        $oldRecords = ResumeAnalysis::whereNotNull('file_path')
            ->where('created_at', '<', $cutoffDate)
            ->get();

        $count = 0;

        foreach ($oldRecords as $record) {
            if (Storage::disk('public')->exists($record->file_path)) {
                Storage::disk('public')->delete($record->file_path);
                $count++;
            }

            // Update record to indicate file is gone
            $record->file_path = null;
            $record->save();
        }

        Log::info("Cleaned up {$count} old resume analysis files.");
    }
}
