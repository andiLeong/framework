<?php

namespace Andileong\Framework\Tests\Feature;

use Andileong\Framework\Core\tests\CreateUser;
use Andileong\Framework\Core\tests\Testcase\ApplicationTestCase;
use Andileong\Framework\Core\tests\Transaction;
use App\Models\User;

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
        $this->assertCount(1, array_filter($body['data'], fn($u) => $u['id'] == $user->id));
    }

    /** @test */
    public function it_can_get_a_single_user()
    {
        $user = $this->createUser();
        $response = $this->get("/user/$user->id");
        $response->assertJson()->assertOk();
        $body = $response->getBodyAsArray();
        $this->assertEquals($user->id, $body['id']);
    }

    /** @test */
    public function it_gets_404_if_a_user_not_found()
    {
        $response = $this->get("/user/99999999");
        $response->assertJson()->assertNotFound();
    }

    /** @test */
    public function it_can_create_a_user()
    {
        $user = $this->createUser();
        $email = 'uniqueemail@gmail.com';
        $attributes = $this->baseAttribute([
            'email' => $email
        ]);
        $this->assertEquals(0, User::whereEmail($email)->count());

        $response = $this->post("/user", $attributes, ['Authorization' => $this->getLoginToken($user->email)]);
        $response->assertOk();
        $body = $response->getBodyAsArray();
        $this->assertEquals($email, $body['email']);
        $this->assertEquals(1, User::whereEmail($email)->count());
    }

    /** @test */
    public function it_can_update_a_user()
    {
        $user = $this->createUser([
            'password' => 'password'
        ]);
        $name = 'new name';
        $this->assertNotEquals($name, $user->name);

        $attributes = $this->baseAttribute([
            'name' => $name
        ]);

        $response = $this->put("/user/$user->id", $attributes, ['Authorization' => $this->getLoginToken($user->email)]);
        $response->assertOk();
        $body = $response->getBodyAsArray();
        $this->assertEquals($name, $body['name']);
        $this->assertEquals($name, User::whereId($user->id)->first()->name);
    }

    /** @test */
    public function it_can_delete_a_user()
    {
        $user = $this->createUser([
            'password' => 'password'
        ]);
        $this->assertEquals(1, User::whereEmail($user->email)->count());

        $response = $this->delete("/user/$user->id", [], ['Authorization' => $this->getLoginToken($user->email)]);
        $response->assertOk();
        $this->assertEquals(0, User::whereEmail($user->email)->count());
    }

    public function getLoginToken($email)
    {
        $body = $this->post("/login", [
            'email' => $email,
            'password' => 'password'
        ])->getBodyAsArray();
        return $body['jwt_token'];
    }
}
