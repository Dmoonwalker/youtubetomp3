<?php
// CommentController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use Illuminate\Support\Facades\Session;

class CommentController extends Controller
{
    public function index()
    {

        $comments = [
            [
                'author' => 'John Doe',
                'date' => '1 month ago',
                'text' => 'Impressive! Though it seems the drag feature could be improved. But overall it looks incredible. You\'ve nailed the design and the responsiveness at various breakpoints works really well.',
                'avatar' => asset('assets/img/profile.png')
            ],
            // Add more dummy comments as needed
        ];
       // $comments = Comment::with('user')->orderBy('created_at', 'desc')->get();
        return response()->json(['comments' => $comments]);
    }

    public function store(Request $request)
    {
    
        $request->validate([
            'comment' => 'required|string|max:255',
        ]);

        $comment = new Comment();
    /// Ensure the user is authenticated
        $comment->text = $request->input('comment');
        $comment->save();

        return response()->json(['comment' => $comment]);
    }
}
