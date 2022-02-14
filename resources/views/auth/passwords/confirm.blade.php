@extends('layouts.auth')
@section('content')
<x-card-auth>
  <h2 class="card-auth__heading">Passwort bestätigen</h2>
  <p>{{ __('Please confirm your password before continuing.') }}</p>
  @if ($errors->any())
    <x-alert type="danger" message="{{__('messages.general_error')}}" />
  @endif
  <form method="POST" action="{{ route('password.confirm') }}">
    @csrf
    <x-text-field label="Passwort" type="password" name="password" autocomplete="false" />
    <div class="card-auth__buttons">
      <x-button label="{{ __('Confirm Password') }}" name="register" type="submit" />
      @if (Route::has('password.request'))
        <a href="{{ route('password.request') }}" class="form-helper">Passwort vergessen?</a>
      @endif
    </div>
  </form>
</x-card-auth>
@endsection
