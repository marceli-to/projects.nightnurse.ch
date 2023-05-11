@extends('layout.guest')
@section('seo_title', __('Passwort vergessen'))
@section('content')
<x-card-auth>
  <h2 class="card-auth__heading">{{ __('Passwort vergessen') }}</h2>
  <p class="text-sm text-dark">{{__('messages.password_recovery')}}</p>
  @if ($errors->any())
    <x-alert type="danger" message="{{__('messages.general_error')}}" />
  @endif
  @if (session('status'))
    <x-alert type="success" message="{{ session('status') }}" />
  @endif
  <form method="POST" action="{{ route('password.email') }}">
    @csrf
    <x-text-field label="E-Mail" type="email" name="email" />
    <div class="card-auth__buttons">
      <x-button label="{{ __('Senden') }}" name="register" type="submit" />
      <a href="{{ route('login') }}" class="form-helper">{{ __('Zurück') }}</a>
    </div>
  </form>
</x-card-auth>
@endsection