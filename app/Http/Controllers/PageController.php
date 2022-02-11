<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PageController extends Controller
{
  protected $viewPath = 'pages.';

  public function index()
  {
    return view($this->viewPath . 'index');
  }

}
