@extends('layout.guest')
@section('seo_title', 'Registrierung')
@section('content')
<x-card-auth>
  <h2 class="card-auth__heading">{{ __('Registrierung') }}</h2>
  @if ($errors->any())
    <div class="alert alert-danger pt-1">
      <ul class="!p-0">
        @foreach ($errors->all() as $error)
          <li style="list-style: none">{{ $error }}</li>
        @endforeach
      </ul>
  </div>
  @endif
  <p>{!! __("Diese Anmeldung war für <strong>:email</strong> bestimmt. Sollte das nicht Ihre E-Mail sein oder Sie eine andere E-Mail-Adresse registrieren möchten, schreiben Sie schreiben Sie dem Projektleiter oder eine E-Mail an <a href='mailto:projects@nightnurse.ch'>projects@nightnurse.ch</a>.", ['email' => $user->email]) !!}</p>
  <form method="POST" action="{{ route('register') }}">
    @csrf
    <input type="hidden" name="uuid" value="{{$uuid}}">
    <x-text-field label="{{ __('Vorname') }}" type="text" name="firstname" autocomplete="false" required="true" value="{{$user->firstname ?? ''}}" />
    <x-text-field label="{{ __('Name') }}" type="text" name="name" autocomplete="false" required="true" value="{{$user->name ?? ''}}" />
    <x-text-field label="{{ __('Telefon') }}" type="text" name="phone" autocomplete="false" value="{{ $user->phone ?? '' }}" />
    <x-select label="{{ __('Geschlecht') }}" name="gender_id" :options="['1' => 'Mann', '2' => 'Frau']" required="true" value="{{$user->gender_id ?? ''}}" />
    <x-select label="{{ __('Sprache') }}" name="language_id" :options="['1' => 'Deutsch', '2' => 'English']" required="true" value="{{$user->language_id ?? ''}}" />
    <x-text-field label="{{ __('E-Mail') }}" type="email" name="email" autocomplete="false" disabled="true" value="{{ $user->email }}" />
    <x-text-field label="{{ __('Passwort (min. 8 Zeichen)') }}" type="password" name="password" autocomplete="false" required="true" />
    <x-text-field label="{{ __('Passwort wiederholen') }}" type="password" name="password_confirmation" autocomplete="false" required="true" />
    <div class="card-auth__buttons">
      <x-button label="{{ __('Registrierung abschliessen') }}" name="register" type="submit" />
    </div>
  </form>
</x-card-auth>
@endsection