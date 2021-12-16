@if(empty($name))
    <code>&lt;x-boilerplate-media-manager::image> The name attribute has not been set</code>
@else
<div class="form-group{{ isset($groupClass) ? ' '.$groupClass : '' }}"{!! isset($groupId) ? ' id="'.$groupId.'"' : '' !!}>
@isset($label)
    {!! Form::label($name, __($label)) !!}
@endisset
<div class="select-image-wrapper {{ empty(old($name, $value ?? '')) ? '' : 'editable' }}" style="width:{{ $width ?? 300 }}px;height:{{ $height ?? 200 }}px">
    <button type="button" style="max-width:{{ $width ?? 300 }}px;height:{{ $height ?? 200 }}px" class="btn-select-image" data-field="{{ $id }}" data-src="{!! route('mediamanager.index', ['mce' => true, 'type' => 'image', 'return_type' => 'image', 'field' => $id], false) !!}">
@empty(old($name, $value ?? ''))
        <span class="fa fa-image fa-3x"></span>
@else
        <img src="{{ old($name, $value ?? '') }}" />
@endempty
    </button>
    <div class="select-image-menu">
        <button class="btn select-image-view"><span class="fa fa-eye"></span></button>
        <button class="btn select-image-delete"><span class="fa fa-times"></span></button>
    </div>
    <input type="hidden" name="{{ $name }}" value="{{ old($name, $value ?? '') }}" data-id="{{ $id }}"/>
</div>
@if($help ?? false)
    <small class="form-text text-muted">@lang($help)</small>
@endif
@error($name)
    <div class="error-bubble"><div>{{ $message }}</div></div>
@enderror
</div>
@include('boilerplate-media-manager::components.async_scripts')
@endif
