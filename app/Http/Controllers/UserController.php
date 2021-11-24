<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        return auth()->user();
    }

    public function all()
    {
        if (Auth::user()->is_admin) {
            return User::all();
        } else {
            return auth()->user();
        }
    }

    public function show($id)
    {
        if (Auth::user()->is_admin || $id == Auth::user()->id) {
            return User::find($id);
        } else {
            return response(['error' => 'unauthenticated'], 401);
        }
    }
}
