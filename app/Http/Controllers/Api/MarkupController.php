<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Resources\DataCollection;
use App\Models\Markup;
use App\Models\MessageFile;
use App\Http\Resources\MarkupResource;
use App\Models\Project;
use App\Http\Requests\MarkupStoreRequest;
use Illuminate\Http\Request;

class MarkupController extends Controller
{
  /**
   * Get a list of markups for a given message file
   * 
   * @return \Illuminate\Http\Response
   */
  public function get(MessageFile $messageFile)
  {
    // $project = Project::where('id', $messageFile->message->project_id)->first();
    // $this->authorize('containsProject', $project);

    $markups = Markup::where('message_file_id', $messageFile->id)->get();
    return response()->json([
      'data' => MarkupResource::collection($markups),
    ]);
  }

  /**
   * Creates a new markup
   *
   * @param  \Illuminate\Http\Request $request
   * @return \Illuminate\Http\Response
   */
  public function create(Request $request)
  {
    $markup = Markup::create([
      'uuid' => \Str::uuid(),
      'shape' => json_encode(
        $request->input('element')
      ),
      'shape_uuid' => $request->input('element')['name'],
      'user_id' => auth()->user()->id,
      'message_file_id' => MessageFile::where('uuid', $request->input('uuid'))->first()->id
    ]);

    return response()->json([
      'markup' => MarkupResource::make($markup),
    ]);
  }

  /**
   * Updates a markup
   * 
   * @param Markup $markup
   * @param  \Illuminate\Http\Request $request
   * @return \Illuminate\Http\Response
   */
  
  public function update(Markup $markup, Request $request)
  {
    $markup->shape = json_encode($request->input('element'));
    $markup->save();
    return response()->json([
      'markup' => MarkupResource::make($markup),
    ]);
  }

  /**
   * Delete a markup
   * 
   * @param  \Illuminate\Http\Request $request
   * @return \Illuminate\Http\Response
   */
  public function delete($shapeId = NULL)
  {
    $markup = Markup::where('shape_uuid', $shapeId)->first();

    if ($markup->user_id !== auth()->user()->id)
    {
      return response()->json([
        'message' => 'You are not allowed to delete this markup',
      ], 403);
    }

    $markup->delete();
    return response()->json([
      'message' => 'Markup deleted',
    ]);
  }

  /** 
   * Save a comment
   * 
   * @param  \Illuminate\Http\Request $request
   * @return \Illuminate\Http\Response
   */
  public function comment(Request $request)
  {
    $markup = Markup::where('shape_uuid', $request->input('shape_uuid'))->first();

    if ($markup->user_id !== auth()->user()->id)
    {
      return response()->json([
        'message' => 'You are not allowed to comment on this markup',
      ], 403);
    }

    $markup->comment = $request->input('comment');
    $markup->save();

    return response()->json([
      'comment' => MarkupResource::make($markup),
    ]);
  }
}
