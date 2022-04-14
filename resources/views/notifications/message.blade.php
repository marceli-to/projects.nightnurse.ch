@component('mail::message')
@include('notifications.partials.logo')
<div class="py-20">
<div class="border-top text-xs font-mono pt-10">Von: {{$message['sender']['full_name']}}</div>
@if ($message['users'])
<div class="border-bottom pb-10 pt-5 text-xs text-sm font-mono">
An: 
@foreach($message['users'] as $user)
<span class="text-xs font-mono">{{$user['full_name']}}@if (!$loop->last),</span> @endif
@endforeach
</div>
@endif
</div>
@if ($message['body'])
<div class="pt-20">{!! $message['body'] !!}</div>
@endif
@if ($message['files'])
<div class="pt-20">
@foreach($message['files'] as $file)
@if ($file['preview'])
<table class="border-top" style="width: 100%">
<tr>
  <td class="py-10" style="width: 70px">
    <a href="{{url('')}}/storage/uploads/{{$file['name']}}" target="_blank" class="font-mono text-sm">
      <img src="{{url('')}}/img/thumbnail/{{$file['name']}}" alt="" title="" height="60" width="60" class="img img-thumbnail img-rounded">
    </a>
  </td>
  <td class="py-10" style="vertical-align: middle">
    <span class="text-sm font-mono">
      <a href="{{url('')}}/storage/uploads/{{$file['name']}}" target="_blank" class="font-mono text-sm text-dark">
        {{$file['original_name']}}
      </a>
    </span>
  </td>
</tr>
</table>
@else
<table class="border-top" style="width: 100%">
<tr>
  <td class="py-10" style="vertical-align: middle">
    <span class="text-sm font-mono">
      <a href="{{url('')}}/storage/uploads/{{$file['name']}}" target="_blank" class="font-mono text-sm text-dark">{{$file['original_name']}}</a>
    </span>
  </td>
</tr>
</table>
@endif
@endforeach
</div>
<div class="pt-10 text-center text-sm font-mono">
  <a href="{{url('')}}/projects/project/{{$message['project']['uuid']}}/messages" class="font-mono text-xs text-dark">Nachricht im Browser öffnen</a>
</div>
@endif
@endcomponent