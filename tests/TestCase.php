<?php
namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Storage;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected $baseUrl = 'http://localhost/MAD/api';
    protected $only_priority_tests = false;
    protected $write_to_db = true;
    protected $url_prefix = '/v1';

    protected $db;
    protected $client;
    protected $response;
    protected $response_data;
    protected $year = 2022; // :HARDCODE:
    protected $jwt_token = null;
    protected $jtw_token_file = null;

    protected $api_user_login_for_jwt = []; // Filled by constructor
    protected $auth = []; // Filled by constructor
    protected $headers = [
            // "HTTP_Authorization" => "Basic " . base64_encode("USERNAME:PASSWORD"),
            "PHP_AUTH_USER"      => "", // Filled by constructor
            "PHP_AUTH_PW"        => "", // Filled by constructor
            "HTTP_Accept"        => "application/json"
    ];

    // This is a mirroring of the DB structure for easier ideal case testing. This has to be refreshed every year.
    protected $ideal_center_id = 247;
    protected $ideal_project_id= 1;
    protected $ideal_batchs = [
        '3933'	=> [
            'day'			=> 6,
            'class_time'	=> '10:00:00',
            'name'			=> 'Saturday 10:00 AM',
            'center_id'		=> 247,
            'project_id'	=> 1
        ],
        '3934'	=> [
            'day'			=> 0,
            'class_time'	=> '15:00:00',
            'name'			=> 'Sunday 03:00 PM',
            'center_id'		=> 247,
            'project_id'	=> 1
        ],
    ];
    protected $ideal_levels = [
        '11510'	=> [
            'name'	=> 'A',
            'grade'	=> '6',
            'level_name' =>	'6 A',
            'center_id'		=> 247,
            'project_id'	=> 1,
        ],
        '11511'	=> [
            'name'	=> 'A',
            'grade'	=> '7',
            'level_name' =>	'7 A',
            'center_id'		=> 247,
            'project_id'	=> 1,
        ]
    ];
    protected $ideal_batch_level_user_mapping = [
        '3933'	=> [
            '11510'	=> [203355],
            '11511'	=> [203356]
        ],
        '3934'	=> [
            '11511'	=> [203354, 203353]
        ]
    ];
    protected $ideal_users = [
        '203353'    => ['name' => 'Ideal Teacher 1'],
        '203354'    => ['name' => 'Ideal Teacher 2'],
        '203355'    => ['name' => 'Ideal Teacher 3'],
        '203356'    => ['name' => 'Ideal Teacher 4'],
        '203357'    => ['name' => 'Ideal Mentor 1'],
        '203358'    => ['name' => 'Ideal Mentor 3'],
    ];

    protected $ideal_user_id = 1;
    protected $ideal_user = [
        'id'	    => 1,
        'name'	    => 'Binny V A',
        'email'	    => 'binnyva@gmail.com',
        'phone'	    => '9746068565',
        'center_id' => 154
    ];

    // Almost the constructor. These will be called before tests are run.
    protected function setUp():void 
    {   
        parent::setUp();

        // Loading Secrets
        $auth_logins = require(__DIR__ . '/Secrets/Logins.php');
        $this->api_user_login_for_jwt = $auth_logins['api_user_login_for_jwt'];
        $this->auth = $auth_logins['basic_auth'];
        $this->headers['PHP_AUTH_USER'] = $this->auth['username'];
        $this->headers['PHP_AUTH_PW'] = $this->auth['password'];

        // Setting up an HTTP Client
        if (!$this->client) {
            $this->client = new \GuzzleHttp\Client();
        }

        // Getting the JWT Token for calls.
        $this->jtw_token_file = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Secrets/jwt_token.txt';
        if(file_exists($this->jtw_token_file)) {
            $this->jwt_token = file_get_contents($this->jtw_token_file);
        }

        if(!$this->jwt_token) {
            $this->fetchAndSaveJWToken();
        }


        $this->db = app('db');
    }

    public function load($url, $method = 'GET', $form_data = [], $auth = 'jwt')
    {
        // On April 2020, I found all the feature tests are giving 404 erros and not running. I spent the entire day dubgging it without figuring out what's causing. Finally decided to go with another approch(using a HTTP Client within the TestCase::load()). I'm hoping someday well be able to use the native methord
        // $this->withoutMiddleware();
        // $this->response = $this->call($method, $this->url_prefix . $url, $form_data, [], [], $this->headers);
        // $this->response_data = json_decode($this->response->getContent());
        // return $this->response;

        // Initilization
        $this->response = null;
        $this->response_data = null;

        $full_url = $this->baseUrl . $this->url_prefix . $url;
        try {
            $headers = [
                'form_params'   => $form_data
            ];
            if($this->jwt_token and $auth == 'jwt') {
                $headers['headers'] = ['Authorization' => "Bearer {$this->jwt_token}"];
            } else {
                $headers['headers'] = ['Authorization' => "Basic " . base64_encode($this->auth['username'].":".$this->auth['password'])];
            }

            // var_dump([$full_url, $headers]);
            $this->response = $this->client->request($method, $full_url, $headers);
            $contents = $this->response->getBody()->getContents();

            if ($contents) {
                $this->response_data = json_decode($contents);
                // var_dump($this->response_data);
            }
        } catch (\GuzzleHttp\Exception\BadResponseException $exception) {
            // If we get a 40X, it makes the response null. This fixes it.
            $this->response = $exception->getResponse();
            $contents = $this->response->getBody()->getContents();

            if ($contents) {
                $this->response_data = json_decode($contents);
                // print "Response: " . $contents . "\n";
                // var_dump($this->response_data);

                if(is_array($this->response_data->data)) {
                    $error = $this->response_data->data[0];
                    if($error == "Token is Expired" or $error == "Token is Invalid") {
                        if($this->fetchAndSaveJWToken()) {
                            $this->load($url, $method, $form_data); // :TODO: This can cause infinite loop
                        }
                    }
                }
            }
        }

        return $this->response;
    }

    public function fetchAndSaveJWToken()
    {
        try {
            $auth_response = $this->client->request('POST', $this->baseUrl . $this->url_prefix . '/users/login', [
                'auth'          => array_values($this->auth),
                'form_params'   => $this->api_user_login_for_jwt
            ]);
            $auth_contents = $auth_response->getBody()->getContents();
            if ($auth_contents) {
                $auth_contents_data = json_decode($auth_contents, true);
                $this->jwt_token = $auth_contents_data['data']['users']['jwt_token'];

                // Save JWT Token to storage, it will be loaded in constructor
                if($this->jtw_token_file) file_put_contents($this->jtw_token_file, $this->jwt_token);
            }
        } catch (\GuzzleHttp\Exception\BadResponseException $exception) {
            echo $exception->getResponse();
            exit;
        }

        return $this->jwt_token;
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
