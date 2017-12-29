<?php

namespace App\Http\Controllers;

use App\Post;

class PostController extends Controller {

    public function __construct() {
        $this->middleware('auth');
    }

    public function index() {
        $posts = Post::active();

        return view('welcome', compact('posts'));
    }
}
