<?php
namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    
    protected $baseUrl = 'http://localhost/Experiments/Php/Laravel-latest/';
    protected $only_priority_tests = false;
    protected $write_to_db = true;
    protected $url_prefix = '/v1';
    protected $response;
    protected $call_headers = [
            // "HTTP_Authorization" => "Basic " . base64_encode("sulu.simulation@makeadiff.in:pass"), // This should be there - but for some reason php thows up an error.
            "PHP_AUTH_USER"      => "sulu.simulation@makeadiff.in",
            "PHP_AUTH_PW"        => "pass"
    ];

    public function load($url, $method = 'GET', $form_data = [])
    {
        $this->withoutMiddleware();
        $this->response = $this->call($method, $this->url_prefix . $url, $form_data, [], [], $this->call_headers);
        $this->response_data = json_decode($this->response->getContent());

        return $this->response;
    }
}
