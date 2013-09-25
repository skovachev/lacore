<?php namespace Skovachev\Lacore\Extensions;

class RedirectResponse extends \Illuminate\Http\RedirectResponse
{
    public function withErrorMessage($message)
    {
        $this->with('message', $message)->with('message-status', 'error');
        return $this;
    }

    public function withSuccessMessage($message)
    {
        $this->with('message', $message)->with('message-status', 'success');
        return $this;
    }

    public function withMessage($message)
    {
        $this->with('message', $message);
        return $this;
    }
}