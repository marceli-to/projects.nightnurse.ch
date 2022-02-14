@include('partials.header')
<main role="main">
  <div class="min-h-screen flex flex-col justify-center items-center pt-12 sm:pt-0 bg-light">
    @yield('content')
  </div>
</main>
@include('partials.footer')