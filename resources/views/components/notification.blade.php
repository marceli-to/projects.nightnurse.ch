@if ($notification)
  <div class="notification is-inline">
    <div>
      @if ($notification->title)
        <h1>{{ $notification->title }}</h1>
      @endif
      @if ($notification->text)
        <div>{{ $notification->text }}</div>
      @endif
    </div>
  </div>
@endif