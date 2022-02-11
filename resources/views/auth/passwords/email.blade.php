@extends('layout.web')
@section('seo_title', 'Passwort vergessen')
@section('content')
<section class="auth">
  <div>
    <div class="auth-logo">
      @include('partials.logo')
    </div>
    <div>
      <h2 class="auth-heading">Passwort vergessen</h2>
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
        <div class="auth-buttons">
          <x-button label="Senden" name="register" type="submit" />
          <a href="{{ route('login') }}" class="auth-helper">Zurück</a>
        </div>
      </form>
    </div>
  </div>
</section>
@endsection