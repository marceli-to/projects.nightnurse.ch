<div class="form-group @if ($errors->has($name)) has-error @endif">
  @if($label ?? null)
    <label class="{{ ($required ?? false) ? 'is-required' : '' }}" for="{{ $name }}">
      {{ $label }} {!! ($required ?? false) ? '<span class="text-xs absolute -top-1 -right-3">*</span>' : '' !!}
    </label>
  @endif
  <input
    class="{{ $css ?? '' }}"
    type="{{ $type ?? 'text' }}"
    name="{{ $name }}"
    placeholder="{{ $placeholder ?? '' }}"
    value="{{ old($name, $value ?? '') }}"
    {{ ($required ?? false) ? 'required' : '' }}
    @if ($disabled)
      disabled="true"
    @endif
    @if ($autocomplete != 'false')
      autocomplete="off"
    @endif
  >
</div>