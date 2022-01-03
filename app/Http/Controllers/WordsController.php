<?php

namespace App\Http\Controllers;

use App\Models\Dictionary;
use App\Models\Word;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WordsController extends Controller
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
        if ($request->get('language') == null) {
            return auth()->user()->words;
        } else {
            return Word::whereUserId(auth()->user()->id)->where('language', $request->get('language'))->get();
        }
    }

    public function show($id)
    {
        $word = Word::find($id);

        if (!Gate::allows('access-word', $word)) {
            return response(['error' => 'forbidden'], 403);
        }

        return $word;
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        if (empty($user)) {
            return response(['error' => 'forbidden'], 403);
        }

        $request->validate([
            'word_foreign' => 'required|string',
            'word_translation' => 'required|string',
            'language' => 'required|string',
            'dictionary_id' => 'required',
        ]);

        $dicts = $user->dictionaries->where('id', $request['dictionary_id']);

        if (count($dicts) <= 0) {
            return response(['error' => 'dictionary doesnt exist'], 400);
        }

        $dicts->first()->touch();

        return Word::create([
            'word_foreign' => $request['word_foreign'],
            'word_translation' => $request['word_translation'],
            'language' => $request['language'],
            'learning_status' => $request['learning_status'] ?? 'reviewing',
            'times_reviewed' => (int)$request['times_reviewed'] ?? 0,
            'dictionary_id' => (int)$request['dictionary_id'] ?? 0,
            'user_id' => (int)auth()->user()->id,
        ]);
    }

    public function update(Request $request, $id)
    {
        $word = $wordArg ?? Word::find($id);

        if (!Gate::allows('access-word', $word)) {
            return response(['error' => 'forbidden'], 403);
        }

        if ($request['dictionary_id'] != null) {
            if (!Gate::allows('access-dictionary', Dictionary::find($request['dictionary_id']))) {
                return response(['error' => 'invalid dictionary id']);
            }
        }

        $dict = $word->dictionary;

        $dict->touch();

        $word->update($request->except(['user_id']));

        return $word;
    }

    public function destroy($id)
    {
        $word = Word::find($id);

        if (!Gate::allows('access-word', $word)) {
            return response(['error' => 'forbidden'], 403);
        }

        $dict = $word->dictionary;

        $dict->touch();

        return $word->delete();
    }
}
