<div id="media-list" class="media-tiles" data-path="{{ $content->path() }}">
    @if($parent)
        <div class="tile">
            <div class="tile-icon">
                <a href="{{ $parent['link'] }}" class="link-folder">
                    <span class="fa fa-level-up-alt fa-4x fa-fw media-icon fa-flip-horizontal" ></span>
                </a>
            </div>
            <div class="tile-label">
                <label class="text-center">..</label>
            </div>
        </div>
    @endif
    @foreach($list as $k => $item)
        <div class="tile media" data-filename="{{ $item['name'] }}" data-url="{{ $item['url'] }}">
            <div class="tile-icon">
                @if($item['isDir'])
                    <a href="{{ $item['link'] }}" class="link-folder">
                        <span class="far fa-folder }} fa-4x fa-fw media-icon"></span>
                    </a>
                @else
                    <a href="{{ $item['url'] }}" class="link-media" data-filename="{{ $item['name'] }}">
                        @if($item['type'] === 'image')
                            <img class="lazy" data-src="{{ $item['thumb'] }}" alt="{{ $item['name'] }}">
                        @else
                            <span class="fa fa-{{ $item['icon'] }} fa-5x fa-fw media-icon"></span>
                        @endif
                    </a>
                @endif
            </div>
            <div class="tile-menu">
                <div class="btn-group">
                    @if(!$item['isDir'])
                        <a href="{{ $item['url'] }}" class="btn btn-sm btn-default btn-view">
                            <span class="fa fa-eye"></span>
                        </a>
                        <a href="{{ $item['url'] }}" class="btn btn-sm btn-default" download="{{ $item['url'] }}" target="_blank">
                            <span class="fa fa-download"></span>
                        </a>
                    @endif
                    <a href="#" class="btn btn-sm btn-default btn-rename" data-type="{{ $item['type'] === 'folder' ? 'folder' : 'file' }}" data-filename="{{ $item['name']}}" data-name="{{ $item['filename'] ?? '' }}">
                        <span class="fa fa-pencil-alt"></span>
                    </a>
                    <a href="#" class="btn btn-sm btn-default btn-delete" data-filename="{{ $item['name'] }}">
                        <span class="fa fa-trash"></span>
                    </a>
                </div>
            </div>
            <div class="tile-label">
                <div class="icheck-primary d-inline">
                    <input type="checkbox" name="check[]" value="{{ $item['name'] }}" id="item_{{ $k }}">
                    <label for="item_{{ $k }}">{{ $item['name'] }}</label>
                </div>
            </div>
        </div>
    @endforeach
</div>
