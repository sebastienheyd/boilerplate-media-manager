<div class="card card-outline card-info">
    <div class="card-header border-bottom-0">
        <a href="#" class="btn btn-default btn-back-to-list">
            <span class="fa fa-arrow-left"></span> {{ __('boilerplate-media-manager::list.back') }}
        </a>
        <span class="ml-2">{{ __('boilerplate-media-manager::list.searchresults', ['term' => e($term)]) }}</span>
    </div>
    <div class="card-body pt-0">
        @if(empty($results))
            <div class="alert alert-default-secondary">
                {{ __('boilerplate-media-manager::list.noresults', ['term' => e($term)]) }}
            </div>
        @else
            <table class="table table-striped table-sm table-hover" id="media-list" data-path="/">
                <thead>
                <tr>
                    <th>{{ __('boilerplate-media-manager::list.name') }}</th>
                    <th style="width: 200px">{{ __('boilerplate-media-manager::list.path') }}</th>
                    <th style="width: 100px">{{ __('boilerplate-media-manager::list.weight') }}</th>
                    <th style="width: 80px">{{ __('boilerplate-media-manager::list.type') }}</th>
                    <th style="width: 160px">{{ __('boilerplate-media-manager::list.date') }}</th>
                    <th style="width: 150px"></th>
                </tr>
                </thead>
                <tbody>
                @foreach($results as $k => $item)
                    <tr class="media" data-filename="{{ $item['name'] }}" data-url="{{ $item['url'] }}">
                        <td>
                            <a href="{{ $item['url'] }}" class="link-media" data-filename="{{ $item['name'] }}">
                            @if($item['type'] === 'image')
                                <img class="lazy mr-2" data-src="{{ $item['thumb'] }}" alt="{{ $item['name'] }}" style="max-width:26px;max-height:26px"> {{ $item['name'] }}
                            @else
                                <span class="far fa-{{ $item['icon'] }} fa-lg fa-fw media-icon"></span>&nbsp;{{ $item['name'] }}
                            @endif
                            </a>
                        </td>
                        <td>
                            <a href="#" class="link-search-path" data-path="{{ $item['dir'] }}">{{ $item['dir'] }}</a>
                        </td>
                        <td>{{ $item['size'] }}</td>
                        <td>{{ __('boilerplate-media-manager::types.'.$item['type']) }}</td>
                        <td>{{ $item['time'] }}</td>
                        <td class="visible-on-hover">
                            <div class="btn-group">
                                <a href="{{ $item['url'] }}" class="btn btn-sm btn-default btn-view">
                                    <span class="fa fa-eye"></span>
                                </a>
                                <a href="{{ $item['url'] }}" class="btn btn-sm btn-default" download target="_blank">
                                    <span class="fa fa-download"></span>
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
