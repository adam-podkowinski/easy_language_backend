<?php

namespace App\Http\Controllers;

use App\Models\Word;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
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
            'order_index' => 'required|int',
        ]);

        $dict = $user->dictionaries->where('id', $request['dictionary_id']);

        if (count($dict) <= 0) {
            return response(['error' => 'dictionary doesnt exist'], 400);
        }

        return Word::create([
            'word_foreign' => $request['word_foreign'],
            'word_translation' => $request['word_translation'],
            'language' => $request['language'],
            'learning_status' => $request['learning_status'] ?? 'reviewing',
            'times_reviewed' => $request['times_reviewed'] ?? 0,
            'dictionary_id' => $request['dictionary_id'],
            'order_index' => $request['order_index'],
            'user_id' => auth()->user()->id,
        ]);
    }

    public function update(Request $request, $id)
    {
        $word = Word::find($id);

        if (!Gate::allows('access-word', $word)) {
            return response(['error' => 'forbidden'], 403);
        }

        return $word->update($request->except(['user_id']));
    }

    public function updateBulk(Request $request)
    {
        foreach ($request->all() as $word) {
            $wordToUpdate = auth()->user()->words->where('id', $word['id'])->first();

            if (empty($wordToUpdate)) {
                return response(['error' => 'no words to update'], 404);
            }

            $wordToUpdate->update(Arr::except($word, ['user_id']));
        }

        return auth()->user()->words;
    }

    public function destroyWordBank(Request $request)
    {
        $wordsToDelete = Word::whereUserId(auth()->user()->id)->whereLanguage($request->get('language'))->get();

        if (count($wordsToDelete) <= 0) {
            return response(['error' => 'no words to delete'], 404);
        }

        foreach ($wordsToDelete as $word) {
            $word->delete();
        }

        return response(['success' => 'words deleted']);
    }

    public function destroy($id)
    {
        $word = Word::find($id);

        if (!Gate::allows('access-word', $word)) {
            return response(['error' => 'forbidden'], 403);
        }

        return Word::destroy($id);
    }
}
