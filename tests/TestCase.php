<?php
namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use WithoutMiddleware;

    protected $baseUrl = 'http://localhost/MAD/api/v1';
    protected $only_priority_tests = false;
    protected $write_to_db = true;
    protected $url_prefix = '/v1';
    protected $response;
    protected $response_data;
    protected $year = 2019; //:TODO: :HARDCODE:
    protected $call_headers = [
            // "HTTP_Authorization" => "Basic " . base64_encode("sulu.simulation@makeadiff.in:pass"), // This should be there - but for some reason php thows up an error.
            "PHP_AUTH_USER"      => "sulu.simulation@makeadiff.in",
            "PHP_AUTH_PW"        => "pass",
            "HTTP_Accept"        => "application/json"
    ];

    public function load($url, $method = 'GET', $form_data = [])
    {
        // print $this->baseUrl . $this->url_prefix . $url; exit;
        $this->withoutMiddleware();
        try {
            $this->response = $this->call($method, $this->url_prefix . $url, $form_data, [], [], $this->call_headers);
            print_r($this->response->getContent());
        } catch (Exception $e) {
            print "Error: " . $e->getMessage();
        }

        
        // $this->response_data = json_decode($this->response->getContent());
        // return $this->response;
    }
}
