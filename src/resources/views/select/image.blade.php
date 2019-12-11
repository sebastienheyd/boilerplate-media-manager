<button type="button" class="btn btn-default btn-select-media" data-field="{{ $name ?? 'image' }}">Sélectionner une image</button>
<input type="hidden" name="{{ $name ?? 'image' }}" value="{{ $value ?? '' }}" />
<div class="select-image-preview"></div>

@push('js')
    <script>
        $(function() {
            $(document).on('click', '.btn-select-media', function() {
                bootbox.dialog({
                    size: 'large',
                    message: '<iframe src="{{ route('mediamanager.mce', [], false) }}" name="myFrame" id="myFrame" style="width:100%;height:600px;border:none"></iframe>'
                });
            });
        });
    </script>
@endpush()