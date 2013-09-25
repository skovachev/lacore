<?php namespace Skovachev\Lacore\Extensions;

class Validator extends \Illuminate\Validation\Validator {

    public function validateLocation($attribute, $value, $parameters)
    {
        return !is_null($this->getValue('latitude')) and !is_null($this->getValue('longitude'));
    }

    public function validateAlphaSpace($attribute, $value, $parameters)
    {
        return preg_match('/^([a-z\x20])+$/i', $value);
    }

}