<?php namespace Skovachev\Lacore;

use Str;
use URL;
use Auth;
use View;
use Request;

abstract class Controller extends \Illuminate\Routing\Controllers\Controller {

    protected $data = array();
    protected $view = null;
    protected $module = null;

    protected $title = 'My Website';
    protected $description = null;
    protected $keywords = array();

    protected function getCurrentUser()
    {
        return Auth::check() ? Auth::user() : null;
    }

    protected function setMetaDescription($description)
    {
        $this->layout['meta_description'] = $description;
    }

    protected function addMetaKeywords($keywords)
    {
        $present_keywords = is_null($this->layout['meta_keywords']) ? array() : explode(',', $this->layout['meta_keywords']);
        $this->layout['meta_keywords'] = implode(',', array_merge($present_keywords, $keywords));
    }

    protected function addMetaKeywordsFromText($ext)
    {
        $slug = Str::slug($string, '-');
        return $this->addMetaKeywords(explode('-', $slug));
    }

    /**
     * Setup the layout used by the controller.
     *
     * @return void
     */
    protected function setupLayout()
    {
        if ( ! is_null($this->layout))
        {
            // setup user
            $user = $this->getCurrentUser();
            $this->data['user'] = is_null($user) ? false : $user;

            $this->data['meta_keywords'] = null;
            $this->data['meta_description'] = null;
            $this->data['title'] = $this->title;

            // setup page
            $url_segments = explode('/', str_replace(Request::root() . '/', '', URL::current()));
            $current_page = array_shift($url_segments);
            $this->data['page'] = empty($current_page) ? 'none' : $current_page;

            $this->layout = View::make($this->layout)->with($this->data);
        }
    }

    /**
     * Overrides method in Controller
     */
    protected function processResponse($router, $method, $response)
    {
        $request = $router->getRequest();

        if (! is_null($this->view))
        {
            if (!is_null($this->module))
            {
                $this->view = $this->module . '::' . $this->view;
            }
            $response->content = View::make($this->view)->with($this->data);
            $response->with($this->data);
        }

        // The after filters give the developers one last chance to do any last minute
        // processing on the response. The response has already been converted to a
        // full Response object and will also be handed off to the after filters.
        $response = $router->prepare($response, $request);

        $this->callAfterFilters($router, $method, $response);

        return $response;
    }

}