<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use App\Models\Group;

/**
 * @runInSeparateProcess
 */
class PermissionTest extends TestCase
{
    use WithoutMiddleware;

    public function testGroupPermission()
    {
        $group = new Group;
        $permissions = $group->find(9)->permissions();

        $this->assertContains('kids_index', $permissions);
    }

    public function testGroupPermissionInheritence()
    {
        $group = new Group;
        $permissions = $group->find(368)->permissions(); // This group inherits from group#9

        $this->assertContains('kids_index', $permissions);
    }
}
