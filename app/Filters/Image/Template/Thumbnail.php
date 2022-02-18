<?php
namespace App\Filters\Image\Template;
use Intervention\Image\Image;
use Intervention\Image\Filters\FilterInterface;

class Thumbnail implements FilterInterface
{
  protected $size = 160;
  
  public function applyFilter(Image $image)
  {
    return $image->fit($this->size);
  }
}