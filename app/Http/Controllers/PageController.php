<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;

class PageController extends Controller
{
  protected $viewPath = 'pages.';

  /**
   * Show the homepage
   *
   * @return \Illuminate\Http\Response
   */

  public function index()
  {
    if (auth()->user()) 
    {
      return redirect(RouteServiceProvider::DASHBOARD);
    }
    return redirect(route('login'));
  }
}
