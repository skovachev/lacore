<?php namespace Skovachev\Lacore\Extensions;

use Illuminate\Support\Facades\Config;

class RedirectResponse extends \Illuminate\Http\RedirectResponse
{
    public function withErrorMessage($message)
    {
        $this->with('message', $message)->with('message-status', Config::get('error_status'));
        return $this;
    }

    public function withSuccessMessage($message)
    {
        $this->with('message', $message)->with('message-status', Config::get('success_status'));
        return $this;
    }

    public function withMessage($message, $status = null)
    {
        $this->with('message', $message);
        if (!is_null($status))
        {
            $this->with('message-status', $status);
        }
        return $this;
    }
}