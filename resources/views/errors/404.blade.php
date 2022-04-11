@extends('layout.web')
@section('content')
<div class="card-error">
  <div>
    <div class="card-error__logo">
      @include('partials.logo')
    </div>
    <h2>Fehler 404</h2>
    <p>Die gewünschte Seite konnte leider nicht gefunden werden.</p>
  </div>
</div>
@endsection