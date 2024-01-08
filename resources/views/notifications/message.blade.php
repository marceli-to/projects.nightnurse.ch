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
{{$message['sender']['full_name']}}
@if ($message['sender']['phone'])
 – <a href="tel:{{$message['sender']['phone']}}" class="text-xs font-mono font-bold no-underline text-black" style="color: #000000">{{$message['sender']['phone']}}</a>
@endif
</div>
@if ($message['users'])
<div class="text-xs text-sm font-mono">
An: 
@foreach($message['users'] as $user)
<span class="text-xs font-mono">{{$user['full_name']}}@if (!$loop->last),</span> @endif
@endforeach
</div>
@endif
</td>
</tr>
</table>
</td>
</tr>
</table>
</div>
@if ($message['body'])
<div class="pt-25 message-body">
{!! $message['body'] !!}
</div>
@endif
@if (isset($message['markupMessage']) && $message['markupMessage']['uuid'] && isset($message['markupFiles'][0]))
<div class="pt-20 pb-5 text-right text-xs text-slate font-mono">
<a href="{{url('')}}/project/{{$message['project']['slug']}}/{{$message['project']['uuid']}}/markup/{{$message['markupMessage']['uuid']}}/{{$message['markupFiles'][0]['uuid']}}" class="text-xs text-slate font-mono">Kommentare und Markierungen anzeigen</a>
</div>
</div>
@else
@if ($message['files'])
<div class="pt-20">
@foreach($message['files'] as $file)
@if ($file['preview'])
<table class="border-top" style="width: 100%">
<tr>
  <td class="py-15" style="width: 60px">
    @if ($file['folder'])
      <a href="{{url('')}}/storage/uploads/{{$file['folder']}}/{{$file['name']}}" target="_blank" class="font-mono text-sm">
        <img src="{{url('')}}/img/thumbnail/{{$file['folder']}}/{{$file['name']}}" alt="" title="" height="50" width="50" class="img img-thumbnail img-rounded">
      </a>
    @else
      <a href="{{url('')}}/storage/uploads/{{$file['name']}}" target="_blank" class="font-mono text-sm">
        <img src="{{url('')}}/img/thumbnail/{{$file['name']}}" alt="" title="" height="50" width="50" class="img img-thumbnail img-rounded">
      </a>
    @endif
  </td>
  <td class="py-15" style="vertical-align: middle">
    <span class="text-xs font-mono">
      @if ($file['folder'])
        <a href="{{url('')}}/storage/uploads/{{$file['folder']}}/{{$file['name']}}" target="_blank" class="font-mono text-xs text-dark">{{ $file['original_name'] }}</a>, {{ \ReadableSize($file['size'])}}
      @else
        <a href="{{url('')}}/storage/uploads/{{$file['name']}}" target="_blank" class="font-mono text-xs text-dark">{{ $file['original_name'] }}</a>, {{ \ReadableSize($file['size'])}}
      @endif
    </span>
  </td>
</tr>
</table>
@else
<table class="border-top" style="width: 100%">
<tr>
  <td class="py-10" style="vertical-align: middle">
    <span class="text-xs font-mono">
      @if ($file['folder'])
        <a href="{{url('')}}/storage/uploads/{{$file['folder']}}/{{$file['name']}}" target="_blank" class="font-mono text-xs text-dark">{{ $file['original_name'] }}</a><br>{{ \ReadableSize($file['size'])}}
      @else
        <a href="{{url('')}}/storage/uploads/{{$file['name']}}" target="_blank" class="font-mono text-xs text-dark">{{ $file['original_name'] }}</a><br>{{ \ReadableSize($file['size'])}}
      @endif
    </span>
  </td>
</tr>
</table>
@endif
@endforeach
@if (count($message['files']) > 0)
<div class="border-top py-20 text-xs text-slate font-mono">
  <span class="text-xs font-mono">
    <a href="{{url('')}}/download/zip/{{$message['uuid']}}" target="_blank" class="font-mono text-xs text-dark">Download ZIP</a>
  </span>
</div>
@endif
</div>
@endif
<div class="pb-5 text-right text-xs text-slate font-mono">
  <a href="{{url('')}}/project/{{$message['project']['slug']}}/{{$message['project']['uuid']}}{{$message['private'] ? '/private' : ''}}" class="text-xs text-slate font-mono">Open in Browser</a>
</div>
@endif
@endcomponent