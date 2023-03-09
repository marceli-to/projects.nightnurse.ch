<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Resources\DataCollection;
use App\Models\Project;
use App\Models\ProjectQuote;
use App\Http\Requests\ProjectQuoteStoreRequest;
use Illuminate\Http\Request;

class ProjectQuoteController extends Controller
{
  /**
   * Store a newly created project
   *
   * @param  \Illuminate\Http\ProjectStoreRequest $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $projectQuote = ProjectQuote::create([
      'uuid' => \Str::uuid(),
      'description' => $request->input('description'),
      'uri' => $request->input('uri'),
      'project_id' => $request->input('project_id'),
    ]);

    return response()->json(['projectQuoteUuid' => $projectQuote->uuid]);
  }

  /**
   * Remove a project quote
   *
   * @param ProjectQuote $projectQuote
   * @return \Illuminate\Http\Response
   */
  public function destroy(ProjectQuote $projectQuote)
  {
    $projectQuote->delete();
    return response()->json('successfully deleted');
  }
}
