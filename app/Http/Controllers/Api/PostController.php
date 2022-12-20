<?php

namespace App\Http\Controllers\Api;

use App\Traits\ResponseAPI;
use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\User;
use App\Constants\ResponseMessages;
use App\Http\Resources\PostResource;
use App\Http\Requests\PostRequest;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PostController extends Controller
{
    use ResponseAPI;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth('api')->user();
        $posts = Post::publish();
        if ($user->hasRole(User::USER_ROLE_WRITER)) {
            $posts = $posts->whereAuthorId($user->id);
        }

        $posts = $posts->latest()->get();
        return $this->sendResponse(PostResource::collection($posts), ResponseMessages::RESPONSE_API_INDEX, Response::HTTP_OK);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\PostRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PostRequest $request)
    {
        $request->request->add(['author_id' => auth('api')->user()->id]);
        $post = Post::create($request->all());

        return $this->sendResponse(new PostResource($post), ResponseMessages::RESPONSE_API_CREATE, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        return $this->sendResponse(new PostResource($post), ResponseMessages::RESPONSE_API_INDEX, Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\PostRequest  $request
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(PostRequest $request, Post $post)
    {
        $user = auth('api')->user();
        if ($user->can('delete', $post)) {
            $post->update($request->all());
            return $this->sendResponse(new PostResource($post), ResponseMessages::RESPONSE_API_UPDATE, Response::HTTP_OK);
        } else {
            return $this->sendError([], "This action is unauthorized.", Response::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        $user = auth('api')->user();
        if ($user->can('delete', $post)) {
            $post->delete();
            return $this->sendResponse([], ResponseMessages::RESPONSE_API_DELETE, Response::HTTP_NO_CONTENT);
        } else {
            return $this->sendError([], "This action is unauthorized.", Response::HTTP_UNAUTHORIZED);
        }
    }
}
