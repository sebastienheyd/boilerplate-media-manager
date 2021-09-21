@include('boilerplate-media-manager::scripts')
<!DOCTYPE html>
<html lang="{{ App::getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }}</title>
    @stack('css')
    <link rel="stylesheet" href="{{ mix('/plugins/fontawesome/fontawesome.min.css', '/assets/vendor/boilerplate') }}">
    <link rel="stylesheet" href="{{ mix('/adminlte.min.css', '/assets/vendor/boilerplate') }}">
    <link rel="stylesheet" href="{{ mix('/mediamanager.min.css', '/assets/vendor/boilerplate-media-manager') }}">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:ital,wght@0,300;0,400;0,700;1,400&display=swap" rel="stylesheet">
</head>
<body class="sidebar-mini{{ setting('darkmode', false) && config('boilerplate.theme.darkmode') ? ' dark-mode accent-light' : '' }}">
<div id="disable"></div>
<div class="content-wrapper ml-0">
    <div id="loading">
        <div><span class="fa fa-4x fa-sync-alt fa-spin"></span></div>
    </div>
    <div id="media-content" data-mce="1" data-display="list" data-type="{{ $type }}"
         data-path="{{ (string) $path }}" data-field="{{ $field }}" data-return="{{ $return_type }}"
         data-selected="{{ $selected }}"></div>
</div>
<script src="{{ mix('/bootstrap.min.js', '/assets/vendor/boilerplate') }}"></script>
<script src="{{ mix('/admin-lte.min.js', '/assets/vendor/boilerplate') }}"></script>
<script src="{{ mix('/boilerplate.min.js', '/assets/vendor/boilerplate') }}"></script>
<script>
    $.ajaxSetup({headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}});
    bootbox.setLocale('{{ App::getLocale() }}');
    var session = {
        keepalive: "{{ route('boilerplate.keepalive', null, false) }}",
        expire: {{ time() +  config('session.lifetime') * 60 }},
        lifetime:  {{ config('session.lifetime') * 60 }},
        id: "{{ session()->getId() }}"
    }
</script>
@stack('js')
</body>

