<?php

namespace Andileong\Framework\Core\tests;

use Andileong\Framework\Core\Database\Model\Model;
use Andileong\Framework\Core\Support\Str;
use PHPUnit\Framework\TestCase;

class ModelTest extends testcase
{
    /** @test */
    public function it_has_mutator_feature()
    {
        $user = new User();
        $user->password = 'password';
        $this->assertEquals($user->getAttributes()['password'], md5('password'));
    }

    /** @test */
    public function it_can_update_model_using_the_mutator_value()
    {
        $user = $this->createUser(['password' => 'wwww']);
        $user->update(['password' => 'password']);
        $this->assertEquals($user->getAttributes()['password'], md5('password'));
    }

    /** @test */
    public function it_can_mutate_attribute_when_new_up_a_model()
    {
        $user = new User(['password' => 'password']);
        $this->assertEquals($user->getAttributes()['password'], md5('password'));
    }

    /** @test */
    public function it_can_use_mutator_when_creating_model()
    {
        $user = $this->createUser(['password' => 'password']);
        $this->assertEquals($user->getAttributes()['password'], md5('password'));
    }

    /** @test */
    public function it_can_save_a_model_using_mutator_value()
    {
        [$user] = $this->saveUser(null, 'password');
        $this->assertEquals($user->getAttributes()['password'], md5('password'));

        $user = $this->createUser(['password' => 'wwww']);
        $this->assertEquals($user->getAttributes()['password'], md5('wwww'));
        $user->password = 'password';
        $user->save();
        $this->assertEquals($user->getAttributes()['password'], md5('password'));
    }

    /** @test */
    public function it_has_accessor_feature()
    {
        $user = new User();
        $user->password = 'password';
        $this->assertEquals($user->password, 'access_' . md5('password'));

        $user = new User(['password' => 'password']);
        $this->assertEquals($user->password, 'access_' . md5('password'));
    }

    /** @test */
    public function it_can_has_accessor_if_model_retrieve_from_db()
    {
        $user = $this->createUser(['password' => 'password']);
        $this->assertEquals($user->password, 'access_' . md5('password'));
    }

    /** @test */
    public function it_can_be_created()
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
    public function it_can_a_collection_of_records()
    {
        $user = $this->createUser();
        $users = User::orderBy('id', 'desc')->get();

        $this->assertIsArray($users);
        $this->assertEquals($user->id, $users[0]->id);
    }

    /** @test */
    public function it_can_get_a_single_record()
    {
        $user = $this->createUser();
        $latest = User::latest()->first();

        $this->assertEquals($user->id, $latest->id);
    }

    public function createUser($attributes = [])
    {
        return User::create($this->baseAttribute($attributes));
    }

    public function baseAttribute($overwrite = [])
    {
        return array_merge([
            'email' => Str::random(5) . '@asd.com',
            'password' => 'mysdsdsd@asd.com',
            'username' => Str::random(),
            'name' => 'mysdsdsd@asd.com',
        ], $overwrite);
    }

    public function saveUser($username = null, $password = null, $email = null, $name = null)
    {
        $user = new User();
        $user->username = $username ?? Str::random(4);
        $user->password = $password ?? Str::random();
        $user->email = $email ?? Str::random();
        $user->name = $name ?? Str::random(4);

        $result = $user->save();
        return [$user, $result];
    }
}


class User extends Model
{
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = md5($value);
    }

    public function getPasswordAttribute($value)
    {
        return 'access_' . $value;
    }

}