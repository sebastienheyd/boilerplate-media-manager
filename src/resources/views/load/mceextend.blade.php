@push('js')
    <script>
        var tinyMediaManager = {
            image_advtab: true,
            images_upload_handler: function (blobInfo, success, failure) {

                var formData = new FormData();
                formData.append('file', blobInfo.blob(), blobInfo.filename());

                $.ajax({
                    url: '{{ route('mediamanager.ajax.mce-upload') }}',
                    type: 'post',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(json){
                        if (!json || typeof json.location != 'string') {
                            failure('Invalid JSON');
                            return;
                        }
                        success(json.location);
                    }
                });
            },
            file_picker_callback: function (callback, value, meta) {
                tinymce.activeEditor.windowManager.openUrl({
                    url: '{{ route('mediamanager.mce', [], false) }}?type=' + meta.filetype,
                    title: 'File Manager',
                    width: Math.round(window.innerWidth * 0.8),
                    height: Math.round(window.innerHeight * 0.8),
                    onMessage: function (instance, data) {
                        if (data.mceAction === 'insertMedia') {
                            if (meta.filetype === 'image') {
                                callback(data.url, {alt: data.name});
                            }

                            if (meta.filetype === 'file') {
                                callback(data.url, {text: data.name});
                            }

                            if (meta.filetype === 'media') {
                                callback(data.url);
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
