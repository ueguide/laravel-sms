<?php

namespace TheLHC\SMS;

use InvalidArgumentException;

class SMSManager
{
    /**
     * The application instance.
     *
     * @var \Illuminate\Foundation\Application
     */
    protected $app;

    /**
     * The array of resolved sms providers.
     *
     * @var array
     */
    protected $providers = [];

    /**
     * The registered custom driver creators.
     *
     * @var array
     */
    protected $customCreators = [];

    /**
     * Create a new SMS manager instance.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * Get a sms provider instance by name.
     *
     * @param  string|null  $name
     * @return \Illuminate\Contracts\SMS\Repository
     */
    public function provider($name = null)
    {
        $name = $name ?: $this->getDefaultDriver();

        return $this->providers[$name] = $this->get($name);
    }

    /**
     * Get a sms driver instance.
     *
     * @param  string  $driver
     * @return mixed
     */
    public function driver($driver = null)
    {
        return $this->provider($driver);
    }

    /**
     * Attempt to get the provider from the local sms.
     *
     * @param  string  $name
     * @return \Illuminate\Contracts\SMS\Repository
     */
    protected function get($name)
    {
        return $this->providers[$name] ?? $this->resolve($name);
    }

    /**
     * Resolve the given provider.
     *
     * @param  string  $name
     * @return \Illuminate\Contracts\SMS\Repository
     *
     * @throws \InvalidArgumentException
     */
    protected function resolve($name)
    {
        $config = $this->getConfig($name);

        if (is_null($config)) {
            throw new InvalidArgumentException("SMS provider [{$name}] is not defined.");
        }

        if (isset($this->customCreators[$config['driver']])) {
            return $this->callCustomCreator($config);
        } else {
            $driverMethod = 'create'.ucfirst($config['driver']).'Driver';

            if (method_exists($this, $driverMethod)) {
                return $this->{$driverMethod}($config);
            } else {
                throw new InvalidArgumentException("Driver [{$config['driver']}] is not supported.");
            }
        }
    }

    /**
     * Call a custom driver creator.
     *
     * @param  array  $config
     * @return mixed
     */
    protected function callCustomCreator(array $config)
    {
        return $this->customCreators[$config['driver']]($this->app, $config);
    }

    /**
     * Create an instance of the APC sms driver.
     *
     * @param  array  $config
     * @return \Illuminate\SMS\ApcStore
     */
    protected function createSnsDriver(array $config)
    {
        return new SNSProvider($config);
    }

    /**
     * Get the sms connection configuration.
     *
     * @param  string  $name
     * @return array
     */
    protected function getConfig($name)
    {
        return $this->app['config']["sms.providers.{$name}"];
    }

    /**
     * Get the default sms driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return $this->app['config']['sms.default'];
    }

    /**
     * Set the default sms driver name.
     *
     * @param  string  $name
     * @return void
     */
    public function setDefaultDriver($name)
    {
        $this->app['config']['sms.default'] = $name;
    }

    /**
     * Register a custom driver creator Closure.
     *
     * @param  string    $driver
     * @param  \Closure  $callback
     * @return $this
     */
    public function extend($driver, Closure $callback)
    {
        $this->customCreators[$driver] = $callback->bindTo($this, $this);

        return $this;
    }

    /**
     * Dynamically call the default driver instance.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->provider()->$method(...$parameters);
    }
}
