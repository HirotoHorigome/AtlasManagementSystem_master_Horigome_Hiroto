<?php

namespace App\Http\Controllers\Authenticated\BulletinBoard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Categories\MainCategory;
use App\Models\Categories\SubCategory;
use App\Models\Posts\Post;
use App\Models\Posts\PostComment;
use App\Models\Posts\Like;
use App\Models\Users\User;
use App\Http\Requests\BulletinBoard\PostFormRequest;
use Auth;
use DB;

class PostsController extends Controller
{
    public function show(Request $request)
    {
        $posts = Post::with('user', 'postComments', 'subCategories')->get();
        $posts = Post::with('subCategories')->get();
        $categories = MainCategory::with('SubCategories')->get();
        $like = new Like;
        $post_method = new Post;

        if (!empty($request->keyword)) {
            $posts = Post::with('user', 'postComments')
                ->where('post_title', 'like', '%' . $request->keyword . '%')
                ->orWhere('post', 'like', '%' . $request->keyword . '%')->get();
        } else if ($request->category_word) {
            $sub_category = $request->category_word;
            $sub_category_id_array = SubCategory::where('sub_category', "$sub_category")->get('id');
            $sub_category_id = $sub_category_id_array[0]->id;
            $posts = SubCategory::find($sub_category_id)->posts;
        } else if ($request->like_posts) {
            $likes = Auth::user()->likePostId()->get('like_post_id');
            $posts = Post::with('user', 'postComments')
                ->whereIn('id', $likes)->get();
        } else if ($request->my_posts) {
            $posts = Post::with('user', 'postComments')
                ->where('user_id', Auth::id())->get();
        }
        return view('authenticated.bulletinboard.posts', compact('posts', 'categories', 'like', 'post_method'));
    }

    public function postDetail($post_id)
    {
        $post = Post::with('user', 'postComments')->findOrFail($post_id);
        return view('authenticated.bulletinboard.post_detail', compact('post'));
    }

    public function postInput()
    {
        // withメソッドの引数にはモデルで定義したリレーションメソッド名を文字列で指定します。
        $main_categories = MainCategory::with('SubCategories')->get();
        return view('authenticated.bulletinboard.post_create', compact('main_categories'));
    }

    public function postCreate(PostFormRequest $request)
    {
        $post_get = Post::create([
            'user_id' => Auth::id(),
            'post_title' => $request->post_title,
            'post' => $request->post_body
        ]);
        $post = Post::findOrFail($post_get->id);
        // attach：中間テーブルにアクセスし、値の追加をするメソッド
        $post->subCategories()->attach($request->post_category_id);
        DB::commit();

        return redirect()->route('post.show');
    }

    public function postEdit(Request $request)
    {
        Post::where('id', $request->post_id)->update([
            'post_title' => $request->post_title,
            'post' => $request->post_body,
        ]);
        return redirect()->route('post.detail', ['id' => $request->post_id]);
    }

    public function postDelete($id)
    {
        Post::findOrFail($id)->delete();
        return redirect()->route('post.show');
    }
    public function mainCategoryCreate(Request $request)
    {
        if ($request->main_category_name != null) {
            MainCategory::create(['main_category' => $request->main_category_name]);
            return redirect()->route('post.input');
        }
        return redirect()->route('post.input');
    }

    public function subCategoryCreate(Request $request)
    {
        if ($request->main_category_id != null and $request->sub_category_name != null) {
            SubCategory::create(['main_category_id' => $request->main_category_id, 'sub_category' => $request->sub_category_name]);
            return redirect()->route('post.input');
        }
        return redirect()->route('post.input');
    }

    public function commentCreate(Request $request)
    {
        PostComment::create([
            'post_id' => $request->post_id,
            'user_id' => Auth::id(),
            'comment' => $request->comment
        ]);
        return redirect()->route('post.detail', ['id' => $request->post_id]);
    }

    public function myBulletinBoard()
    {
        $posts = Auth::user()->posts()->get();
        $like = new Like;
        return view('authenticated.bulletinboard.post_myself', compact('posts', 'like'));
    }

    public function likeBulletinBoard()
    {
        $like_post_id = Like::with('users')->where('like_user_id', Auth::id())->get('like_post_id')->toArray();
        $posts = Post::with('user')->whereIn('id', $like_post_id)->get();
        $like = new Like;
        return view('authenticated.bulletinboard.post_like', compact('posts', 'like'));
    }

    public function postLike(Request $request)
    {
        Auth::user()->likes()->attach($request->post_id);
        return response()->json();
    }

    public function postUnLike(Request $request)
    {
        Auth::user()->likes()->detach($request->post_id);
        return response()->json();
    }
}
