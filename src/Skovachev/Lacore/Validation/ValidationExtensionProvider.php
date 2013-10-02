<?php namespace Skovachev\Lacore\Validation;

interface ValidationExtensionProvider
{
    public function provideValidationRules();
    public function provideValidationRulesForUpdate();
    public function provideValidationMessages();
}