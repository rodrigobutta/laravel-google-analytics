<?php

namespace RodrigoButta\LaravelGoogleAnalytics\Core;

use RodrigoButta\LaravelGoogleAnalytics\Exceptions\UndefinedConfigKeyException;

class Core
{
    /**
     * @var \Google_Client
     */
    private $client;

    /**
     * Config details.
     *
     * @var array
     */
    protected $config;

    /**
     * Core constructor.
     *
     * @param array $config
     */
    public function __construct()
    {
        $this->config = config('laravel-google-analytics');

        $this->instantiateClient();
    }

    public function getClient()
    {
        return $this->client;
    }

    public function getConfig($key = null)
    {
        if (is_null($key)) {
            return $this->config;
        }

        $keys = explode('.', $key);

        $result = $this->config;

        foreach ($keys as $key) {
            if (!array_key_exists($key, $result)) {
                throw new UndefinedConfigKeyException("Undefined configuration [{$key}].");
            }

            $result = $result[$key];
        }

        return $result;
    }

    private function instantiateClient()
    {
        $this->client = new \Google_Client();

        $this->client->setApplicationName($this->config['applicationName']);

        $this->client->setAuthConfig($this->config['jsonFilePath']);

        $this->client->setScopes($this->config['scopes']);
    }
}
