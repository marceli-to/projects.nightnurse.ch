<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;

class HandleProjectRedirect
{
  /**
   * Handle an incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
   * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
   */
  public function handle(Request $request, Closure $next)
  {
    // if request uri is like '/projects/project/{uuid}/messages' (uuid is a wildcard):
    // 1. extract the uuid
    // 2. get the project by uuid
    // 3. make sure the request uri is like '/projects/project/{uuid}/messages'
    // 4. redirect to '/project/{project->slug}/messages'

    if (preg_match('/^\/projects\/project\/[a-z0-9-]+\/messages$/', $request->getRequestUri()))
    {
      $uuid = explode('/', $request->getRequestUri())[3];
      $project = \App\Models\Project::where('uuid', $uuid)->first();
      return redirect('/project/' . $project->slug . '/messages');
    }

    return $next($request);
  }
}
