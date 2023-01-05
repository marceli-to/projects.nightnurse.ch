<div class="form-group @if ($errors->has($name)) has-error @endif">
  @if($label ?? null)
    <label class="{{ ($required ?? false) ? 'is-required' : '' }}" for="{{ $name }}">
      {{ $label }} {!! ($required ?? false) ? '<span class="text-xs absolute -top-1 -right-3">*</span>' : '' !!}
    </label>
  @endif
  <div class="select-wrapper">
    <select
      class="{{ $css ?? '' }}"
      name="{{ $name }}"
      {{ ($required ?? false) ? 'required' : '' }}
    >
    @if ($options)
      <option value="">{{ __('Bitte wählen...') }}</option>
      @foreach($options as $key => $val)
        @if (isset($value) && $key == $value)
          <option value="{{$key}}" selected>{{$val}}</option>
        @else
          <option value="{{$key}}">{{$val}}</option>
        @endif
      @endforeach
    @endif
    </select>
  </div>
</div>