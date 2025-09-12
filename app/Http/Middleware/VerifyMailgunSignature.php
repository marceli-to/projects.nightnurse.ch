<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Exceptions\HttpResponseException;

class VerifyMailgunSignature
{
  public function handle(Request $request, Closure $next)
  {
    // Mailgun v3 JSON uses: signature: { timestamp, token, signature }
    $timestamp = data_get($request->input('signature'), 'timestamp') ?? $request->input('timestamp');
    $token     = data_get($request->input('signature'), 'token')     ?? $request->input('token');
    $signature = data_get($request->input('signature'), 'signature') ?? $request->input('signature');

    if (!$timestamp || !$token || !$signature) {
      throw new HttpResponseException(response()->json(['message' => 'Invalid signature payload'], 400));
    }

    // Optional: reject very old timestamps (replay protection)
    if (abs(time() - (int)$timestamp) > 300) { // 5 minutes
      throw new HttpResponseException(response()->json(['message' => 'Stale timestamp'], 400));
    }

    $signingKey = config('client.mailgun.webhook_signing_key') ?? env('MAILGUN_WEBHOOK_SIGNING_KEY');
    $computed   = hash_hmac('sha256', $timestamp . $token, $signingKey);

    if (!hash_equals($computed, $signature)) {
      throw new HttpResponseException(response()->json(['message' => 'Signature mismatch'], 401));
    }

    return $next($request);
  }
}
