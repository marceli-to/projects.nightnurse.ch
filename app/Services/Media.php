<?php
namespace App\Services;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Image;

class Media
{ 

  /**
   * The upload path
   */
  protected $upload_path;

  /**
   * The storage path
   */
  protected $storage_path;

  /**
   *  Force lowercase for filenames
   */

  protected $force_lowercase = true;

  /**
   *  Image file types
   */

  protected $previewable_image_types = [
    'png', 'jpg', 'jpeg', 'gif'
  ];
  
  /**
   * Constructor
   */
  public function __construct($opts = array())
  {
    $this->storage_path = storage_path('app/public/uploads');
    $this->upload_path = storage_path('app/public/uploads/temp');

    if (isset($opts['force_lowercase']))
    {
      $this->force_lowercase = $opts['force_lowercase'];
    }

    if (!File::isDirectory($this->upload_path))
    {
      File::makeDirectory($this->upload_path, 0775, true, true);
    }

  }

  /**
   * Store a file to the storage
   * 
   * @param  \Illuminate\Http\Request $request
   * @param  String $destinationFolder
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request, $destinationFolder = NULL)
  {
    $file = $request->file('file');
    $file_data = getimagesize($request->file('file'));
    $name = $this->sanitize(trim($file->getClientOriginalName()), $this->force_lowercase);
    
    
    // $filename = uniqid()  . '_' . $name;
    // $filename = $name;
    $filename = $this->uniqueFileName($name);


    $file->move($this->upload_path, $filename);
    $filetype = File::extension($this->upload_path . DIRECTORY_SEPARATOR . $filename);
    $filesize = File::size($this->upload_path . DIRECTORY_SEPARATOR . $filename);

    $previewable = $file_data !== FALSE ? $this->previewable($file_data, $filetype) : FALSE;

    return [
      'name' => $filename, 
      'original_name' => $name, 
      'extension' => $filetype, 
      'size' => $filesize,
      'preview' => $previewable ? TRUE : FALSE
    ];
  }

  /**
   * Copy a file from temp to storage folder
   * 
   * @param String $filename
   */
  public function copy($filename = NULL)
  {
    return Storage::move('public/uploads/temp' . DIRECTORY_SEPARATOR . $filename, 'public/uploads' . DIRECTORY_SEPARATOR . $filename);
  }

  /**
   * Remove a bunch of files from storage
   * 
   * @param Array $files
   */
  public function removeMany($files = NULL, $temp = FALSE)
  {
    foreach($files as $file)
    {
      if ($temp)
      {
        Storage::delete('public/uploads/temp' . DIRECTORY_SEPARATOR . $file->name);
      }
      Storage::delete('public/uploads' . DIRECTORY_SEPARATOR . $file->name);
    }
    return true;
  }

  /**
   * Removes a file from storage
   * 
   * @param String $filename
   */
  public function remove($filename = NULL, $temp = FALSE)
  {
    if ($temp)
    {
      return Storage::delete('public/uploads/temp' . DIRECTORY_SEPARATOR . $filename);
    }
    return Storage::delete('public/uploads' . DIRECTORY_SEPARATOR . $filename);
  }

  /**
   * Sanitize a filename
   *
   * @param str $filename
   * @param boolean  $force_lowercase - Force the string to lowercase?
   * @param boolean  $anal - If set to *true*, will remove all non-alphanumeric characters.
   */

  protected function sanitize($filename, $force_lowercase = true, $anal = true)
  {
    $strip = array("~", "`", "!", "@", "#", "$", "%", "^", "&", "*", "(", ")", "=", "+", "[", "{", "]", "}", "\\", "|", ";", ":", "\"", "'", "&#8216;", "&#8217;", "&#8220;", "&#8221;", "&#8211;", "&#8212;", "â€”", "â€“", ",", "<", ">", "/", "?");
    $clean = trim(str_replace($strip, "", strip_tags($filename)));
    $clean = preg_replace('/\s+/', "-", $clean);
    $clean = ($anal) ? preg_replace("/[^a-zA-Z0-9._\-]/", "", $clean) : $clean ;
    return ($force_lowercase) ? (function_exists('mb_strtolower')) ? mb_strtolower($clean, 'UTF-8') : strtolower($clean) : $clean;
  }

  /**
   * Get the formatted size of a file
   *
   * @param $size
   * @return Number $size
   */

  protected function filesize($size)
  {
    $units = array( 'B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
    $power = $size > 0 ? floor(log($size, 1024)) : 0;
    return number_format($size / pow(1024, $power), 2, '.', ',') . ' ' . $units[$power];
  }

   /**
   * Check if file is previewable
   *
   * @param Array $file_data
   * @param String $filetype
   * @return Boolean
   */
 
  protected function previewable($file_data = NULL, $filetype = NULL)
  {
    if (in_array(strtolower($filetype), $this->previewable_image_types))
    {
      if ($file_data[0] <= 5000)
      {
        return TRUE;
      }
    }
    return FALSE;
  }

  /**
   * Generate a unique filename by adding an incrementable number at the end
   * 
   * Expample: 
   * - file.png => file-1.png
   * - file-1.png => file-2.png
   */
  protected function uniqueFileName($filename)
  {
    $current_name = pathinfo($filename, PATHINFO_FILENAME);
    $name = $current_name;
    $extension = pathinfo($filename, PATHINFO_EXTENSION);

    // check in destination folder
    while(file_exists($this->storage_path . DIRECTORY_SEPARATOR . $name . '.' . $extension))
    {           
      $name = $name. '-' .  uniqid();
    }

    // check in temp folder
    $current_name = pathinfo($name, PATHINFO_FILENAME);
    $name = $current_name;

    while(file_exists($this->upload_path . DIRECTORY_SEPARATOR . $name . '.' . $extension))
    {           
      $name = $name. '-' .  uniqid();
    }

    return $name . '.' . $extension;
  }
}
