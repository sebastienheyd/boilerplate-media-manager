image_advtab: true,
file_picker_callback: function (callback, value, meta) {
    tinymce.activeEditor.windowManager.openUrl({
        url: '{{ route('mediamanager.index', [], false) }}?mce=1&type=' + meta.filetype + '&selected=' + value,
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