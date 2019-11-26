@include('boilerplate::load.tinymce')

@if(!defined('LOAD_TINYMCE_MEDIA'))
    @push('js')
        <script>
            var tinyMediaManager = {
                plugins: $.merge(tinymce.defaultSettings.plugins, ["image"]),
                image_advtab: true,
                file_picker_callback: function (callback, value, meta) {
                    tinymce.activeEditor.windowManager.openUrl({
                        url: '{{ route('mediamanager.mce', [], false) }}?type=' + meta.filetype,
                        title: 'File Manager',
                        width: Math.round(window.innerWidth * 0.8),
                        height: Math.round(window.innerHeight * 0.8),
                        onMessage: function (instance, data) {
                            if (data.mceAction == 'insertMedia') {
                                if (meta.filetype === 'image') {
                                    callback(data.url, {alt: data.name});
                                }

                                if (meta.filetype === 'file') {
                                    callback(data.url, {text: data.name});
                                }
                            }

                            instance.close();
                        }
                    });
                    return false;
                }
            };

            tinymce.defaultSettings = $.extend({}, tinymce.defaultSettings, tinyMediaManager);
        </script>
    @endpush

    @php(define('LOAD_TINYMCE_MEDIA', true))
@endif
