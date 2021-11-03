<table class="table table-striped table-sm table-hover" id="media-list" data-path="{{ $content->path() }}">
    <thead>
    <tr>
        <th style="width:35px">
            <div class="icheck-primary">
                <input type="checkbox" class="check-all" id="check-all">
                <label for="check-all"></label>
            </div>
        </th>
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
                    <span class="fa fa-level-up-alt fa-lg fa-fw media-icon fa-flip-horizontal" ></span> ..
                </a>
            </td>
            <td>-</td>
            <td>{{ __('boilerplate-media-manager::types.folder') }}</td>
            <td>{{ $parent['time'] }}</td>
            <td></td>
        </tr>
    @endif
    @foreach($list as $k => $item)
        <tr class="media" data-filename="{{ $item['name'] }}" data-url="{{ $item['url'] }}">
            <td>
                <div class="icheck-primary">
                    <input type="checkbox" name="check[]" value="{{ $item['name'] }}" id="item_{{ $k }}">
                    <label for="item_{{ $k }}"></label>
                </div>
            </td>
            <td>
                @if($item['isDir'])
                    <a href="{{ $item['link'] }}" class="link-folder">
                        <span class="far fa-folder fa-lg fa-fw media-icon"></span>&nbsp;{{ $item['name'] }}
                    </a>
                @else
                    <a href="{{ $item['url'] }}" class="link-media" data-filename="{{ $item['name'] }}">
                    @if($item['type'] === 'image')
                        <img class="lazy mr-2" data-src="{{ $item['thumb'] }}" alt="{{ $item['name'] }}" style="max-width:26px;max-height:26px"> {{ $item['name'] }}
                    @else
                        <span class="far fa-{{ $item['icon'] }} fa-lg fa-fw media-icon"></span>&nbsp;{{ $item['name'] }}
                    @endif
                    </a>
                @endif
            </td>
            <td>{{ $item['size'] }}</td>
            <td>{{ __('boilerplate-media-manager::types.'.$item['type']) }}</td>
            <td>{{ $item['time'] }}</td>
            <td class="visible-on-hover">
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
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
