<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>@if(trim($__env->yieldContent('seo_title')))@yield('seo_title') – {{config('seo.title')}}@else{{config('seo.title')}}@endif</title>
<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
<link rel="manifest" href="/site.webmanifest">
<link rel="mask-icon" href="/safari-pinned-tab.svg" color="#ffffff">
<meta name="msapplication-TileColor" content="#000000">
<meta name="theme-color" content="#000000">
<meta name="csrf-token" content="{{ csrf_token() }}" />
<meta name="format-detection" content="telephone=no">
<link href="{{ mix('css/app.css') }}" rel="stylesheet">
</head>
<body>
<div id="app">
  <app-component />
</div>
<script src="{{ mix('js/app.js') }}" defer></script>
</body>
</html>
