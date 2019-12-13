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
    <link rel="stylesheet" href="{{ mix('/boilerplate.min.css', '/assets/vendor/boilerplate') }}">
    <link rel="stylesheet" href="{{ mix('/mediamanager.min.css', '/assets/vendor/boilerplate-media-manager') }}">
    <style>
        .content-wrapper {
            margin-left: 0
        }
    </style>
    @stack('css')
</head>
<body class="sidebar-mini skin-blue">
<div id="disable"></div>
<div class="content-wrapper">
    <div id="loading">
        <div><span class="fa fa-4x fa-refresh fa-spin"></span></div>
    </div>
    <div id="media-content" data-mce="1" data-display="list" data-type="{{ $type }}"
         data-path="{{ (string) $path }}" data-field="{{ $field }}" data-return="{{ $return_type }}"
         data-selected="{{ $selected }}"></div>
</div>
<script src="{{ mix('/boilerplate.min.js', '/assets/vendor/boilerplate') }}"></script>
<script>
    $(function () {
        $.ajaxSetup({headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}});
        bootbox.setLocale("{{ App::getLocale() }}");
        @if(Session::has('growl'))
        @if(is_array(Session::get('growl')))
        growl("{!! Session::get('growl')[0] !!}", "{{ Session::get('growl')[1] }}");
        @else
        growl("{{Session::get('growl')}}");
        @endif
        @endif
        $('.logout').click(function (e) {
            e.preventDefault();
            if (bootbox.confirm("{{ __('boilerplate::layout.logoutconfirm') }}", function (e) {
                if (e === false) return;
                $('#logout-form').submit();
            })) ;
        });
    });
</script>
@stack('js')
</body>

