<?php

namespace Sebastienheyd\BoilerplateMediaManager\Tests;

use Collective\Html\HtmlServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Sebastienheyd\Boilerplate\BoilerplateServiceProvider;
use Sebastienheyd\BoilerplateMediaManager\ServiceProvider;

abstract class TestCase extends OrchestraTestCase
{
    /**
     * Load package service provider.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            HtmlServiceProvider::class,
            BoilerplateServiceProvider::class,
            ServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app)
    {
        return [
            'Form' => 'Collective\Html\FormFacade',
        ];
    }
}
