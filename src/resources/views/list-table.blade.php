<table class="table table-striped table-hover" id="media-list" data-path="{{ $content->path() }}">
    <thead>
    <tr>
        <th style="width:35px"><input type="checkbox" class="icheck check-all"></th>
        <th>{{ __('boilerplate-media-manager::list.name') }}</th>
        <th style="width: 100px">{{ __('boilerplate-media-manager::list.weight') }}</th>
        <th style="width: 80px">{{ __('boilerplate-media-manager::list.type') }}</th>
        <th style="width: 160px">{{ __('boilerplate-media-manager::list.date') }}</th>
        <th style="width: 150px"></th>
    </tr>
    </thead>
    <tbody>
    @if($parent)
        <tr class="level-up">
            <td></td>
            <td>
                <a href="{{ $parent['link'] }}" class="link-folder">
                    <span class="fa fa-level-up fa-lg fa-fw media-icon fa-flip-horizontal" ></span> ..
                </a>
            </td>
            <td>-</td>
            <td>{{ __('boilerplate-media-manager::types.folder') }}</td>
            <td>{{ $parent['time'] }}</td>
            <td></td>
        </tr>
    @endif
    @foreach($list as $item)
        <tr class="media" data-filename="{{ $item['name'] }}">
            <td>
                <input type="checkbox" class="icheck" name="check[]" value="{{ $item['name'] }}">
            </td>
            <td>
                @if($item['isDir'])
                    <a href="{{ $item['link'] }}" class="link-folder">
                        <span class="fa fa-folder-o fa-lg fa-fw media-icon"></span>&nbsp;{{ $item['name'] }}
                    </a>
                @else
                    <a href="{{ $item['url'] }}" class="link-media" data-filename="{{ $item['name'] }}">
                        <span class="fa fa-{{ $item['icon'] }} fa-lg fa-fw media-icon"></span>&nbsp;{{ $item['name'] }}
                    </a>
                @endif
            </td>
            <td>{{ $item['size'] }}</td>
            <td>{{ __('boilerplate-media-manager::types.'.$item['type']) }}</td>
            <td>{{ $item['time'] }}</td>
            <td>
                <div class="btn-group">
                    @if(!$item['isDir'])
                        <a href="{{ $item['url'] }}" class="btn btn-sm btn-default btn-view">
                            <span class="fa fa-eye"></span>
                        </a>
                        <a href="{{ $item['url'] }}" class="btn btn-sm btn-default" download="{{ $item['url'] }}" target="_blank">
                            <span class="fa fa-download"></span>
                        </a>
                    @endif
                    <a href="#" class="btn btn-sm btn-default btn-rename" data-filename="{{ $item['name'] }}">
                        <span class="fa fa-pencil"></span>
                    </a>
                    <a href="#" class="btn btn-sm btn-default btn-delete" data-filename="{{ $item['name'] }}">
                        <span class="fa fa-trash"></span>
                    </a>
                </div>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
