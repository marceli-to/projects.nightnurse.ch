<?php
namespace App\Http\Middleware;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;
use Closure;

class CheckRole
{
  /**
   * Handle the incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Closure  $next
   * @param  string  $role
   * @return mixed
   */
  public function handle($request, Closure $next, ...$roles)
  {
    if (Auth::user()->isAdmin() === FALSE && Auth::user()->isClient() === FALSE)
    {
      return abort(403);
    }

    if (count($roles) === 1 && $roles[0] === 'admin' && !Auth::user()->isAdmin())
    {
      return abort(403);
    }
    return $next($request);
  }
}