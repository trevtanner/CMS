<?php

namespace App\Http\Controllers;

use App\Http\Requests\Posts\CreatePostsRequest;
use App\Http\Requests\Posts\UpdatePostsRequest;
use App\Post;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PostsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return view('posts.index')->with('posts', Post::all());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('posts.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(CreatePostsRequest $request)
    {
    $image = $request->image->store('posts');


    Post::create([
        'title' => $request->title,
        'description' => $request->description,
        'content' => $request->content,
        'image' => $image,
        'published_at' =>$request->published_at,
    ]);

    session()->flash('success', 'Post created successfully.');

    return redirect(route('posts.index'));

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit(Post $post)
    {
        return view('posts.create')->with('post',$post);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  int  $id
     * @return Response
     */
    public function update(UpdatePostsRequest $request, Post $post)
    {
        $data = $request->only(['title', 'description', 'published_at', 'content']);

        if ($request->hasFile('image')){

            $image = $request->image->store('posts');

            $post->deleteImage();

            $data['image'] = $image;
        }

        $post->update($data);

        session()->flash('success', 'Post updated successfully');

        return redirect(route('posts.index'));


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {

        $post = Post::withTrashed()->where('id', $id)->firstorfail();
        if ($post->trashed()){

            $post->deleteImage();


            $post->forceDelete();
        }else {
            $post->delete();

        }

        session()->flash('success', 'Post deleted successfully.');

        return redirect(route('posts.index'));

    }

    /**
     * Display a list of trashed posts
     *
     *
     * @return Response
     */
    public function trashed()
    {
        $trashed = Post::onlyTrashed()->get();

        return view('posts.index')->withPosts($trashed);
    }

    public function restore($id)
    {
        $post = Post::withTrashed()->where('id', $id)->firstorfail();

        $post->restore();

        session()->flash('success', 'Post restored successfully');

        return redirect()->back();
    }
}
