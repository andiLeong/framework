<?php

namespace Andileong\Framework\Core\tests\Database;

use Andileong\Framework\Core\Facades\DB;
use Andileong\Framework\Core\tests\CreateUser;
use Andileong\Framework\Core\tests\stubs\User;
use Exception;
use PHPUnit\Framework\TestCase;

class TransactionTest extends testcase
{
    use CreateUser;

    /** @test */
    public function it_init_auto_transaction()
    {
        DB::setPdo();
        $user = DB::transaction(function () {
            return $this->createUser();
        });

        $this->assertTrue($user->isExisted());
        $userId = $user->id;
        $user = User::find($userId);
        $user->delete();

        $this->expectException(Exception::class);
        $user = DB::transaction(function () {
            $user = $this->createUser();
            throw new Exception('new exception');
            return $user;
        });

        $this->assertFalse($user->isExisted());
    }

    /** @test */
    public function it_support_manual_transaction()
    {
        DB::setPdo();
        DB::beginTransaction();
        $user = $this->createUser();
        DB::commit();
        $user = User::find($user->id);
        $this->assertTrue($user->isExisted());
        $user->delete();

        DB::beginTransaction();
        $user = $this->createUser();
        DB::rollback();
        $user = User::find($user->id);
        $this->assertNull($user);
    }
}