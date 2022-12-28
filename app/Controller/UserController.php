<?php

namespace App\Controller;

use Andileong\Framework\Core\Request\Request;
use Andileong\Framework\Core\Support\Arr;
use Andileong\Framework\Core\Support\Controller;
use Andileong\Framework\Core\Support\Str;
use App\Models\User;
use Carbon\Carbon;

class UserController extends Controller
{
    protected $columns = ['id', 'name', 'email', 'avatar', 'username', 'location', 'created_at'];

    public function index()
    {
        return User::select($this->columns)->latest()->paginate(10);
    }

    public function show($id)
    {
        return User::select($this->columns)->find($id);
    }

    public function store(Request $request)
    {
        $attributes = $this->validate([
            'name' => 'required',
            'location' => 'required',
            'email' => ['required', 'email', fn($email) => User::query()->whereEmail($email)->count() === 0
            ],
            'password' => 'required',
            'username' => 'required',
        ]);

        $additional = [
            'remember_token' => Str::random(20),
            'created_at' => Carbon::now(),
            'avatar' => 'https://i.pravatar.cc/150?img=' . Arr::random(range(1, 70))
        ];

        return User::create($attributes + $additional);
    }

    public function update(Request $request, $id)
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