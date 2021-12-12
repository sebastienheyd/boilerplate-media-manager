<?php

namespace Sebastienheyd\BoilerplateMediaManager\View\Composers;

use Illuminate\View\View;
use Sebastienheyd\Boilerplate\View\Composers\ComponentComposer;

class ImageComposer extends ComponentComposer
{
    protected $props = [
        'id',
        'name',
        'width',
        'height',
    ];

    public function compose(View $view)
    {
        parent::compose($view);

        $data = $view->getData();

        if (empty($data['id'])) {
            $view->with('id', uniqid('image_'));
        }
    }
}
