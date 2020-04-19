<?php
namespace Tests\Feature;

use Tests\TestCase;

/**
 * @runTestsInSeparateProcesses
 */
class SampleTest extends TestCase
{
    // protected $only_priority_tests = true;
    // protected $write_to_db = true;

    /// Path: GET    /users/{user_id}
    public function testGetUserSingle()
    {
        if ($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }

        // $this->withoutExceptionHandling();

        $response = $this->get('v1/users/1');
        $response->dumpHeaders();
        $response->dump();
    }
}

// On April 18, I found all the feature tests are giving 404 erros and not running. I spent the entire day dubgging it without figuring out what's causing. Finally decided to go with another approch(using a HTTP Client within the TestCase::load()). I'm hoping someday well be able to use the native methord.
