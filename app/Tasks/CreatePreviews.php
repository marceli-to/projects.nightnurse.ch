<?php
namespace App\Tasks;

class CreatePreviews
{
  public function __invoke()
  {
    // Call the artisan command to create previews
    Artisan::call('create:previews');
  }
}