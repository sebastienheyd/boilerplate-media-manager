@if(!defined('LOAD_TINYMCE_MEDIA'))
    @include('boilerplate::load.tinymce')
    @include('boilerplate-media-manager::load.mceextend')
    @php(define('LOAD_TINYMCE_MEDIA', true))
@endif
