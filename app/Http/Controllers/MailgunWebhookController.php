<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MailgunWebhookController extends Controller
{
  public function handle(Request $request)
  {
    // Prefer JSON body; fall back to form fields
    $payload   = $request->all();
    $eventData = data_get($payload, 'event-data', $payload); // if not present, use flat payload

    $event       = data_get($eventData, 'event') ?? data_get($payload, 'event');
    $severity    = data_get($eventData, 'severity'); // 'temporary' | 'permanent'
    $reason      = data_get($eventData, 'reason');   // 'suppress-bounce', 'bounce', 'generic', etc.
    $recipient   = data_get($eventData, 'recipient') ?? data_get($payload, 'recipient');
    $messageId   = data_get($eventData, 'message.headers.message-id') ?? data_get($eventData, 'message-id');
    $deliveryMsg = data_get($eventData, 'delivery-status.message');
    $code        = data_get($eventData, 'delivery-status.code');
    $smtpResp    = data_get($eventData, 'delivery-status.description') ?? data_get($eventData, 'delivery-status.smtp-response');

    // Treat “blocked” broadly: failed, rejected, or dropped due to suppression
    $isBlockedLike = in_array($event, ['failed','rejected','complained','unsubscribed'], true);

    if ($isBlockedLike) {
      // Example: persist to DB, notify, or mark recipient as undeliverable
      // (Implement your own service/repo as needed)

      // Send raw email to recipient
      Mail::raw($deliveryMsg, function ($message) {
        $message->to(env('MAIL_TO'));
      });

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
      ]);
    }

    return response()->json(['ok' => true]);
  }
}
