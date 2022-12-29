<?php

namespace App\Controller;

use Andileong\Framework\Core\Support\Controller;
use App\Models\User;

class UserController extends Controller
{
    protected $columns = ['id', 'name', 'email', 'avatar', 'username', 'location', 'created_at', 'updated_at'];

    public function index()
    {
        return User::select($this->columns)->latest()->paginate(10);
    }

    public function show($id)
    {
        return User::select($this->columns)->find($id);
    }

    public function store()
    {
        $attributes = $this->validate([
            'name' => 'required',
            'location' => 'required',
            'email' => ['required', 'email', fn($email) => User::query()->whereEmail($email)->count() === 0
            ],
            'password' => 'required',
            'username' => 'required',
        ]);

        return User::create($attributes);
    }

    public function update($id)
    {
        $user = User::find($id);
        if (is_null($user)) {
            return json(['message' => 'User not found'], 404);
        }

        $attributes = $this->validate([
            'name' => 'required',
            'location' => 'required',
            'email' => ['required', 'email', function ($email) use ($user) {
                if ($email === $user->email) {
                    return true;
                }
                return User::query()->whereEmail($email)->count() === 0;
            }],
            'username' => 'required',
        ]);

        $user->update($attributes);
        return $user;
    }

    public function destroy($id)
    {
        $user = User::find($id);
        if (is_null($user)) {
            return json(['message' => 'User not found'], 404);
        }
        $user->delete();
    }
}