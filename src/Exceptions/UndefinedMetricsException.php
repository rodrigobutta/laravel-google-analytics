<?php

namespace RodrigoButta\LaravelGoogleAnalytics\Exceptions;

class UndefinedMetricsException extends \Exception
{
    public $message = 'No metrics specified.';
}
