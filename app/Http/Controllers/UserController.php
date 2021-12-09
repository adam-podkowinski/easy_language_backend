<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\Dictionary;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    public function index()
    {
        return new UserResource(auth()->user());
    }

    public function all()
    {
        if (Auth::user()->is_admin) {
            return User::all();
        } else {
            return new UserResource(auth()->user());
        }
    }

    public function show($id)
    {
        if (Auth::user()->is_admin || $id == Auth::user()->id) {
            return new UserResource(User::find($id));
        } else {
            return response(['error' => 'unauthenticated'], 401);
        }
    }

    public function currentDictionary()
    {
        $dict = auth()->user()->currentDictionary;
        if (Gate::allows('access-dictionary', $dict)) {
            return $dict;
        } else {
            return response(['error' => 'forbidden'], 403);
        }
    }

    public function update(Request $request)
    {
        $request->validate([
            'email' => 'email',
        ]);

        $user = Auth::user();

        if ($request['current_dictionary_id'] != null) {
            $dict = Dictionary::find($request['current_dictionary_id']);
            if ($dict != null) {
                if (!Gate::allows('access-dictionary', $dict)) {
                    return response(['error' => 'invalid dictionary id']);
                }
            } else {
                return response(['error' => 'invalid dictionary id']);
            }
        }
        $user->update($request->except(['password', 'is_admin']));

        return new UserResource($user);
    }
}
