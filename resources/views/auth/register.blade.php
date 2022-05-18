@extends('layout.guest')
@section('seo_title', 'Registrierung')
@section('content')
<x-card-auth>
  <h2 class="card-auth__heading">Registrierung</h2>
  @if ($errors->any())
    <div class="alert alert-danger pt-1">
      <ul class="!p-0">
        @foreach ($errors->all() as $error)
          <li style="list-style: none">{{ $error }}</li>
        @endforeach
      </ul>
  </div>
  @endif
  <form method="POST" action="{{ route('register') }}">
    @csrf
    <input type="hidden" name="uuid" value="{{$uuid}}">
    <x-text-field label="Vorname" type="text" name="firstname" autocomplete="false" required="true" value="{{$user->firstname ?? ''}}" />
    <x-text-field label="Name" type="text" name="name" autocomplete="false" required="true" value="{{$user->name ?? ''}}" />
    <x-text-field label="Telefon" type="text" name="phone" autocomplete="false" value="{{$user->phone ?? ''}}" />
    <x-select label="Geschlecht" name="gender_id" :options="['1' => 'Mann', '2' => 'Frau']" required="true" value="{{$user->gender_id ?? ''}}" />
    <x-select label="Sprache" name="language_id" :options="['1' => 'Deutsch', '2' => 'English']" required="true" value="{{$user->language_id ?? ''}}" />
    <x-text-field label="Passwort (min. 8 Zeichen)" type="password" name="password" autocomplete="false" required="true" />
    <x-text-field label="Passwort wiederholen" type="password" name="password_confirmation" autocomplete="false" required="true" />
    <div class="card-auth__buttons">
      <x-button label="Registrierung abschliessen" name="register" type="submit" />
    </div>
  </form>
</x-card-auth>
@endsection