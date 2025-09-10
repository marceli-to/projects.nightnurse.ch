<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Project;
use App\Models\Message;

class AnalyzeProjectsMessages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'project:analyze {project? : Specific project ID to analyze}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Analyze projects and messages statistics (deleted/not deleted)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $projectId = $this->argument('project');
        
        if ($projectId) {
            $this->analyzeSingleProject($projectId);
        } else {
            $this->analyzeAllProjects();
        }
        
        return 0;
    }
    
    private function analyzeAllProjects()
    {
        $this->info('=== PROJECT ANALYSIS ===');
        
        // Project statistics
        $activeProjects = Project::count();
        $deletedProjects = Project::onlyTrashed()->count();
        $totalProjects = $activeProjects + $deletedProjects;
        
        $this->table(['Status', 'Count'], [
            ['Active Projects', $activeProjects],
            ['Deleted Projects', $deletedProjects],
            ['Total Projects', $totalProjects]
        ]);
        
        // Message statistics  
        $activeMessages = Message::count();
        $deletedMessages = Message::onlyTrashed()->count();
        $totalMessages = $activeMessages + $deletedMessages;
        
        $this->info('=== MESSAGE ANALYSIS ===');
        $this->table(['Status', 'Count'], [
            ['Active Messages', $activeMessages],
            ['Deleted Messages', $deletedMessages],
            ['Total Messages', $totalMessages]
        ]);
        
        // Detailed project breakdown
        $this->info('=== DETAILED PROJECT BREAKDOWN ===');
        
        $projects = Project::withTrashed()->get();
        $data = [];
        
        foreach ($projects as $project) {
            $activeMessagesCount = $project->messages()->count();
            $deletedMessagesCount = $project->messages()->onlyTrashed()->count();
            
            $data[] = [
                'ID' => $project->id,
                'Status' => $project->trashed() ? 'DELETED' : 'Active',
                'Active Messages' => $activeMessagesCount,
                'Deleted Messages' => $deletedMessagesCount,
                'Total Messages' => $activeMessagesCount + $deletedMessagesCount
            ];
        }
        
        $this->table(['Project ID', 'Status', 'Active Messages', 'Deleted Messages', 'Total Messages'], $data);
    }
    
    private function analyzeSingleProject($projectId)
    {
        $project = Project::withTrashed()->find($projectId);
        
        if (!$project) {
            $this->error("Project ID {$projectId} not found.");
            return;
        }
        
        $this->info("=== PROJECT {$projectId} ANALYSIS ===");
        
        $projectStatus = $project->trashed() ? 'DELETED' : 'Active';
        $activeMessages = $project->messages()->count();
        $deletedMessages = $project->messages()->onlyTrashed()->count();
        $totalMessages = $activeMessages + $deletedMessages;
        
        $this->table(['Attribute', 'Value'], [
            ['Project ID', $project->id],
            ['Status', $projectStatus],
            ['Deleted At', $project->deleted_at ?? 'N/A'],
            ['Active Messages', $activeMessages],
            ['Deleted Messages', $deletedMessages],
            ['Total Messages', $totalMessages]
        ]);
        
        if ($deletedMessages > 0) {
            $this->info('=== DELETED MESSAGES DETAILS ===');
            $deletedMessagesDetails = $project->messages()->onlyTrashed()->get();
            
            $messageData = [];
            foreach ($deletedMessagesDetails as $message) {
                $messageData[] = [
                    'Message ID' => $message->id,
                    'Deleted At' => $message->deleted_at->format('Y-m-d H:i:s'),
                    'Time Diff from Project' => $project->deleted_at ? 
                        $message->deleted_at->diffInSeconds($project->deleted_at) . 's' : 'N/A'
                ];
            }
            
            $this->table(['Message ID', 'Deleted At', 'Time Diff from Project'], $messageData);
        }
    }
}
