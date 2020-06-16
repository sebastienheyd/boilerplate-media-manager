@php($id = uniqid())
<div class="form-group">
    @isset($label)
    {{ Form::label($name ?? 'file', $label) }}
    @endisset
    <div class="input-group">
        <div class="input-group-prepend">
            <button type="button" class="btn-select-file btn btn-secondary" data-field="{{ $id }}" data-src="{!! route('mediamanager.index', ['mce' => true, 'type' => $type ?? 'all', 'return_type' => 'file', 'field' => $id], false) !!}">
                <i class="far fa-folder-open"></i>
            </button>
        </div>
        <input type="text" class="form-control" data-id="text-{{ $id }}" value="{{ preg_replace('/.*\/(.*)\?.*$/', '$1', $value ?? '') }}" placeholder="{{ __('boilerplate-media-manager::select.no_file_selected') }}" style="background: #FFF" disabled>
        <button class="btn {{ $value ? '' : 'd-none' }}" id="clear-{{ $id }}" style="position:absolute;right:0"><span class="fa fa-times"></span></button>
    </div>
    <input type="hidden" name="{{ $name ?? 'file' }}" value="{{ $value ?? '' }}" data-id="{{ $id }}"/>
    {!! $errors->first( $name ?? 'file' ,'<div class="error-bubble"><div>:message</div></div>') !!}
</div>
@include('boilerplate-media-manager::select.scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        $('input[type="hidden"][data-id="{{ $id }}"]').on('change', function() {
            $('#clear-{{ $id }}').addClass('d-none')
            $('input[type="text"][data-id="text-{{ $id }}"]').val('')
            if($(this).val() !== '') {
                $('#clear-{{ $id }}').removeClass('d-none')
                $('input[type="text"][data-id="text-{{ $id }}"]').val($(this).val().replace(/.*\/(.*)\?.*$/, '$1'))
            }
        })

        $('#clear-{{ $id }}').on('click', function(e) {
            e.preventDefault()
            $('input[type="hidden"][data-id="{{ $id }}"]').val('').trigger('change')
        })
    })
</script>
