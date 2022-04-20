@extends('layout.web')
@section('content')
<div class="card-error">
  <div>
    <div class="card-error__logo">
      @include('partials.logo')
    </div>
    <h2>Service unavailable 503</h2>
    <p>The site is undergoing maintenance. We'll be back shortly.</p>
  </div>
</div>
@endsection
