<?php

namespace Andileong\Framework\Core\tests\Database;

use Andileong\Framework\Core\Database\Exception\ModelNotFoundException;
use Andileong\Framework\Core\Database\Model\ModelCollection;
use Andileong\Framework\Core\Support\Str;
use Andileong\Framework\Core\tests\CreateUser;
use Andileong\Framework\Core\tests\stubs\User;
use Andileong\Framework\Core\tests\Transaction;
use PHPUnit\Framework\TestCase;

class ModelTest extends testcase
{
    use Transaction;
    use CreateUser;

    /** @test */
    public function it_has_mutator_feature()
    {
        $user = new User();
        $user->password = 'password';
        $this->assertTrue($user->password !== 'password');
    }

    /** @test */
    public function it_can_update_model_using_the_mutator_value()
    {
        $user = $this->createUser(['password' => 'wwww']);
        $user->update(['password' => 'password']);
        $this->assertTrue($user->password !== 'password');
    }

    /** @test */
    public function it_can_mutate_attribute_when_new_up_a_model()
    {
        $user = new User(['password' => 'password']);
        $this->assertTrue($user->password !== 'password');
    }

    /** @test */
    public function it_can_use_mutator_when_creating_model()
    {
        $user = $this->createUser(['password' => 'password']);
        $this->assertTrue($user->password !== 'password');
    }

    /** @test */
    public function it_can_save_a_model_using_mutator_value()
    {
        [$user] = $this->saveUser(null, 'password');
        $this->assertTrue($user->password !== 'password');

        $user = $this->createUser(['password' => 'wwww']);
        $user->password = 'password';
        $user->save();
        $this->assertTrue($user->password !== 'password');
    }

    /** @test */
    public function it_has_accessor_feature()
    {
        $user = new User();
        $user->password = 'password';
        $this->assertTrue(str_starts_with($user->password, 'access_'));

        $user = new User(['password' => 'password']);
        $this->assertTrue(str_starts_with($user->password, 'access_'));
    }

    /** @test */
    public function it_can_has_accessor_if_model_retrieve_from_db()
    {
        $user = $this->createUser(['password' => 'password']);
        $this->assertTrue(str_starts_with($user->password, 'access_'));
    }

    /** @test */
    public function it_can_be_created_using_create_method()
    {
        $user = $this->createUser();
        $this->assertTrue($user->isExisted());
        $count = User::where(['id' => $user->id])->count();
        $this->assertEquals(1, $count);
    }

    /** @test */
    public function it_can_be_deleted()
    {
        $user = $this->createUser();
        $count = User::where(['id' => $user->id])->count();
        $this->assertEquals(1, $count);

        $user->delete();
        $count = User::where(['id' => $user->id])->count();
        $this->assertEquals(0, $count);
    }

    /** @test */
    public function it_can_be_created_by_using_save_method()
    {
        [$user, $res] = $this->saveUser();

        $this->assertTrue($res);
        $count = User::where(['id' => $user->id])->count();
        $this->assertEquals(1, $count);
    }

    /** @test */
    public function it_can_be_updated_by_using_save_method()
    {
        $user = $this->createUser();
        $user->email = $email = Str::random(15);

        $res = $user->save();

        $this->assertTrue($res);
        $this->assertEquals($email, $user->email);
    }

    /** @test */
    public function it_can_be_updated_by_using_update_method()
    {
        $user = $this->createUser();
        $res = $user->update(['username' => $username = Str::random('5')]);

        $this->assertTrue($res);
        $this->assertEquals($username, $user->username);
    }

    /** @test */
    public function it_can_get_a_collection_of_records()
    {
        $user = $this->createUser();
        $users = User::orderBy('id', 'desc')->get();

        $this->assertInstanceOf(ModelCollection::class, $users);
        $this->assertEquals($user->id, $users[0]->id);
    }

    /** @test */
    public function it_can_get_a_collection_of_records_with_certain_columns()
    {
        $users = User::select('id', 'email')->get();
        $attributes = $users[0]->getAttributes();
        $this->assertArrayHasKey('email', $attributes);
        $this->assertArrayNotHasKey('name', $attributes);

        $users = User::get('id', 'name');
        $attributes = $users[0]->getAttributes();
        $this->assertArrayHasKey('name', $attributes);
        $this->assertArrayNotHasKey('email', $attributes);
    }

