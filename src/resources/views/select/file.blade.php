@php($id = uniqid())
<div class="form-group">
    @if(!empty($label))
    {!! html()->label($label)->for($name ?? 'file') !!}
    @endif
    <div class="input-group">
        <div class="input-group-prepend">
            <button type="button" class="btn-select-file btn btn-secondary" data-field="{{ $id }}" data-src="{!! route('mediamanager.index', ['mce' => true, 'type' => $type ?? 'all', 'return_type' => 'file', 'field' => $id], false) !!}">
                <i class="far fa-folder-open"></i>
            </button>
        </div>
        <input type="text" class="form-control" data-id="text-{{ $id }}" value="{{ preg_replace('/.*\/(.*)\?.*$/', '$1', $value ?? '') }}" placeholder="{{ __('boilerplate-media-manager::select.no_file_selected') }}" style="background: #FFF" disabled>
        <input type="hidden" name="{{ $name ?? 'file' }}" value="{{ $value ?? '' }}" data-id="{{ $id }}" data-action="setMediaFile"/>
        <button class="btn {{ $value ? '' : 'd-none' }}" id="clear-{{ $id }}" style="position:absolute;right:0" data-action="clearMediaFile" type="button"><span class="fa fa-times"></span></button>
    </div>
    {!! $errors->first( $name ?? 'file' ,'<div class="error-bubble"><div>:message</div></div>') !!}
</div>
@include('boilerplate-media-manager::select.scripts')
