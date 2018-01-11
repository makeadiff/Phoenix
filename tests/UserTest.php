<?php
# use Laravel\Lumen\Testing\DatabaseTransactions;

/**
 * @runTestsInSeparateProcesses
 */
class UserTest extends TestCase
{
    private $only_priority_tests = true;

    public function testSingleUserGet()
    {
        if($this->only_priority_tests) $this->markTestSkipped("Running only priority tests.");

        $this->get('/users/1');
        $data = json_decode($this->response->getContent());

        $this->assertEquals($data->status, 'success');
        $this->assertEquals($data->data->user->name, 'Binny V A');
        $this->assertEquals(200, $this->response->status());
    }

    public function testSingleDeletedUserGet()
    {
        if($this->only_priority_tests) $this->markTestSkipped("Running only priority tests.");

        $this->get('/users/2');
        $data = json_decode($this->response->getContent());

        $this->assertEquals($data->status, 'error');
        $this->assertEquals($data->message, "Can't find user with user id '2'");
        $this->assertEquals(404, $this->response->status());
    }

    public function testSearchUserGet() 
    {
        $this->get('/users?name=Binny');
        $data = json_decode($this->response->getContent());

        $this->assertEquals($data->status, 'success');
        $this->assertEquals($data->data->users[0]->name, "Binny V A");
        $this->assertEquals(200, $this->response->status());

        $this->get('/users?phone=9746068565');
        $data = json_decode($this->response->getContent());

        $this->assertEquals($data->status, 'success');
        $this->assertEquals($data->data->users[0]->name, "Binny V A");
        $this->assertEquals(200, $this->response->status());

        $this->get('/users?email=binnyva@gmail.com&mad_email=cto@makeadiff.in&city_id=26&user_type=volunteer');
        $data = json_decode($this->response->getContent());

        $this->assertEquals($data->status, 'success');
        $this->assertEquals($data->data->users[0]->name, "Binny V A");
        $this->assertEquals(200, $this->response->status());
    }
}
