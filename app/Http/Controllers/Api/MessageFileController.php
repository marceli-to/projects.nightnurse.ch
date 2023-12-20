<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Resources\DataCollection;
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
    $files = MessageFile::where('message_id', $message->id)->get();
    return response()->json($files);
  }

  /**
   * Get a single file for a given file
   * 
   * @param MessageFile $messageFile
   * @return \Illuminate\Http\Response
   */
  public function find(MessageFile $messageFile)
  {
    $file = MessageFile::findOrFail($messageFile->id);
    return response()->json($file);
  }

}
