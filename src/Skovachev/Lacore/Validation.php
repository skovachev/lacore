<?php namespace Skovachev\Lacore;

use Validator;
use Skovachev\Lacore\Validation\ValidationExtensionProvider;
use Config;

abstract class Validation
{
    protected $rules = array();

    protected $updateRules = array();

    protected $messages = array();

    protected $validationErrorMessage = 'Submitted data was invalid';

    protected function doValidation($data, $rules)
    {
        $validator = Validator::make($data, $rules, $this->messages);
        if ($validator->fails())
        {
            $exceptionClass = Config::get('lacore::validation_exception_class');
            throw new $exceptionClass($this->validationErrorMessage, $validator->messages());
        }
    }

    public function validate($data)
    {
        return $this->doValidation($data, $this->rules);
    }

    public function extendValidation(ValidationExtensionProvider $provider)
    {
        $this->rules = array_merge($this->rules, $provider->provideValidationRules());
        $this->updateRules = array_merge($this->updateRules, $provider->provideValidationRulesForUpdate());
        $this->messages = array_merge($this->messages, $provider->provideValidationMessages());
    }

    public function validateForUpdate($data, $id = null)
    {
        // get item id
        if (is_null($id))
        {
            $id = array_get($data, 'id');
        }

        $hasUpdateRules = !empty($this->updateRules);
        $updateRules = $hasUpdateRules ? $this->updateRules : $this->rules;

        // add id to the update rules if they need it
        if (!is_null($id) && $hasUpdateRules)
        {
            $updateRules = array_map(function($rule) use ($id){
                return str_replace('{id}', $id, $rule);
            }, $updateRules);
        }
        
        return $this->doValidation($data, $updateRules);
    }
}
