<?php

namespace Andileong\Framework\Tests\Database;

use Andileong\Framework\Core\Facades\DB;
use Andileong\Framework\Core\tests\CreateUser;
use Andileong\Framework\Core\tests\Transaction;
use PHPUnit\Framework\TestCase;

class QueryBuilderTest extends TestCase
{
    use CreateUser;
    use Transaction;

    /** @test */
    public function it_supports_conditional_chaining_call()
    {
        $user = $this->createUser([
            'name' => 'when'
        ]);
        $user = $this->createUser([
            'name' => 'iamfalse'
        ]);
        $users = DB::from('users')->when(true, fn($builder) => $builder->whereName('when'))->getRaw();
        $users2 = DB::from('users')->when(
            false,
            fn($builder) => $builder->whereName('when'),
            fn($builder) => $builder->whereName('iamfalse')
        )->getRaw();

        $this->assertEquals('when', $users[0]->name);
        $this->assertEquals('iamfalse', $users2[0]->name);
    }
}
