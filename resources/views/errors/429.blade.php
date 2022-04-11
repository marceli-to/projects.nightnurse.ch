@extends('layout.web')
@section('content')
<div class="card-error">
  <div>
    <div class="card-error__logo">
      @include('partials.logo')
    </div>
    <h2>Fehler 429</h2>
    <p>Der Zugriff auf diese Seite wurde verweigert.</p>
  </div>
</div>
@endsection
