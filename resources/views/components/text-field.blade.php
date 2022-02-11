<div class="mb-4 lg:mb-6 @if ($errors->has($name)) has-error @endif">
  @if($label ?? null)
    <label class="{{ ($required ?? false) ? 'is-required' : 'text-gray-400 text-base font-mono' }}" for="{{ $name }}">
      {{ $label }} {{ ($required ?? false) ? '*' : '' }}
    </label>
  @endif
  <input
    class="{{ $css ?? 'font-mono text-dark text-base mt-0 block w-full px-0 py-2 border-0 border-bottom-theme focus:ring-0 focus:border-theme-dark' }}"
    type="{{ $type ?? 'text' }}"
    name="{{ $name }}"
    placeholder="{{ $placeholder ?? '' }}"
    value="{{ old($name, $value ?? '') }}"
    {{ ($required ?? false) ? 'required' : '' }}

    @if ($autocomplete != 'false')
      autocomplete="off"
    @endif
  >

</div>