<?php

namespace RodrigoButta\LaravelGoogleAnalytics\Exceptions;

class UndefinedViewIdException extends \Exception
{
    public $message = 'ViewID is a requirement to retrieve data.';
}
