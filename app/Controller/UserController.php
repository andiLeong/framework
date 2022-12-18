<?php

namespace App\Controller;

use Andileong\Framework\Core\Request\Request;
use Andileong\Validation\Validator;
use App\Models\User;

class UserController
{
    public function index()
    {
        return User::latest()->paginate(10);
    }

    public function show($id)
    {
        return User::find($id);
    }

    public function store(Request $request)
    {
        $validator = new Validator($request->all());
        $attributes = $validator->validate([
            'name' => 'required',
            'email' => ['required', 'email', fn($email) => User::query()->whereEmail($email)->count() === 0
            ],
            'password' => 'required',
            'username' => 'required',
        ]);

        return User::create($attributes);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if (is_null($user)) {
            return json(['message' => 'User not found'], 404);
        }

        $validator = new Validator($request->all());
        $attributes = $validator->validate([
            'name' => 'required',
            'email' => ['required', 'email', function ($email) use ($user) {
                if ($email === $user->email) {
                    return true;
                }
                return User::query()->whereEmail($email)->count() === 0;
            }
            ],
            'password' => 'required',
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