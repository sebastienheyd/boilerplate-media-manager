<div id="media-list" class="media-tiles" data-path="{{ $content->path() }}">
    @if($parent)
        <div class="tile">
            <div class="tile-icon">
                <a href="{{ $parent['link'] }}" class="link-folder">
                    <span class="fa fa-level-up fa-5x fa-fw media-icon fa-flip-horizontal" ></span>
                </a>
            </div>
            <div class="tile-label">
                <label style="justify-content: center">
                    <span class="text-center">..</span>
                </label>
            </div>
        </div>
    @endif
    @foreach($list as $item)
        <div class="tile media">
            <div class="tile-icon">
                @if($item['isDir'])
                    <a href="{{ $item['link'] }}" class="link-folder">
                        <span class="fa fa-folder-o }} fa-5x fa-fw media-icon"></span>
                    </a>
                @else
                    <a href="{{ $item['url'] }}" class="link-media" data-filename="{{ $item['name'] }}">
                        @if($item['type'] === 'image')
                            <img class="lazy" data-src="/storage{{ $content->path().'/thumb_'.$item['name'] }}" alt="{{ $item['name'] }}">
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
                    @endif
                    <a href="#" class="btn btn-sm btn-default btn-rename" data-filename="{{ $item['name'] }}">
                        <span class="fa fa-pencil"></span>
                    </a>
                    <a href="#" class="btn btn-sm btn-default btn-delete" data-filename="{{ $item['name'] }}">
                        <span class="fa fa-trash"></span>
                    </a>
                </div>
            </div>
            <div class="tile-label">
                <label>
                    <input type="checkbox" class="icheck" name="check[]" value="{{ $item['name'] }}">
                    <span>{{ $item['name'] }}</span>
                </label>
            </div>
        </div>
    @endforeach
</div>