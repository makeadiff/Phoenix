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
        $es_vol_group_id = 9;
        $permissions = $group->find($es_vol_group_id)->permissions();

        $this->assertContains('kids_index', $permissions);
    }

    public function testGroupPermissionInheritence()
    {
        $group = new Group;
        $es_trained_group_id = 368;
        $permissions = $group->find($es_trained_group_id)->permissions(); // This group inherits from group#9

        $this->assertContains('kids_index', $permissions);
    }
}
