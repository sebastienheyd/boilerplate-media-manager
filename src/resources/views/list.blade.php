<div class="card card-outline card-info">
    <div class="card-header border-bottom-0">
        <div class="btn-group">
            <button href="#" class="btn btn-default delete-checked" disabled>
                <span class="fa fa-trash"></span>
            </button>
            <button href="#" class="btn btn-default cut-checked" disabled>
                <span class="fa fa-cut"></span>
            </button>
            <button href="#" class="btn btn-default btn-paste" style="display:none">
                <span class="fa fa-paste"></span>
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
        @if($display === 'tiles')
        <div class="btn-group float-right mr-2">
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="fa fa-sort"></span> {{ __('boilerplate-media-manager::list.sort') }}
            </button>
            <div class="dropdown-menu dropdown-menu-right">
                @foreach(['name', 'size', 'type', 'date'] as $col)
                <a href="#" class="dropdown-item btn-sort" data-sort="{{ $col }}">
                    {{ __('boilerplate-media-manager::list.' . ($col === 'size' ? 'weight' : $col)) }}
                    @if(isset($sorts[$col]))
                        <span class="fa fa-sort-{{ $sorts[$col]['order'] === 'asc' ? 'up' : 'down' }} ml-1"></span>
                        @if(count($sorts) > 1)<span class="sort-priority">{{ $sorts[$col]['priority'] }}</span>@endif
                    @endif
                </a>
                @endforeach
            </div>
        </div>
        @endif
        <div class="input-group float-right mr-2" style="width:250px">
            <input type="text" class="form-control" id="search-input" placeholder="{{ __('boilerplate-media-manager::list.search') }}">
            <div class="input-group-append">
                <button class="btn btn-default btn-search-clear" type="button" style="display:none">
                    <span class="fa fa-times"></span>
                </button>
            </div>
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
