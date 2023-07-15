<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Resources\DataCollection;
use App\Models\Notification;
use App\Http\Requests\NotificationStoreRequest;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
  /**
   * Get a list of notifications
   * 
   * @return \Illuminate\Http\Response
   */
  public function get()
  {
    return new DataCollection(Notification::get());
  }

  /**
   * Find a notification for a given notification
   * 
   * @param Notification $notification
   * @return \Illuminate\Http\Response
   */

   public function findLatest()
   {
     return response()->json(Notification::published()->first());
   }

  /**
   * Find a notification for a given notification
   * 
   * @param Notification $notification
   * @return \Illuminate\Http\Response
   */

  public function find(Notification $notification)
  {
    return response()->json(Notification::findOrFail($notification->id));
  }

  /**
   * Store a newly created notification
   *
   * @param  \Illuminate\Http\NotificationStoreRequest $request
   * @return \Illuminate\Http\Response
   */
  public function store(NotificationStoreRequest $request)
  {
    $notification = Notification::create($request->all());
    return response()->json(['notification' => $notification]);
  }

  /**
   * Update a notification for a given notification
   *
   * @param Notification $notification
   * @param  \Illuminate\Http\NotificationStoreRequest $request
   * @return \Illuminate\Http\Response
   */
  public function update(Notification $notification, NotificationStoreRequest $request)
  {
    $notification->update($request->all());
    return response()->json('successfully updated');
  }

  /**
   * Toggle the status a given notification
   *
   * @param  Notification $notification
   * @return \Illuminate\Http\Response
   */
  public function toggle(Notification $notification)
  {
    $notification->publish = $notification->publish == 0 ? 1 : 0;
    $notification->save();
    return response()->json($notification->publish);
  }

  /**
   * Remove a notification
   *
   * @param Notification $notification
   * @return \Illuminate\Http\Response
   */
  public function destroy(Notification $notification)
  {
    $notification->delete();
    return response()->json('successfully deleted');
  }
}
