<?php
namespace App\View\Components;
use App\Models\Notification as NotificationModel;
use Illuminate\View\Component;

class Notification extends Component
{

  /**
   * Notification
   *
   * @var Object
   */
  public $notification;

  /**
   * Create a new component instance.
   *
   * @return void
   */
  public function __construct()
  {
    $this->notification = NotificationModel::published()->first();
  }

  /**
   * Get the view / contents that represent the component.
   *
   * @return \Illuminate\View\View|string
   */
  public function render()
  {
    return view('components.notification');
  }
}
