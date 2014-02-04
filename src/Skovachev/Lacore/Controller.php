<?php namespace Skovachev\Lacore;

use Str;
use URL;
use Auth;
use View;
use Request;

abstract class Controller extends \Illuminate\Routing\Controller {

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
            $this->data['module'] = $this->module;

            // setup page
            $url_segments = explode('/', str_replace(Request::root() . '/', '', URL::current()));
            $current_page = array_shift($url_segments);
            $this->data['page'] = empty($current_page) ? 'none' : $current_page;

            $this->layout = View::make($this->layout)->with($this->data);
        }
    }

    public function callAction($method, $parameters)
    {
        $this->setupLayout();

        $response = call_user_func_array(array($this, $method), $parameters);

        // If no response is returned from the controller action and a layout is being
        // used we will assume we want to just return the layout view as any nested
        // views were probably bound on this view during this controller actions.
        if (is_null($response) and ! is_null($this->layout))
        {
            $response = $this->layout;
        }

        if (! is_null($this->view))
        {
            if (!is_null($this->module))
            {
                $this->view = $this->module . '::' . $this->view;
            }
            $response->content = View::make($this->view)->with($this->data);
            $response->with($this->data);
        }

        return $response;
    }

}