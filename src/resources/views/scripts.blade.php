@include('boilerplate::load.icheck')
@push('js')
    <script>
        var clipboard = {path: '', files: []};
        var locales = {
            deleteConfirm: "{{ __('boilerplate-media-manager::message.delete.confirm') }}",
            deleteSuccess: "{{ __('boilerplate-media-manager::message.delete.success') }}",
            folderName: "{{ __('boilerplate-media-manager::message.folder.name') }}",
            folderSuccess: "{{ __('boilerplate-media-manager::message.folder.success') }}",
            renameTitle: "{{ __('boilerplate-media-manager::message.rename.title') }}",
            renameSuccess: "{{ __('boilerplate-media-manager::message.rename.success') }}",
            uploadSuccess: "{{ __('boilerplate-media-manager::message.upload.success') }}",
            pasteSuccess: "{{ __('boilerplate-media-manager::message.paste.success') }}",
        };
        var routes = {
            ajaxList: "{{ route('mediamanager.ajax.list', [], false) }}",
            ajaxDelete: "{{ route('mediamanager.ajax.delete', [], false) }}",
            ajaxUpload: "{{ route('mediamanager.ajax.upload', [], false) }}",
            ajaxPaste: "{{ route('mediamanager.ajax.paste', [], false) }}",
            newFolder: "{{ route('mediamanager.ajax.new-folder', [], false) }}",
            rename: "{{ route('mediamanager.ajax.rename', [], false) }}",
        }
    </script>
    <script src="{{ mix('/vendor/blueimp-file-upload/jquery.fileupload.min.js', '/assets/vendor/boilerplate-media-manager') }}"></script>
    <script src="{{ mix('/vendor/jquery-lazy/jquery.lazy.plugins.js', '/assets/vendor/boilerplate-media-manager') }}"></script>
    <script src="{{ mix('/mediamanager.min.js', '/assets/vendor/boilerplate-media-manager') }}"></script>
@endpush
