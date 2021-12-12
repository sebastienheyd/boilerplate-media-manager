@if(empty($name))
    <code>&lt;x-boilerplate-media-manager::file> The name attribute has not been set</code>
@else
<div class="form-group">
    @if(!empty($label))
    {{ Form::label($name ?? 'file', $label) }}
    @endif
    <div class="input-group">
        <div class="input-group-prepend">
            <button type="button" class="btn-select-file btn btn-secondary" data-field="{{ $id }}" data-src="{!! route('mediamanager.index', ['mce' => true, 'type' => $type ?? 'all', 'return_type' => 'file', 'field' => $id], false) !!}">
                <i class="far fa-folder-open"></i>
            </button>
        </div>
        <input type="text" class="form-control" data-id="text-{{ $id }}" value="{{ preg_replace('/.*\/(.*)\?.*$/', '$1', old($name, $value ?? '')) }}" placeholder="{{ __('boilerplate-media-manager::select.no_file_selected') }}" style="background: transparent" disabled>
        <input type="hidden" name="{{ $name }}" value="{{ old($name, $value ?? '') }}" data-id="{{ $id }}" data-action="setMediaFile"/>
        <button class="btn {{ old($name, $value ?? false)  ? '' : 'd-none' }}" id="clear-{{ $id }}" type="button" data-action="clearMediaFile" style="position:absolute;right:0"><span class="fa fa-times"></span></button>
    </div>
    @error($name)
    <div class="error-bubble"><div>{{ $message }}</div></div>
    @enderror
</div>
@include('boilerplate-media-manager::components.async_scripts')
@endif