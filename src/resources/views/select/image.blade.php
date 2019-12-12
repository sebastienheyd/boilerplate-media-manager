@php($id = uniqid())
<div class="select-image-wrapper">
    <button type="button" class="btn-select-image" data-field="{{ $id }}"
            data-src="{!! route('mediamanager.mce', ['type' => 'image', 'return_type' => 'image', 'field' => $id], false) !!}">
        @empty($value)
            <span class="fa fa-image fa-3x"></span>
        @else
            <img src="{{ $value }}" />
        @endempty
    </button>
</div>
<input type="hidden" name="{{ $name ?? 'image' }}" value="{{ $value ?? '' }}" data-id="{{ $id }}"/>
@include('boilerplate-media-manager::select.scripts')
