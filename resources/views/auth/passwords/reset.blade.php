@extends('layout.guest')
@section('seo_title', 'Passwort zurücksetzen')
@section('content')
<x-card-auth>
  <h2 class="card-auth__heading">Passwort zurücksetzen</h2>
  @if ($errors->any())
    <x-alert type="danger" message="{{__('messages.general_error')}}" />
  @endif
  @if (session('status'))
    <x-alert type="success" message="{{ session('status') }}" />
  @endif
  <form method="POST" action="{{ route('password.update') }}">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">
    <x-text-field label="E-Mail" type="email" required name="email" />
    <x-text-field label="Passwort" type="password" required name="password" />
    <x-text-field label="Passwort bestätigen" type="password" name="password_confirmation" required autocomplete="new-password" />
    <div class="card-auth__buttons">
      <x-button label="Zurücksetzen" name="reset_password" type="submit" />
    </div>
  </form>
</x-card-auth>
@endsection

