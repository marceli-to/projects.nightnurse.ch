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

    $event       = data_get($eventData, 'event') ?? data_get($payload, 'event') ?? 'unknown';
    $severity    = data_get($eventData, 'severity') ?? 'unknown'; // 'temporary' | 'permanent'
    $reason      = data_get($eventData, 'reason') ?? 'unknown';   // 'suppress-bounce', 'bounce', 'generic', etc.
    $recipient   = data_get($eventData, 'recipient') ?? data_get($payload, 'recipient') ?? 'unknown';
    $messageId   = data_get($eventData, 'message.headers.message-id') ?? data_get($eventData, 'message-id') ?? 'unknown';
    $deliveryMsg = data_get($eventData, 'delivery-status.message') ?? 'No message';
    $code        = data_get($eventData, 'delivery-status.code') ?? 'unknown';
    $smtpResp    = data_get($eventData, 'delivery-status.description') ?? data_get($eventData, 'delivery-status.smtp-response') ?? 'unknown';

    // Debug log the raw payload
    Log::info('Mailgun webhook payload', ['payload' => $payload]);

    // Treat “blocked” broadly: failed, rejected, or dropped due to suppression
    $isBlockedLike = in_array($event, ['failed','rejected','complained','unsubscribed'], true);

    if ($isBlockedLike) {
      // Example: persist to DB, notify, or mark recipient as undeliverable
      // (Implement your own service/repo as needed)

      // Send raw email to recipient
      $emailContent = "Mailgun Webhook Event:\n\n" .
        "Event: {$event}\n" .
        "Severity: {$severity}\n" .
        "Reason: {$reason}\n" .
        "Recipient: {$recipient}\n" .
        "Code: {$code}\n" .
        "Message: {$deliveryMsg}\n" .
        "SMTP Response: {$smtpResp}\n" .
        "Message ID: {$messageId}";

      Mail::raw($emailContent, function ($message) use ($event) {
        $message->to(env('MAIL_TO'))->subject('Mailgun Webhook: ' . ucfirst($event));
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
