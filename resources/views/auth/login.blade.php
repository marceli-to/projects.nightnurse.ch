@extends('layout.guest')
@section('seo_title', 'Login')
@section('content')
<x-card-auth>
  <h2 class="card-auth__heading">Login</h2>
  @if ($errors->any())
    <x-alert type="danger" message="{{__('messages.general_error')}}" />
  @endif
  <x-notification />
  <form method="POST" action="{{ route('login') }}">
    @csrf
    <x-text-field label="E-Mail" type="email" name="email" autocomplete="false" />
    <x-text-field label="Passwort" type="password" name="password" autocomplete="false" />
    <div class="card-auth__buttons">
      <x-button label="Anmelden" name="register" type="submit" />
      @if (Route::has('password.request'))
        <a href="{{ route('password.request') }}" class="form-helper">Passwort vergessen?</a>
      @endif
    </div>
  </form>
</x-card-auth>
@endsection