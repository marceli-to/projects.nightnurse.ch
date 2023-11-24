<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Resources\DataCollection;
use App\Models\MessageFile;
use Illuminate\Http\Request;

class MessageFileController extends Controller
{
  /**
   * Get a single file for a given file
   * 
   * @param MessageFile $messageFile
   * @return \Illuminate\Http\Response
   */
  public function find(MessageFile $messageFile)
  {
    $file = MessageFile::findOrFail($messageFile->id);
    // return response()->json(MessageResource::make($message));
    return response()->json($file);
  }

}
