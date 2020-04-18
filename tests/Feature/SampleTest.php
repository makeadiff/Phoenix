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

        // $response = $this->get('v1/users/1');
        // $response->dumpHeaders();
        // $response->dump();

        $response = $this->load('/users/1');
        $response->dump();
    }

}