    /** @test */
    public function it_can_get_a_single_record()
    {
        $user = $this->createUser();
        $latest = User::latest()->first();
        $this->assertEquals($user->id, $latest->id);

        $user = User::where('name', 'fake')->first();
        $this->assertNull($user);
    }

    /** @test */
    public function it_can_get_a_single_record_with_columns()
    {
        $this->createUser();
        $user = User::latest()->first(['id', 'name']);
        $attributes = $user->getAttributes();
        $this->assertArrayNotHasKey('email', $attributes);
        $this->assertArrayHasKey('name', $attributes);
    }

    /** @test */
    public function it_can_find_record_by_primary_ids()
    {
        $user = $this->createUser();
        $latest = User::find($user->id);
        $this->assertEquals($user->id, $latest->id);

        $user1 = $this->createUser();
        $user2 = $this->createUser();
        $users = User::find([$user1->id, $user2->id]);
        $this->assertEquals($user1->id, $users[0]->id);

        $user = User::find('fake');
        $this->assertNull($user);
    }

    /** @test */
    public function it_throw_exception_if_record_not_found()
    {
        $this->expectException(ModelNotFoundException::class);
        User::findOrFail(999999);
    }

    /** @test */
    public function it_can_find_record_by_primary_ids_with_desire_columns()
    {
        $user = $this->createUser();
        $latestUser = User::find($user->id, ['id', 'email']);

        $attributes = $latestUser->getAttributes();
        $this->assertArrayNotHasKey('name', $attributes);
        $this->assertArrayHasKey('email', $attributes);
    }

    /** @test */
    public function it_can_be_json_serialized_properly_with_appends_and_accessors()
    {
        $user = $this->createUser();
        $userInJson = json_encode($user);
        $user = json_decode($userInJson);

        $this->assertTrue(str_starts_with($user->password, 'access_'));
        $this->assertEquals('bar', $user->foo_bar);
        $this->assertFalse(property_exists($user, 'no_appends'));
    }

    /** @test */
    public function it_can_use_scopes()
    {
        $this->createUser(['location' => 'usa']);
        $this->createUser(['location' => 'uk']);

        $users = User::country('usa')->get();
        $usa = array_filter($users->all(), fn ($user) => $user->location === 'usa');
        $uk = array_filter($users->all(), fn ($user) => $user->location === 'uk');
        $this->assertCount(1, $usa);
        $this->assertCount(0, $uk);

        $users = User::country('uk')->get();
        $usa = array_filter($users->all(), fn ($user) => $user->location === 'usa');
        $uk = array_filter($users->all(), fn ($user) => $user->location === 'uk');
        $this->assertCount(0, $usa);
        $this->assertCount(1, $uk);
    }

    /** @test */
    public function it_trigger_creating_event()
    {
        $user = User::create($this->baseAttribute());
        $this->assertNotNull($user->avatar);
    }

    /** @test */
    public function it_auto_fill_create_at_updated_at_timestamp_when_create_user()
    {
        $user = User::create($this->baseAttribute());
        $this->assertNotNull($user->created_at);
        $this->assertNotNull($user->updated_at);

        $saveUser = new User($this->baseAttribute());
        $saveUser->save();

        $this->assertNotNull($saveUser->created_at);
        $this->assertNotNull($saveUser->updated_at);
    }

    /** @test */
    public function it_auto_fill_updated_at_timestamp_when_update_user()
    {
        $user = User::create($this->baseAttribute());
        $oldUpdate = $user->updated_at;
        $user->update(['location' => 'hiii']);
        $newUpdate = $user->updated_at;

        $this->assertNotSame($oldUpdate, $newUpdate);


        $user2 = User::create($this->baseAttribute());
        $oldUpdate = $user2->updated_at;
        $user2->location = 'eygey';
        $user2->save();

        $newUpdate = $user2->updated_at;
        $this->assertNotSame($oldUpdate, $newUpdate);
    }
}
