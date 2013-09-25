<?php namespace Skovachev\Lacore;

use Validator;

abstract class Validation
{
    protected $rules = array();

    protected $updateRules = array();

    protected $messages = array();

    protected $validationErrorMessage = 'Submitted data was invalid';

    private function doValidation($data, $rules)
    {
        $validator = Validator::make($data, $rules, $this->messages);
        if ($validator->fails())
        {
            throw new Exceptions\ValidationException($this->validationErrorMessage, $validator->messages());
        }
    }

    public function validate($data)
    {
        return $this->doValidation($data, $this->rules);
    }

    public function validateForUpdate($data)
    {
        return $this->doValidation($data, empty($this->updateRules) ? $this->rules : $this->updateRules);
    }
}
