<?php
namespace App\Console\Commands;
use App\Models\Project;
use Illuminate\Console\Command;

class CreateSlugs extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'create:slugs';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Creates slugs for all projects';


  /**
   * Create a new command instance.
   *
   * @return void
   */
  public function __construct()
  {
    parent::__construct();
  }

  /**
   * Execute the console command.
   *
   * @return int
   */
  public function handle()
  {
    $projects = Project::withTrashed()->get();

    foreach ($projects as $project)
    {
      $project->slug = null;
      $project->update([
        'number' => $project->number,
        'name' => $project->name,
      ]);
    }
  }
}
