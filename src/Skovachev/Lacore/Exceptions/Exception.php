<?php namespace Skovachev\Lacore\Exceptions;

use Redirect;
use Request;

class Exception extends \Exception 
{
    protected $context;
    protected $redirect;

    public function __construct($message)
    {
        parent::__construct($message);

        $this->redirect = null;
        $this->context = array();
    }

    public function setRedirect($redirect)
    {
        $this->redirect = $redirect;
    }

    public function addToContext($key, $value)
    {
        $this->context[$key] = $value;
    }

    public function getContextInformation()
    {
        return empty($this->context) ? array() : $this->context;
    }

    public function getErrorResponse()
    {
        if (is_null($this->redirect))
        {
            $redirect = Redirect::home();
            
            $referer = Request::header('referer');
            if (!empty($referer))
            {
                $redirect = Redirect::back();
            }
        }
        else
        {
            $redirect = $this->redirect;
        }

        $response = $redirect->withErrorMessage($this->getMessage())->withInput();

        return $response;
    }
}