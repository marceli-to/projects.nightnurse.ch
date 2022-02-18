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

  public function message()
  {
    // $messageUsers = \App\Models\MessageUser::with('user', 'message.project', 'message.files', 'message.sender')->where('processed', '=', 0)->get();
    // $messageUsers = collect($messageUsers)->splice(0, 1);

    // foreach($messageUsers->all() as $m)
    // {
    //   \Mail::to('m@marceli.to')->send(new \App\Mail\Notification($m->message));
    // }
  }

}
