<?php

namespace Andileong\Framework\Tests\Database;

use Andileong\Framework\Core\Facades\DB;
use PHPUnit\Framework\TestCase;

class ConnectionTest extends TestCase
{

    /** @test */
    public function it_can_query_directly_from_db_facades()
    {
        $users = DB::from('users')->getRaw();
        $this->assertIsArray($users);
    }

    /** @test */
    public function it_can_start_query_by_select()
    {
        $users = DB::select('id', 'name')->from('users')->getRaw();
        $this->assertIsArray($users);
    }
}
