@extends('layout.guest')
@section('seo_title', __('Passwort vergessen'))
@section('content')
<x-card-auth>
  <h2 class="card-auth__heading">{{ __('Passwort vergessen') }}</h2>
  <p class="text-sm text-dark">{{__('messages.password_recovery')}}</p>

  @if ($errors->any())
    <div class="alert alert-danger mb-2">
      <ul class="!p-0 !m-0">
        @foreach ($errors->all() as $error)
          <li style="list-style: none" class="!m-0 leading-5">{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif
  @if (session('status'))
    <x-alert type="success" message="{{ session('status') }}" />
  @endif
  <form method="POST" action="{{ route('password.email') }}">
    @csrf
    <x-text-field label="{{ __('E-Mail') }}" type="email" name="email" />
    <div class="card-auth__buttons">
      <x-button label="{{ __('Senden') }}" name="register" type="submit" />
      <a href="{{ route('login') }}" class="form-helper">{{ __('Zurück') }}</a>
    </div>
  </form>
</x-card-auth>
@endsection