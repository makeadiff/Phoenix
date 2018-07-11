<?php

class TestCase extends Laravel\Lumen\Testing\TestCase
{
    protected $only_priority_tests = true;
    protected $write_to_db = true;
    protected $url_prefix = '/v1';
    protected $call_headers = [
            // "HTTP_Authorization" => "Basic " . base64_encode("sulu.simulation@makeadiff.in:pass"), // This should be there - but for some reason php thows up an error.
            "PHP_AUTH_USER"      => "sulu.simulation@makeadiff.in",
            "PHP_AUTH_PW"        => "pass"
    ];

    public function load($url, $method = 'GET', $form_data = []) {
        return $this->call($method, $this->url_prefix . $url, $form_data, [], [], $this->call_headers);
    }

    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return require __DIR__.'/../bootstrap/app.php';
    }
}
