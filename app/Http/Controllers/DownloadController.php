<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\Message;
use ZipArchive;
use Illuminate\Http\Request;

class DownloadController extends Controller
{
  /**
   * Get a single messages for a given messages
   * 
   * @param Message $message
   * @return \Illuminate\Http\Response
   */
  public function download(Message $message)
  {
    $zipArchive = new ZipArchive;
    $fileName = $this->filename($message);

    if ($zipArchive->open(public_path($fileName), ZipArchive::CREATE) === TRUE)
    {
      $path = storage_path('app/public/uploads/');
      foreach($message->files as $file)
      {
        $zipArchive->addFile($path . $file->name, $file->name);
      }
      $zipArchive->close();
    }

    return response()->download(public_path($fileName));
  }

  /**
   * Generate a unique filename for the zip file
   * 
   * @param $message
   * @return \Illuminate\Http\Response
   */
  protected function filename($message)
  {
    return $this->sanitize($message->project->number) . '-' . $this->sanitize($message->project->name) . '-' . uniqid() . '.zip';
  }

  /**
   * Sanitize a string
   *
   * @param String $string
   * @param boolean  $force_lowercase - Force the string to lowercase?
   * @param boolean  $anal - If set to *true*, will remove all non-alphanumeric characters.
   */

  protected function sanitize($string, $force_lowercase = true, $anal = true)
  {
    $strip = array("~", "`", "!", "@", "#", "$", "%", "^", "&", "*", "(", ")", "=", "+", "[", "{", "]", "}", "\\", "|", ";", ":", "\"", "'", "&#8216;", "&#8217;", "&#8220;", "&#8221;", "&#8211;", "&#8212;", "â€”", "â€“", ",", "<", ">", "/", "?");
    $clean = trim(str_replace($strip, "", strip_tags($string)));
    $clean = preg_replace('/\s+/', "-", $clean);
    $clean = ($anal) ? preg_replace("/[^a-zA-Z0-9._\-]/", "", $clean) : $clean ;
    return ($force_lowercase) ? (function_exists('mb_strtolower')) ? mb_strtolower($clean, 'UTF-8') : strtolower($clean) : $clean;
  }
}
