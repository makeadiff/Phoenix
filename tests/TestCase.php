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
    protected $year = 2020; //:TODO: :HARDCODE:
    protected $auth = ['username' => "sulu.simulation@makeadiff.in", 'password' => 'pass'];
    protected $headers = [
            // "HTTP_Authorization" => "Basic " . base64_encode("sulu.simulation@makeadiff.in:pass"), // This should be there - but for some reason php thows up an error.
            "PHP_AUTH_USER"      => "sulu.simulation@makeadiff.in",
            "PHP_AUTH_PW"        => "pass",
            "HTTP_Accept"        => "application/json"
    ];

    // This is a mirroring of the DB structure for easier ideal case testing.
    protected $ideal_center_id = 247;
    protected $ideal_project_id= 1;
    protected $ideal_batchs = [
        '3359'	=> [
            'day'			=> 6,
            'class_time'	=> '10:00:00',
            'name'			=> 'Saturday 10:00 AM',
            'center_id'		=> 247,
            'project_id'	=> 1
        ],
        '3360'	=> [
            'day'			=> 0,
            'class_time'	=> '15:00:00',
            'name'			=> 'Sunday 03:00 PM',
            'center_id'		=> 247,
            'project_id'	=> 1
        ],
    ];
    protected $ideal_levels = [
        '10056'	=> [
            'name'	=> 'A',
            'grade'	=> '5',
            'level_name' =>	'5 A',
            'center_id'		=> 247,
            'project_id'	=> 1,
        ],
        '10057'	=> [
            'name'	=> 'A',
            'grade'	=> '6',
            'level_name' =>	'6 A',
            'center_id'		=> 247,
            'project_id'	=> 1,
        ]
    ];
    protected $ideal_batch_level_user_mapping = [
        '3359'	=> [
            '10056'	=> [203356],
            '10057'	=> [203355]
        ],
        '3360'	=> [
            '10057'	=> [203354, 203353]
        ]
    ];

    protected $ideal_user_id = 1;
    protected $ideal_user = [
        'id'	    => 1,
        'name'	    => 'Binny V A',
        'email'	    => 'binnyva@gmail.com',
        'phone'	    => '9746068565',
        'center_id' => 184
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
        if (!$this->client) {
            $this->client = new \GuzzleHttp\Client();
        }
        try {
            $this->response = $this->client->request($method, $full_url, [
                'auth'          => array_values($this->auth),
                'form_params'   => $form_data
            ]);
            $contents = $this->response->getBody()->getContents();
            if ($contents) {
                $this->response_data = json_decode($contents);
            }
        } catch (\GuzzleHttp\Exception\BadResponseException $exception) {
            // If we get a 404, it makes the response null. This fixes it.
            $this->response = $exception->getResponse();
            $contents = $this->response->getBody()->getContents();
            if ($contents) {
                $this->response_data = json_decode($contents);
            }
        }

        return $this->response;
    }

    public function graphql($query)
    {
        // Initilization
        $this->response = null;
        $this->response_data = null;

        $full_url = $this->baseUrl . "/graphql";
        if (!$this->client) {
            $this->client = new \GuzzleHttp\Client();
        }
        try {
            $this->response = $this->client->request("post", $full_url, [
                'form_params'   => ['query' => $query]
            ]);
            $contents = $this->response->getBody()->getContents();
            if ($contents) {
                $this->response_data = json_decode($contents);
            }
        } catch (\GuzzleHttp\Exception\BadResponseException $exception) {
            // If we get a 404, it makes the response null. This fixes it.
            $this->response = $exception->getResponse();
            $contents = $this->response->getBody()->getContents();
            if ($contents) {
                $this->response_data = json_decode($contents);
            }
        }

        return $this->response;
    }
}
