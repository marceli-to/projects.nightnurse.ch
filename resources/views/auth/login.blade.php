@extends('layout.web')
@section('seo_title', 'Login')
@section('content')
<section class="auth">
  <div>
    <div class="auth-logo">
      @include('partials.logo')
    </div>
    <div>
      <h2 class="auth-heading">Login</h2>
      @if ($errors->any())
        <x-alert type="danger" message="{{__('messages.general_error')}}" />
      @endif
      <form method="POST" action="{{ route('login') }}">
        @csrf
        <x-text-field label="E-Mail" type="email" name="email" autocomplete="false" />
        <x-text-field label="Passwort" type="password" name="password" autocomplete="false" />
        <div class="auth-buttons">
          <x-button label="Anmelden" name="register" type="submit" />
          @if (Route::has('password.request'))
            <a href="{{ route('password.request') }}" class="auth-helper">Passwort vergessen?</a>
          @endif
        </div>
      </form>
    </div>
  </div>
</section>
@endsection