<?php

namespace Tests\Feature;

use App\Post;
use App\User;
use Auth;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCaseWithAuth;

class PostRoutesTest extends TestCaseWithAuth {

    use WithFaker, DatabaseTransactions;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var Post
     */
    protected $post;

    protected function setUp() {
        parent::setUp();
        $this->user = factory(User::class)->create([ 'is_admin' => true ]);
        $this->post = factory(Post::class)->create([ 'user_id' => $this->user->id ]);
    }

    /**
     * @dataProvider userProvider
     */
    public function testIndexReturnsOKUnlessGuest($user) {
        $response = $this->actingByString($user)->get('/');

        if (!$this->guestAssertions($response))
            $response->assertStatus(200);
    }

    /**
     * @dataProvider userProvider
     */
    public function testPostsEditReturnsOKOnlyIfAdmin($user) {
        $response = $this->actingByString($user)->get("/posts/edit");

        if (! $this->adminOnlyAssertions($response))
            $response->assertStatus(200);
    }

    /**
     * @dataProvider userProvider
     * @depends testPostsEditReturnsOKOnlyIfAdmin
     */
    public function testPostPostRedirectsBackWithoutErrorsStorePerformedOnlyIfAdmin($user) {
        $this->post->delete();

        $post = $this->post->attributesToArray();
        $post['release_on'] = $this->post->release_on
            ->format(config('dienstplan.date_format'));
        $post['expire_on'] = $this->post->expire_on
            ->format(config('dienstplan.date_format'));

        $this->actingByString($user)->get('/posts/edit');
        $response = $this->actingByString($user)->post('/posts', $post);

        if ($this->adminOnlyAssertions($response))
            return;

        $resultingPost = $this->post->attributesToArray();
        $resultingPost['user_id'] = Auth::id();
        array_forget($resultingPost, [ 'id', 'created_at', 'updated_at' ]);

        $response->assertRedirect('/posts/edit')->assertSessionMissing('errors');
        $this->assertDatabaseHas('posts', $resultingPost);
    }

    /**
     * @dataProvider userProvider
     * @depends testPostsEditReturnsOKOnlyIfAdmin
     */
    public function testDeletePostRedirectsBackWithoutErrorsDeletePerformedOnlyIfAdmin($user) {
        $this->actingByString($user)->get('/posts/edit');
        $response = $this->actingByString($user)->delete("/posts/{$this->post->id}");

        if ($this->adminOnlyAssertions($response))
            return;

        $response->assertRedirect('/posts/edit')->assertSessionMissing('errors');
        self::assertEquals(null, Post::find($this->post->id));
    }

}
