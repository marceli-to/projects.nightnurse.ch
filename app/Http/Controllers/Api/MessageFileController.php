<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Resources\DataCollection;
use App\Http\Resources\FileResource;
use App\Models\Message;
use App\Models\MessageFile;
use Illuminate\Http\Request;

class MessageFileController extends Controller
{
  /**
   * Get a alls file for a given message
   * 
   * @param Message $message
   * @return \Illuminate\Http\Response
   */
  public function get(Message $message)
  {
    $files = MessageFile::with('markups')->where('message_id', $message->id)->get();
    return FileResource::collection($files);
  }

  /**
   * Get a single file for a given file
   * 
   * @param MessageFile $messageFile
   * @return \Illuminate\Http\Response
   */
  public function find(MessageFile $messageFile)
  {
    $file = MessageFile::with('markups')->findOrFail($messageFile->id);
    return FileResource::make($file);
  }

}
