<?php namespace Skovachev\Lacore\Exceptions;

use Redirect;

class ValidationException extends Exception 
{
    protected $errors = array();

    public function setRedirect($redirect)
    {
        $this->redirect = $redirect;
    }

    public function __construct($message, $errors = array())
    {
        parent::__construct($message);
        $this->errors = $errors;
    }

    public function getErrorResponse()
    {
        $reponse = parent::getErrorResponse();
        if (!empty($this->errors))
        {
            $response->withErrors($this->errors);
        }
        return $response;
    }
}