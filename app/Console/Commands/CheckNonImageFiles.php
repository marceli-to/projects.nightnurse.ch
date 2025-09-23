<?php

namespace App\Console\Commands;

use App\Models\MessageFile;
use Illuminate\Console\Command;

class CheckNonImageFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:non-image-files {--preview-only : Only show files with preview=1 or has_preview=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for non-image files and display them in a table';

    /**
     * Image file extensions that should be considered valid image files
     *
     * @var array
     */
    protected $imageExtensions = ['png', 'jpg', 'jpeg', 'gif', 'webp', 'bmp'];

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $query = MessageFile::whereNotIn('extension', $this->imageExtensions);

        if ($this->option('preview-only')) {
            $query->where(function($q) {
                $q->where('preview', 1)->orWhere('has_preview', 1);
            });
        }

        $nonImageFiles = $query->orderBy('extension')
            ->orderBy('created_at', 'desc')
            ->get();

        if ($nonImageFiles->isEmpty()) {
            if ($this->option('preview-only')) {
                $this->info('No non-image files found with preview flags set to 1.');
            } else {
                $this->info('No non-image files found.');
            }
            return 0;
        }

        $this->info("Found {$nonImageFiles->count()} non-image files:");

        // Group by extension for summary
        $extensionCounts = $nonImageFiles->groupBy('extension')->map->count()->sortByDesc(function($count) {
            return $count;
        });

        $this->line('');
        $this->info('Summary by file extension:');
        foreach ($extensionCounts as $extension => $count) {
            $this->line("  {$extension}: {$count} files");
        }

        $this->line('');

        // Display detailed table
        $headers = ['ID', 'Name', 'Extension', 'Size', 'Preview', 'Has Preview', 'Created'];
        $rows = [];

        foreach ($nonImageFiles as $file) {
            $rows[] = [
                $file->id,
                $this->truncateFilename($file->original_name ?? $file->name, 30),
                $file->extension,
                $this->formatFileSize($file->size),
                $file->preview ? 'Yes' : 'No',
                $file->has_preview ? 'Yes' : 'No',
                $file->created_at->format('Y-m-d H:i'),
            ];
        }

        $this->table($headers, $rows);

        // Show problematic files (non-images with preview flags)
        $problematicFiles = $nonImageFiles->filter(function($file) {
            return $file->preview == 1 || $file->has_preview == 1;
        });

        if ($problematicFiles->count() > 0) {
            $this->line('');
            $this->warn("⚠️  Found {$problematicFiles->count()} non-image files with preview flags set to 1:");
            $this->warn("   These files may cause thumbnail generation errors.");
            $this->line('');
            $this->info("To fix these issues, run:");
            $this->line("php artisan tinker --execute=\"\\App\\Models\\MessageFile::whereNotIn('extension', ['png', 'jpg', 'jpeg', 'gif'])->update(['preview' => 0, 'has_preview' => 0]);\"");
        }

        return 0;
    }

    /**
     * Format file size in human readable format
     *
     * @param int $size
     * @return string
     */
    private function formatFileSize($size)
    {
        if (!$size) return '0 B';

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $power = $size > 0 ? floor(log($size, 1024)) : 0;
        return number_format($size / pow(1024, $power), 1, '.', ',') . ' ' . $units[$power];
    }

    /**
     * Truncate filename if too long
     *
     * @param string $filename
     * @param int $maxLength
     * @return string
     */
    private function truncateFilename($filename, $maxLength)
    {
        if (strlen($filename) <= $maxLength) {
            return $filename;
        }

        return substr($filename, 0, $maxLength - 3) . '...';
    }
}