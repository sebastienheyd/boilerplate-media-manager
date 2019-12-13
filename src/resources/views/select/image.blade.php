@php($id = uniqid())
<div class="select-image-wrapper {{ empty($value) ? '' : 'editable' }}" style="width:{{ $width ?? 300 }}px;height:{{ $height ?? 200 }}px">
    <button type="button" style="max-width:{{ $width ?? 300 }}px;height:{{ $height ?? 200 }}px" class="btn-select-image" data-field="{{ $id }}"
            data-src="{!! route('mediamanager.mce', ['type' => 'image', 'return_type' => 'image', 'field' => $id], false) !!}">
        @empty($value)
            <span class="fa fa-image fa-3x"></span>
        @else
            <img src="{{ $value }}" />
        @endempty
    </button>
    <div class="select-image-menu">
        <button class="btn select-image-view"><span class="fa fa-eye"></span></button>
        <button class="btn select-image-edit"><span class="fa fa-pencil"></span></button>
        <button class="btn select-image-delete"><span class="fa fa-times"></span></button>
    </div>
    <input type="hidden" name="{{ $name ?? 'image' }}" value="{{ $value ?? '' }}" data-id="{{ $id }}"/>
</div>
{!! $errors->first( $name ?? 'image' ,'<p class="text-danger"><strong>:message</strong></p>') !!}
@include('boilerplate-media-manager::select.scripts')
