<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePost;
use App\Post;

class PostController extends Controller {

    public function __construct() {
        $this->middleware('auth');
    }

    public function index(bool $edit = false) {
        if ($edit)
            $posts = Post::ordering()->get();
        else
            $posts = Post::active();

        return view('welcome', compact('posts', 'edit'));
    }

    public function edit() {
        $this->authorize('edit', Post::class);

        return $this->index(true);
    }

    public function store(StorePost $request) {
        $request->getPost()->save();

        return redirect('/posts/edit')->with('posts-status', 'Neue Ankündigung erfolgreich angelegt');
    }

    public function destroy(Post $post) {
        $this->authorize('delete', $post);
        $post->delete();

        return redirect('/posts/edit')->with('posts-status', 'Ankündigung erfolgreich gelöscht');
    }

}
