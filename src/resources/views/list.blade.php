<div class="box box-primary">
    <div class="box-header">
        <div class="btn-group">
            <a href="#" class="btn btn-default delete-checked" disabled>
                <span class="fa fa-trash"></span>
            </a>
        </div>
        <span class="btn btn-default fileinput-button">
            <span class="fa fa-upload"></span>
            <span>{{ __('boilerplate-media-manager::menu.upload') }}</span>
            <input id="fileupload" type="file" name="file" multiple>
        </span>
        <a href="#" class="btn btn-default add-folder">
            <span class="fa fa-folder"></span> {{ __('boilerplate-media-manager::menu.newFolder') }}
        </a>

        <div class="btn-group pull-right">
            <a href="#" class="btn btn-{{ $display === 'list' ? 'secondary' : 'default' }} btn-toggle-display" data-display="list">
                <span class="fa fa-th-list"></span>
            </a>
            <a href="#" class="btn btn-{{ $display === 'tiles' ? 'secondary' : 'default' }} btn-toggle-display" data-display="tiles">
                <span class="fa fa-th"></span>
            </a>
        </div>

        <div class="btn-group pull-right mrs">
            <a href="#" class="btn btn-default btn-refresh">
                <span class="fa fa-refresh"></span>
            </a>
        </div>
    </div>
    <div class="box-body">
        <ol class="breadcrumb" style="margin-bottom: 10px;" id="media-breadcrumb">
            <li><a href="{{ route('mediamanager.'.($mce ? 'mce' : 'index'), [], false) }}"><i class="fa fa-home"></i></a></li>
            @foreach($content->breadcrumb() as $dir)
                <li><a href="{{ route('mediamanager.'.($mce ? 'mce' : 'index'), ['path' => $dir['path']], false) }}">{{ $dir['name'] }}</a></li>
            @endforeach
        </ol>
        <div id="progress" class="progress" style="display:none;">
            <div class="progress-bar"></div>
        </div>
        @if(empty($list) && empty($parent))
            <div class="alert alert-info">
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