<?php

namespace Sebastienheyd\BoilerplateMediaManager\Tests\Components;

use Illuminate\Foundation\Application as Laravel;
use Illuminate\Support\Facades\View as ViewFacade;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use Sebastienheyd\BoilerplateMediaManager\Tests\TestCase;

abstract class TestComponent extends TestCase
{
    protected $isLaravelEqualOrGreaterThan7;

    protected function setUp(): void
    {
        parent::setUp();
        $this->isLaravelEqualOrGreaterThan7 = version_compare(Laravel::VERSION, '7.0', '>=');
    }

    /**
     * Render the contents of the given Blade template string.
     *
     * @param  string  $template
     * @param  array  $data
     * @return string
     */
    protected function renderBlade(string $template, array $data = [])
    {
        $this->withoutMix();
        $tempDirectory = sys_get_temp_dir();

        if (! in_array($tempDirectory, ViewFacade::getFinder()->getPaths())) {
            ViewFacade::addLocation(sys_get_temp_dir());
        }

        $tempFileInfo = pathinfo(tempnam($tempDirectory, 'laravel-blade'));
        $tempFile = $tempFileInfo['dirname'].'/'.$tempFileInfo['filename'].'.blade.php';
        file_put_contents($tempFile, $template);

        ViewFacade::share('errors', (new ViewErrorBag())->put('default', new MessageBag([
            'fielderror' => ['Error message'],
        ])));

        return trim(view($tempFileInfo['filename'], $data)->render());
    }
}
