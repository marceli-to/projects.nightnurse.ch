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
{{$feedback['author']}}<br>{{$feedback['user']['email']}}
</div>
</td>
</tr>
</table>
</td>
</tr>
</table>
</div>
@if ($feedback['rating'])
<div class="pt-25 message-body">
@for ($i = 0; $i < 5; $i++)
<span style="font-size: 20px; color: {{ $i < $feedback['rating'] ? '#FFC107;' : '#dddddd' }}">&#9733;</span>
@endfor
</div>
@endif
@if ($feedback['comment'])
<div class="pt-10 message-body">
{!! $feedback['comment'] !!}
</div>
@endif
<div class="pb-5 text-right text-xs text-slate font-mono">
  <a href="{{url('')}}/project/{{$feedback['project']['slug']}}/{{$feedback['project']['uuid']}}" class="text-xs text-slate font-mono">Open in Browser</a>
</div>
@endcomponent