@extends('layout.guest')
@section('seo_title', 'E-Mail verifizieren')
@section('seo_description', '')
@section('content')
<x-card-auth>
  <h2 class="auth-heading">E-Mail verifizieren</h2>
  @if (session('resent'))
    <x-alert type="success" message="Neuer Bestätigungslink gesendet. Bitte Posteingang prüfen." />
  @endif
  <p class="text-sm text-dark">Bevor Sie weiterfahren können, müssen Sie ihre E-Mail-Adresse bestätigen. Bitte prüfen Sie ihren Posteingang.</p>
  <p class="text-sm text-dark">Falls Sie keine E-Mail erhalten haben, können Sie einen neuen Link anfordern:</p>
  <form method="POST" action="{{ route('verification.resend') }}">
    @csrf
    <div class="card-auth__buttons">
      <x-button label="Anfordern" name="request-link" type="submit" />
    </div>
  </form>
</x-card-auth>
@endsection