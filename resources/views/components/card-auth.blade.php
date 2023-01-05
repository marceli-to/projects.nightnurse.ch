<section class="card-auth relative">
  <div class="absolute top-2 right-2 flex justify-end">
    <a href="/language/de" class="text-xs pr-1 no-underline hover:text-highlight text-gray-500">Deutsch</a>
    <a href="/language/en" class="text-xs pl-1 no-underline hover:text-highlight text-gray-500">English</a>
  </div>
  <div>
    <div class="card-auth__logo">
      @include('partials.logo')
    </div>
    <div>
      {{ $slot }}
    </div>
  </div>
</section>