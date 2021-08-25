<?php

namespace App\Http\Controllers\Admin;

use App\Models\Tag;
use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Admin\Post\EditPostRequest;
use App\Http\Requests\Admin\Post\CreatePostRequest;
use Faker\Guesser\Name;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::with('category')->paginate(20);

        return view('admin.posts.index', compact('posts'));
      
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::all()->pluck('name','id');

        return view('admin.posts.create',compact('categories'));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store( CreatePostRequest $request)
    {
        if($request->hasFile('image')){
            $filename = now().'_'.$request->file('image')->getClientOriginalName();
            $request->file('image')->storeAs('uploads',$filename,'public');
        }
      $post =  Post::create([
             'title'  => $request->title,
             'image' => $filename ?? null,
              'post' => $request->post,
              'category_id' => $request->category,
        ]);
         
        $tag = Tag::create([
            'name'=>$request->tags
        ]);
        $post->tags()->attach($tag);

        return redirect()->route('admin.posts.index');

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        $categories = Category::all()->pluck('name','id');
        $tags = $post->tags->implode('name',',');

        return view('admin.posts.edit',compact('post','categories','tags'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(EditPostRequest $request, Post $post)
    {


        if($request->has('image'))
        {
            Storage::delete('public/uploads/'.$post->image);

            $filename = now().'_'. $request->file('image')->getClientOriginalName();
            $request->file('image')->storeAs('uploads',$filename,'public');
        }

        $post->update([
            'title'=> $request->title,
             'image'=> $filename ?? $post->image,
             'post'=> $request->post,
             'category_id' => $request->category
             
        ]);
        $post->tags()->update(["name"=>$request->tags]);
       

        return redirect()->route('admin.posts.index');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        if ($post->image) {
            Storage::delete('public/uploads/' . $post->image);
        }

      
        $post->tags()->detach();
        $post->delete();
    

        return redirect()->route('admin.posts.index');


    }
}
