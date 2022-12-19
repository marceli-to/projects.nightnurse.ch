@component('mail::message')
@include('notifications.partials.logo')
<div class="text-md font-bold pt-20">Einladung Project Room – Nightnurse</div>
<div class="pt-20 font-sm">Um Ihre Registrierung abzuschliessen, klicken Sie bitte auf folgenden Link:</div>
<div class="pt-20 font-sm"><a href="{{url('')}}/register/{{$user['uuid']}}" class="button button-primary font-sm text-white">Abschliessen</a></div>
<div class="pt-20 pb-20 font-sm">Vielen Dank und bis bald!</div>
@endcomponent