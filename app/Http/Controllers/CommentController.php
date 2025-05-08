<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Sheet;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, Sheet $sheet)
    {
        $request->validate([
            'body' => 'required|string|max:1000',
        ]);

        $comment = new Comment();
        $comment->user_id = auth()->id();
        $comment->sheet_id = $sheet->id;
        $comment->body = $request->input('body');
        $comment->save();

        return redirect()
            ->route('sheets.show', $sheet)
            ->with('success', 'Comment posted successfully.');
    }

    //Submit edited version of the comment
    public function edit(Request $request)
    {
        $request->validate([
            'comment_id' => 'required|exists:comments,id',
        ]);
        $comment = Comment::find($request->input('comment_id'));
        if ($comment->user_id !== auth()->id()) {// Check if the comment belongs to the user
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'body' => 'required|string|max:1000',
        ]);

        $comment->body = $request->input('body');
        $comment->save();

        return redirect()
            ->route('sheets.show', $comment->sheet_id)
            ->with('success', 'Comment updated.');
    }

    public function destroy(Request $request, Sheet $sheet, Comment $comment)
    {
        // Validate the request, if the comment does not belong to the user, abort with a 403 error
        if ($comment->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $comment->delete();

        return redirect()
            ->back()
            ->with('success', 'Comment deleted.');
    }
}
