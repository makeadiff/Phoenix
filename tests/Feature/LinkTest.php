<?php
namespace Tests\Feature;

use Tests\TestCase;

/**
 * @runTestsInSeparateProcesses
 */
class LinkTest extends TestCase
{
    // protected $only_priority_tests = false;
    // protected $write_to_db = true;

    // Path: GET /users/{user_id}/links
    public function testUserLinks()
    {
        if ($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }

        $user_id = 1;

        $this->load("/users/$user_id/links", 'GET');

        $user_groups = app('db')->table('Group')->select('Group.id', 'Group.vertical_id')->join('UserGroup', 'UserGroup.group_id', '=', 'Group.id')
            ->where('Group.status', '1')->where('UserGroup.year', $this->year)->where('UserGroup.user_id', $user_id);
        $group_ids = $user_groups->pluck('id')->toArray();
        $vertical_ids = $user_groups->pluck('vertical_id')->toArray();
        $city_id = app('db')->table('User')->select('city_id')->where('id', $user_id)->first()->city_id;
        $center_ids = app('db')->table('Batch')->select('center_id')->join('UserBatch', 'UserBatch.batch_id', '=', 'Batch.id')
            ->where('Batch.year', $this->year)->where('UserBatch.user_id', $user_id)->where('Batch.status', '1')
            ->pluck('center_id')->toArray();

        $links = app('db')->table('Link')->where('status', '1');
        $links->where(function ($q) use ($center_ids) {
            $q->where("center_id", "0")->orWhereIn("center_id", $center_ids);
        });
        $links->where(function ($q) use ($group_ids) {
            $q->where("group_id", "0")->orWhereIn("group_id", $group_ids);
        });
        $links->where(function ($q) use ($vertical_ids) {
            $q->where("vertical_id", "0")->orWhereIn("vertical_id", $vertical_ids);
        });

        $all_link_ids = $links->get()->pluck('id')->toArray();

        $found = 0;

        foreach ($this->response_data->data->links as $l) {
            if (in_array($l->id, $all_link_ids)) {
                $found ++;
            }
        }
        $this->assertEquals($found, count($all_link_ids));
    }
}
