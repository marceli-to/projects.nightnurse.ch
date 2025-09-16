<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Models\Message;
use App\Mail\DeliveryErrorNotification;

class MailgunWebhookController extends Controller
{
  public function handle(Request $request)
  {
    // Prefer JSON body; fall back to form fields
    $payload   = $request->all();
    $eventData = data_get($payload, 'event-data', $payload); // if not present, use flat payload

    $event       = data_get($eventData, 'event') ?? data_get($payload, 'event') ?? 'unknown';
    $severity    = data_get($eventData, 'severity') ?? 'unknown'; // 'temporary' | 'permanent'
    $reason      = data_get($eventData, 'reason') ?? 'unknown';   // 'suppress-bounce', 'bounce', 'generic', etc.
    $recipient   = data_get($eventData, 'recipient') ?? data_get($payload, 'recipient') ?? 'unknown';
    $messageId   = data_get($eventData, 'message.headers.message-id') ?? data_get($eventData, 'message-id') ?? 'unknown';
    $deliveryMsg = data_get($eventData, 'delivery-status.message') ?? 'No message';
    $code        = data_get($eventData, 'delivery-status.code') ?? 'unknown';
    $smtpResp    = data_get($eventData, 'delivery-status.description') ?? data_get($eventData, 'delivery-status.smtp-response') ?? 'unknown';
    $messageUuid = data_get($eventData, 'user-variables.message_uuid');

    // Treat “blocked” broadly: failed, rejected, or dropped due to suppression
    $isBlockedLike = in_array($event, ['failed','rejected','complained','unsubscribed'], true);

    if ($isBlockedLike) {

      // Get the Message/Project/User by UUID
      $message = Message::with('project.manager', 'files', 'markupMessage', 'markupFiles', 'sender', 'users')->where('uuid', $messageUuid)->first();

      if ($message) {
        
        // Update the message
        $message->update([
          'has_delivery_error' => true,
          'delivery_errors' => [
            'event' => $event,
            'severity' => $severity,
            'reason' => $reason,
            'recipient' => $recipient,
            'code' => $code,
            'message' => $deliveryMsg,
            'smtp' => $smtpResp,
            'messageId' => $messageId,
            'messageUuid' => $messageUuid,
          ],
        ]);
        
        Mail::to('m@marceli.to')->send(new DeliveryErrorNotification($message));

        // Log
        Log::warning('Mailgun blocked-like event', [
          'event'     => $event,
          'severity'  => $severity,
          'reason'    => $reason,
          'recipient' => $recipient,
          'code'      => $code,
          'message'   => $deliveryMsg,
          'smtp'      => $smtpResp,
          'messageId' => $messageId,
          'messageUuid' => $messageUuid,
        ]);
      }
    }
    return response()->json(200);
  }
}
