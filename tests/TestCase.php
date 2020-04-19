<?php
namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected $baseUrl = 'http://localhost/MAD/api';
    protected $only_priority_tests = false;
    protected $write_to_db = true;
    protected $url_prefix = '/v1';

    protected $client;
    protected $response;
    protected $response_data;
    protected $year = 2019; //:TODO: :HARDCODE:
    protected $auth = ['username' => "sulu.simulation@makeadiff.in", 'password' => 'pass'];
    protected $headers = [
            // "HTTP_Authorization" => "Basic " . base64_encode("sulu.simulation@makeadiff.in:pass"), // This should be there - but for some reason php thows up an error.
            "PHP_AUTH_USER"      => "sulu.simulation@makeadiff.in",
            "PHP_AUTH_PW"        => "pass",
            "HTTP_Accept"        => "application/json"
    ];

    public function load($url, $method = 'GET', $form_data = [])
    {
        // On April 18, I found all the feature tests are giving 404 erros and not running. I spent the entire day dubgging it without figuring out what's causing. Finally decided to go with another approch(using a HTTP Client within the TestCase::load()). I'm hoping someday well be able to use the native methord
        // $this->withoutMiddleware();
        // $this->response = $this->call($method, $this->url_prefix . $url, $form_data, [], [], $this->call_headers);
        // $this->response_data = json_decode($this->response->getContent());
        // return $this->response;

        // Initilization
        $this->response = null;
        $this->response_data = null;

        $full_url = $this->baseUrl . $this->url_prefix . $url;
        if(!$this->client) $this->client = new \GuzzleHttp\Client();
        try {
            $this->response = $this->client->request($method, $full_url, [
                'auth'          => array_values($this->auth),
                'form_params'   => $form_data
            ]);
            $contents = $this->response->getBody()->getContents();
            if($contents) $this->response_data = json_decode($contents);

        } catch (\GuzzleHttp\Exception\BadResponseException $exception) {
            // If we get a 404, it makes the response null. This fixes it.
            $this->response = $exception->getResponse();
            $contents = $this->response->getBody()->getContents();
            if($contents) $this->response_data = json_decode($contents);
        }

        return $this->response;
    }

    public function graphql($query) 
    {
        // Initilization
        $this->response = null;
        $this->response_data = null;

        $full_url = $this->baseUrl . "/graphql";
        if(!$this->client) $this->client = new \GuzzleHttp\Client();
        try {
            $this->response = $this->client->request("post", $full_url, [
                'form_params'   => ['query' => $query]
            ]);
            $contents = $this->response->getBody()->getContents();
            if($contents) $this->response_data = json_decode($contents);

        } catch (\GuzzleHttp\Exception\BadResponseException $exception) {
            // If we get a 404, it makes the response null. This fixes it.
            $this->response = $exception->getResponse();
            $contents = $this->response->getBody()->getContents();
            if($contents) $this->response_data = json_decode($contents);
        }

        return $this->response;
    }
}
