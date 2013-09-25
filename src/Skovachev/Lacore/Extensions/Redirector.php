<?php namespace Skovachev\Lacore\Extensions;

class Redirector extends \Illuminate\Routing\Redirector
{
    protected function createRedirect($path, $status, $headers)
    {
        $redirect = new RedirectResponse($path, $status, $headers);

        if (isset($this->session))
        {
            $redirect->setSession($this->session);
        }

        $redirect->setRequest($this->generator->getRequest());

        return $redirect;
    }
}