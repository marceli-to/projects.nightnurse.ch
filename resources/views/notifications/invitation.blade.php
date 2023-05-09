@component('mail::message')
@include('notifications.partials.logo')
<div class="text-md font-bold pt-20">{{ __('Einladung Project Room – Nightnurse Images', [], $user->language->acronym) }}</div>
<div class="pt-20 font-sm">{{ __('Um Ihre Registrierung abzuschliessen, klicken Sie bitte auf folgenden Link:', [], $user->language->acronym) }}</div>
<div class="pt-20 font-sm"><a href="{{url('')}}/register/{{$user['uuid']}}" class="button button-primary font-sm text-white">{{ __('Abschliessen', [], $user->language->acronym) }}</a></div>
<div class="pt-20 pb-20 font-sm">{{ __('Vielen Dank und bis bald!', [], $user->language->acronym) }}</div>
@endcomponent