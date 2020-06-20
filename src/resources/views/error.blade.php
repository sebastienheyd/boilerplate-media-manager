<div class="p-4">
    <div class="alert alert-danger" style="display:flex;align-items:center">
        <span class="fa fa-exclamation-triangle fa-3x" style="margin-right: 10px"></span>
        <span>
            {{ __('boilerplate-media-manager::error.notfound') }}<br />
            <a href="{{ route('mediamanager.index').($query !== '' ? '?'.$query : '') }}" style="color: #FFF">
                {{ __('boilerplate-media-manager::error.back') }}
            </a>
        </span>
    </div>
</div>