<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // returning our user objects
        return User::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        // $hashed = Hash('sha256', $password);

        // validate our request
        $request->validate([
            'name' => 'required|string|max:255|min:3',
            'email' => ['required', 'string', Rule::unique('users')],
            'password' => 'required|confirmed|string|min:8',
        ]);

        // $request['password'] = $hashed;

        return User::create($request->all());
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return $user;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        // Validating updated data
        $validData = $request->validate([
            'name' => 'sometimes|string|max:255|min:3',
            'email' => ['sometimes', 'string', Rule::unique('users')->ignore($user->id)],
            'password' => 'sometimes|confirmed|string|min:8',
        ]);

        $user->update($validData);

        return $user;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();

        return response()->json([
            'msg' => 'User deleted successfully',
        ], 200);
    }
}
