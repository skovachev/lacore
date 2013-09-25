<?php namespace Skovachev\Lacore;
 
abstract class ModuleServiceProvider extends \Illuminate\Support\ServiceProvider {

    // include classnames of sub-service providers
    // register and boot methods will be called with parent ServiceProvider's methods
    protected $serviceProviders = array();
 
    public function boot()
    {
        if ($module = $this->getModule(func_get_args()))
        {
            $this->package('app/' . $module, $module, app_path() . '/modules/' . $module);
        }

        $this->callProviderMethods('boot');
    }
 
    public function register()
    {
        if ($module = $this->getModule(func_get_args()))
        {
            $this->app['config']->package('app/' . $module, app_path() . '/modules/' . $module . '/config');
 
            // Add routes
            $routes = app_path() . '/modules/' . $module . '/routes.php';
            if (file_exists($routes)) require $routes;
        }

        $this->callProviderMethods('register');
    }

    protected function callProviderMethods($method)
    {
        foreach ($this->serviceProviders as &$provider) {
            if (is_string($provider))
            {
                $provider = $this->app->getProviderRepository()->createProvider($this->app, $provider);
            }
            call_user_func(array($provider, $method));
        }
    }
 
    public function getModule($args)
    {
        $module = (isset($args[0]) and is_string($args[0])) ? $args[0] : null;
 
        return $module;
    }
 
}