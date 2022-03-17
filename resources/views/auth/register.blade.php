@extends('layout.guest')
@section('seo_title', 'Login')
@section('content')
<x-card-auth>
  <h2 class="card-auth__heading">Registrierung</h2>
  @if ($errors->any())
    <x-alert type="danger" message="{{__('messages.general_error')}}" />
  @endif
  <form method="POST" action="{{ route('login') }}">
    @csrf
    <x-text-field label="E-Mail" type="email" name="email" autocomplete="false" />
    <x-text-field label="Passwort" type="password" name="password" autocomplete="false" />
    <div class="card-auth__buttons">
      <x-button label="Anmelden" name="register" type="submit" />
    </div>
  </form>
</x-card-auth>
@endsection