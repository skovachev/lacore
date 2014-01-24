<?php namespace Skovachev\Lacore;

use Illuminate\Support\ServiceProvider;
use Skovachev\Lacore\Extensions\Redirector;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Blade;

class LacoreServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	public function boot()
	{
		Validator::resolver(function($translator, $data, $rules, $messages)
        {
            return new \Skovachev\Lacore\Extensions\Validator($translator, $data, $rules, $messages);
        });

        Blade::extend(function ($view) {
            $html = "if (Session::has('message')){<div id='message' class='alert <?php echo Session::has('message-status') ? 'alert-' . Session::get('message-status') : ''; ?>''><?php echo Session::get('message'); ?></div>}";
            return str_replace("@message", $html, $view);
        });
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app['redirect'] = $this->app->share(function($app)
        {
            $redirector = new Redirector($app['url']);

            // If the session is set on the application instance, we'll inject it into
            // the redirector instance. This allows the redirect responses to allow
            // for the quite convenient "with" methods that flash to the session.
            if (isset($app['session.store']))
            {
                $redirector->setSession($app['session.store']);
            }

            return $redirector;
        });
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('redirect');
	}

}