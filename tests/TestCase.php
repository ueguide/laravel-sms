<?php

namespace TheLHC\SMS\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use TheLHC\SMS\SMSServiceProvider;

class TestCase extends BaseTestCase
{

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        /*
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:'
        ]);
        */
    }

    /**
     * Get package service providers.
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            SMSServiceProvider::class
        ];
    }
    
    protected function getPackageAliases($app)
    {
        return [
            'SMS' => 'TheLHC\SMS\Facades\SMS'
        ];
    }

}
