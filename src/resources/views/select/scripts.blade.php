@if(!defined('MEDIA_SELECT_SCRIPTS'))
    @push('js')
        <script src="{{ mix('/select-media.min.js', '/assets/vendor/boilerplate-media-manager') }}"></script>
    @endpush
    @push('css')
        <link rel="stylesheet" href="{{ mix('/select-media.min.css', '/assets/vendor/boilerplate-media-manager') }}">
    @endpush
    @php(define('MEDIA_SELECT_SCRIPTS', true))
@endif
