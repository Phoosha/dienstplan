<?php

namespace App\Http\Requests;

use App\Post;
use Auth;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class StorePost extends FormRequest {

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return Auth::user()->can('store', Post::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'title' => 'required|string|max:255',
            'body' => 'present|string|max:65553',
            'release_on' => 'required|date',
            'expire_on' => 'required',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator $validator
     * @return void
     */
    public function withValidator($validator) {
        $validator->sometimes('expire_on', 'date', function ($data) {
            return $data->expire_on !== 'nie';
        });
    }

    public function getPost() {
        $postAttrs = $this->all();

        $postAttrs['release_on'] = Carbon::parse($postAttrs['release_on']);
        if ($postAttrs['expire_on'] === 'nie')
            unset($postAttrs['expire_on']);
        else
            $postAttrs['expire_on'] = Carbon::parse($postAttrs['expire_on']);

        $post = new Post($postAttrs);
        $post->user_id = Auth::user()->id;

        return $post;
    }

}
