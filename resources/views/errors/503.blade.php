@extends('layout.web')
@section('content')
<div class="card-error">
  <div>
    <div class="card-error__logo">
      @include('partials.logo')
    </div>
    <h2>Fehler 503</h2>
    <p>Server error.</p>
  </div>
</div>
@endsection
