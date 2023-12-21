@component('mail::message')
<div class="pb-15 border-bottom">
<table style="width: 100%;">
<tr>
<td style="width: 50%;">
<table style="width: 100%">
<tr>
<td style="width: 50px">
@include('notifications.partials.logo')
</td>
<td>
<div class="text-xs font-mono font-bold">
{{$sender['full_name']}}
@if ($sender['phone'])
  – <a href="tel:{{$sender['phone']}}" class="text-xs font-mono font-bold no-underline text-black" style="color: #000000">{{$sender['phone']}}</a>
@endif
</div>
</td>
</tr>
</table>
</td>
</tr>
</table>
</div>
<div class="pt-25 message-body">
  Neue Markierungen/Kommentare von {{$sender['full_name']}}
</div>
@if ($message['uuid'] && isset($message['files'][0]))
<div class="pt-20 pb-5 text-right text-xs text-slate font-mono">
<a href="{{url('')}}/project/{{$message['project']['slug']}}/{{$message['project']['uuid']}}/markup/{{$message['uuid']}}/{{$message['files'][0]['uuid']}}" class="text-xs text-slate font-mono">Kommentare und Markierungen anzeigen</a>
</div>
</div>
@endif
@endcomponent