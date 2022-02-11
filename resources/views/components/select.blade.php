<div class="form-group @if ($errors->has($name)) has-error @endif">
  @if($label ?? null)
    <label class="{{ ($required ?? false) ? 'is-required' : '' }}" for="{{ $name }}">
      {{ $label }} {{ ($required ?? false) ? '*' : '' }}
    </label>
  @endif
  <div class="select-wrapper">
    <select
      class="{{ $css ?? '' }}"
      name="{{ $name }}"
      {{ ($required ?? false) ? 'required' : '' }}
    >
    @if ($options)
      <option value="">Bitte wählen...</option>
      @foreach($options as $key => $value)
        <option value="{{$value}}">{{$value}}</option>
      @endforeach
    @endif
    </select>
  </div>
</div>