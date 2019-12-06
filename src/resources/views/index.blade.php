@extends('boilerplate::layout.index', [
    'title' => __('boilerplate-media-manager::menu.medialibrary'),
    'subtitle' => __('boilerplate-media-manager::menu.medialist'),
    'breadcrumb' => [
        __('boilerplate-media-manager::menu.medialibrary')
    ]
])

@section('content')
    <div id="disable"></div>
    <div id="loading"><div><span class="fa fa-4x fa-refresh fa-spin"></span></div></div>
    <div id="media-content" data-mce="0" data-display="list" data-type="{{ $type }}" data-path="/{{ (string) $path }}"></div>
@endsection

@push('css')
    <link rel="stylesheet" href="{{ mix('/mediamanager.min.css', '/assets/vendor/boilerplate-media-manager') }}">
@endpush

@include('boilerplate-media-manager::scripts')
