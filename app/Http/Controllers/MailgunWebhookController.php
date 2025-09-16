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
    return response()->json(200);
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

    // Debug log the raw payload
    // Log::info('Mailgun webhook payload', ['payload' => $payload]);

    // Treat “blocked” broadly: failed, rejected, or dropped due to suppression
    // $isBlockedLike = in_array($event, ['failed','rejected','complained','unsubscribed'], true);
    // Set to true for testing
    $isBlockedLike = true;

    if ($isBlockedLike) {

      // Get the Message/Project/User by UUID
      $message = Message::with('project.manager', 'files', 'markupMessage', 'markupFiles', 'sender', 'users')->where('uuid', $messageUuid)->first();

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
      
      Mail::to(env('MAIL_TO'))->send(new DeliveryErrorNotification($message));

      // // Send raw email to recipient
      // $emailContent = "Message could not be delivered:\n\n" .
      //   "Event: {$event}\n" .
      //   "Severity: {$severity}\n" .
      //   "Reason: {$reason}\n" .
      //   "Recipient: {$recipient}\n" .
      //   "Code: {$code}\n" .
      //   "Message: {$deliveryMsg}\n" .
      //   "SMTP Response: {$smtpResp}\n" .
      //   "Message ID: {$messageId}\n" .
      //   "Message UUID: {$messageUuid}";

      // Mail::raw($emailContent, function ($message) use ($event) {
      //   $message->to(env('MAIL_TO'))->subject('Message not delivered');
      // });

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

    return response()->json(200);
  }
}
