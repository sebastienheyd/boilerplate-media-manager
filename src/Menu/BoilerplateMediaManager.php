<?php

namespace Sebastienheyd\BoilerplateMediaManager\Menu;

use Sebastienheyd\Boilerplate\Menu\Builder;

class BoilerplateMediaManager
{
    public function make(Builder $menu)
    {
        $menu->add(/** @scrutinizer ignore-type */ __('boilerplate-media-manager::menu.medialibrary'), [
                'permission' => 'media_manager',
                'icon'       => 'picture-o',
                'route'      => 'mediamanager.index', ])
            ->activeIfRoute('mediamanager.*')
            ->order(500);
    }
}
