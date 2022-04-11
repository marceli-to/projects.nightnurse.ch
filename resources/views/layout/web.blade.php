@include('partials.header')
<main role="main">
  <section class="min-h-screen flex flex-col justify-center items-center pt-12 sm:pt-0 bg-light">
    @yield('content')
  </section>
</main>
@include('partials.footer')