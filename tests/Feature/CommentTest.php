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
    protected $student_id = 21927; // Han in Test City

    // Path: GET /centers/{center_id}/comments
    public function testCenterComments()
    {
        if ($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }

        $center_id = 25;

        $this->load("/centers/$center_id/comments", 'GET');

        $db_comment_ids = app('db')->table('Comment')->select('id', 'comment')
            ->where('item_type', 'Center')->where('item_id', $center_id)
            ->pluck('id')->toArray();

        $found = 0;
        foreach ($this->response_data->data->comments as $l) {
            if (in_array($l->id, $db_comment_ids)) {
                $found ++;
            }
        }
        $this->assertEquals($found, count($db_comment_ids));
    }

    /// Path: POST    /students/{student_id}/comments
    public function testCreateStudentComment()
    {
        if ($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }
        if (!$this->write_to_db) {
            $this->markTestSkipped("Skipping as this test writes to the Database.");
        }

        $this->load("/students/{$this->student_id}/comments", 'POST', [
            'comment'   => "Hello World",
            "added_by_user_id"  => 1
        ]);

        $this->assertEquals($this->response_data->status, 'success');
        $created_comment_id = $this->response_data->data->comment->id;
        $this->assertEquals($this->response->getStatusCode(), 200);

        $db_comments = app('db')->table('Comment')->select('id', 'comment')
            ->where('item_type', 'Student')->where('item_id', $this->student_id)
            ->pluck('comment')->toArray();
        $this->assertTrue(in_array('Hello World', $db_comments));

        return $created_comment_id;
    }

    // Path: GET /students/{student_id}/comments
    /**
     * @depends testCreateStudentComment
     */
    public function testStudentComments($created_comment_id)
    {
        if ($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }

        $this->load("/students/{$this->student_id}/comments", 'GET');

        $found = false;
        foreach ($this->response_data->data->comments as $l) {
            if ($l->comment == "Hello World") {
                $found = true;
            }
        }
        $this->assertTrue($found);
    }

    /// Path: DELETE    /students/{student_id}/comments/{comment_id}
    /**
     * @depends testCreateStudentComment
     */
    public function testDeletecomment($created_comment_id)
    {
        if ($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }
        if (!$this->write_to_db) {
            $this->markTestSkipped("Skipping as this test writes to the Database.");
        }
        if (!$created_comment_id) {
            $this->markTestSkipped("Can't find ID of comment created as test.");
        }

        $this->load("/students/{$this->student_id}/comments/{$created_comment_id}", 'DELETE');
        $this->assertEquals($this->response->getStatusCode(), 200);

        $found = app('db')->table("Comment")->where('id', $created_comment_id)->count();
        $this->assertEquals($found, '0'); // Its actually deleted.
    }


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
