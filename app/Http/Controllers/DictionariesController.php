<?php

namespace App\Http\Controllers;

use App\Models\Dictionary;
use App\Models\Word;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DictionariesController extends Controller
{
    public function all()
    {
        if (Auth::user()->is_admin) {
            return Word::all();
        } else {
            return auth()->user()->words;
        }
    }

    public function index(Request $request)
    {
        return auth()->user()->dictionaries;
    }

    public function show(string $language)
    {
        return auth()->user()->dictionaries->where('language', $language);
    }

    public function showWords(string $language)
    {
        return auth()->user()->dictionaries->where('language', $language)->first()->words;
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        if (empty($user)) {
            return response(['error' => 'forbidden'], 403);
        }

        $request->validate([
            'language' => 'required|string',
        ]);

        return Dictionary::create([
            'language' => $request['language'],
            'user_id' => $user->id,
        ]);
    }
}
