<?php
namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use App\Models\Center;
use App\Models\Comment;

/**
 * @runTestsInSeparateProcesses
 */
class CommentTest extends TestCase
{
    use WithoutMiddleware;

    // protected $only_priority_tests = true;
    // protected $write_to_db = false;

    // GraphQL: {center(id: ID) { id comments { comment }}}
    public function testCenterComment()
    {
        if ($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }

        $center_id = 25;
        $this->graphql('{center(id: ' . $center_id . ') { comments { id comment }}}');

        $comment_ids = app('db')->table('Comment')
            ->where("item_type", 'Center')->where("item_id", $center_id)
            ->get()->pluck('id')->toArray();
        $found = 0;

        foreach ($this->response_data->data->center->comments as $cmnt) {
            if (in_array($cmnt->id, $comment_ids)) {
                $found ++;
            }
        }
        $this->assertEquals($found, count($comment_ids));
    }
}
