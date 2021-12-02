<div class="card card-outline card-info">
    <div id="btn-paste-group" style="display: none">
        <div class="files-selected">
            <span id="nb-files-selected"></span> {{ __('boilerplate-media-manager::message.paste.files') }}
        </div>
        <div class="btn-group">
            <button class="btn btn-primary btn-paste" disabled>{{ __('boilerplate-media-manager::menu.paste') }}</button>
            <button class="btn btn-default btn-paste-cancel">{{ __('boilerplate-media-manager::menu.cancel') }}</button>
        </div>
    </div>
    <div class="card-header border-bottom-0">
        <div class="btn-group">
            <button href="#" class="btn btn-default delete-checked" disabled>
                <span class="fa fa-trash"></span>
            </button>
            <button href="#" class="btn btn-default copy-checked" disabled>
                <span class="fa fa-clipboard"></span>
            </button>
        </div>
        <span href="#" class="btn btn-default fileinput-button">
            <i class="fa fa-upload"></i>
            <span>{{ __('boilerplate-media-manager::menu.upload') }}</span>
            <input id="fileupload" type="file" name="file"  multiple>
        </span>
        <a href="#" class="btn btn-default add-folder">
            <span class="fa fa-folder"></span> {{ __('boilerplate-media-manager::menu.newFolder') }}
        </a>
        <div class="btn-group float-right">
            <a href="#" class="btn btn-{{ $display === 'list' ? 'secondary' : 'default' }} btn-toggle-display" data-display="list">
                <span class="fa fa-th-list"></span>
            </a>
            <a href="#" class="btn btn-{{ $display === 'tiles' ? 'secondary' : 'default' }} btn-toggle-display" data-display="tiles">
                <span class="fa fa-th"></span>
            </a>
        </div>
        <div class="btn-group float-right mr-2">
            <a href="#" class="btn btn-default btn-refresh">
                <span class="fa fa-sync-alt"></span>
            </a>
        </div>
    </div>
    <div class="card-body pt-0">
        <ol id="media-breadcrumb" class="breadcrumb mb-3 py-2">
            <li><a href="{{ route('mediamanager.index', [], false) }}"><i class="fa fa-home"></i></a></li>
            @foreach($breadcrumb->items() as $dir)
                <li><a href="{{ route('mediamanager.index', ['path' => $dir['path']], false) }}">{{ $dir['name'] }}</a></li>
            @endforeach
        </ol>
        <div id="progress" class="progress mb-3" style="display: none">
            <div class="progress-bar"></div>
        </div>
        @if(empty($list) && empty($parent))
            <div class="alert alert-default-secondary">
                {{ __('boilerplate-media-manager::list.nocontent') }}
            </div>
        @else
            @if($display === 'list')
                @include('boilerplate-media-manager::list-table')
            @else
                @include('boilerplate-media-manager::list-tiles')
            @endif
        @endif
    </div>
</div>
