@extends('layout.guest')
@section('seo_title', 'Registrierung')
@section('content')
<x-card-auth>
  <h2 class="card-auth__heading">Registrierung abgeschlossen</h2>
  <p>Vielen Dank, die Registrierung ist abgeschlossen.</p>
  <p><a href="{{route('login')}}" class="btn-primary !inline-block">Zum Login</a></p>
</x-card-auth>
@endsection