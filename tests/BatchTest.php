<?php

/**
 * @runTestsInSeparateProcesses
 */
class BatchTest extends TestCase
{
    private $only_priority_tests = false;
    private $write_to_db = true;

    /// Path: GET   /batches/{batch_id}/levels
    public function testGetLevelsInBatchList()
    {
        if($this->only_priority_tests) $this->markTestSkipped("Running only priority tests.");

        $this->get('/users/1/groups');
        $data = json_decode($this->response->getContent());

        $this->assertEquals($data->status, 'success');
        
        $search_for = 'ES Volunteer';
        $found = false;
        foreach ($data->data->groups as $key => $info) {
            if($info->name == $search_for) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);
        
        $this->assertEquals(200, $this->response->status());
    }

}
