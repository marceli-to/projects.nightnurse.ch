<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Project;
use App\Models\Message;

class RestoreMessagesDeletedWithProjects extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'project:restore-messages {project? : Specific project ID to restore messages for} {--dry-run : Show what would be restored without actually doing it}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restore messages that were deleted when their projects were soft-deleted (not manually deleted messages)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        $projectId = $this->argument('project');
        
        if ($projectId) {
            $this->info("Finding messages for project ID {$projectId}...");
            $deletedProjects = Project::onlyTrashed()->where('id', $projectId)->get();
            
            if ($deletedProjects->isEmpty()) {
                $this->error("Project ID {$projectId} not found in soft-deleted projects.");
                return 1;
            }
        } else {
            $this->info('Finding messages that were deleted with their projects...');
            $deletedProjects = Project::onlyTrashed()->get();
        }
        
        $restoredCount = 0;
        
        foreach ($deletedProjects as $project) {
            // Find messages that were deleted within 1 minute before or after the project deletion
            $timeWindow = 60; // 1 minute in seconds
            $projectDeletedAt = $project->deleted_at;
            
            // Skip if project has no deletion timestamp (shouldn't happen but safety check)
            if (!$projectDeletedAt) {
                $this->warn("Project ID {$project->id}: No deletion timestamp, skipping");
                continue;
            }
            
            $windowStart = $projectDeletedAt->copy()->subSeconds($timeWindow);
            $windowEnd = $projectDeletedAt->copy()->addSeconds($timeWindow);
            
            $messagesToRestore = Message::onlyTrashed()
                ->where('project_id', $project->id)
                ->whereBetween('deleted_at', [$windowStart, $windowEnd])
                ->get();
            
            if ($messagesToRestore->count() > 0) {
                $this->line("Project ID {$project->id}: Found {$messagesToRestore->count()} message(s) to restore");
                
                if (!$isDryRun) {
                    foreach ($messagesToRestore as $message) {
                        $message->restore();
                        $restoredCount++;
                    }
                } else {
                    $restoredCount += $messagesToRestore->count();
                }
            }
        }
        
        if ($isDryRun) {
            $this->warn("DRY RUN: Would restore {$restoredCount} message(s)");
            $this->info('Run without --dry-run to actually restore the messages');
        } else {
            $this->info("Successfully restored {$restoredCount} message(s)");
        }
        
        return 0;
    }
}
