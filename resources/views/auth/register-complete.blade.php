@extends('layout.guest')
@section('seo_title', __('Registrierung'))
@section('content')
<x-card-auth>
  <h2 class="card-auth__heading ">{{ __('Registrierung abgeschlossen') }}</h2>
  <p class="md:!mb-10">{{ __('Vielen Dank, die Registrierung ist abgeschlossen.') }}</p>
  <p><a href="{{route('login')}}" class="btn-primary !inline-block">{{ __('Zum Login') }}</a></p>
</x-card-auth>
@endsection