<?php

namespace Andileong\Framework\Tests\Feature;

use Andileong\Framework\Core\tests\CreateUser;
use Andileong\Framework\Core\tests\Testcase\ApplicationTestCase;
use Andileong\Framework\Core\tests\Transaction;

class UserTest extends ApplicationTestCase
{
    use Transaction;
    use CreateUser;

    /** @test */
    public function it_can_get_list_of_users()
    {
        $user = $this->createUser();
        $response = $this->get("/user", ['foo' => 'va']);
        $response->assertJson()->assertOk();
        $body = $response->getBodyAsArray();
        $this->assertCount(1,array_filter($body['data'],fn($u) => $u['id'] == $user->id));
        $this->app->get('cache')->delete('users');
    }
}
