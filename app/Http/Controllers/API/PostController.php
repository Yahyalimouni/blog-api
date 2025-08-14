<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// Facades
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

// Other
use Illuminate\Validation\Rule;
use App\Helpers\ResponseHelper;
use App\Models\Category;
use App\Models\Comment;

// Models
use App\Models\Post;
use App\Models\PostImage;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function index(Request $request)
    {
        try {
            $categoryId = $request->query('category');
    
            // Start with base query
            $query = Post::query();
    
            // If category parameter exists and is valid, filter posts
            if ($categoryId && Category::where('id', $categoryId)->exists()) {
                $query->where('category_id', $categoryId);
            }
    
            $posts = $query->get();
    
            if ($posts->isEmpty()) {
                return response()->json([
                    'status' => 201,
                    'message' => 'No posts found',
                    'data' => 'Unfound data'
                ]);
            }
    
            return response()->json([
                'status' => 200,
                'message' => "Data fetched successfully",
                'data' => $posts
            ]);
    
        } catch(\Exception $e) {
            return ResponseHelper::setExceptionResponse($e);
        }
    }

    // Increment a post's likes
    public function like(Post $post)
    {
        try {
            $user = Auth::user();
            $userAlreadyLiked = $post->likedUsers()->where('user_id', $user->id)->exists();

            if ($userAlreadyLiked) {
                return response()->json([
                    'success' => false,
                    'message' => 'Already liked this post.',
                ], 409);
            }

            // Add w row in the post_user table with the user_id and post liked
            $post->likedUsers()->attach($user->id);
            // Incrementing likes
            $post->increment('likes');
        
            return response()->json([
                'success' => true,
                'message' => 'Post liked!',
                'likes'   => $post->likes
            ]);
        } catch(\Exception $e) {
            ResponseHelper::setExceptionResponse($e);
        }
    }


    // Decrement a post likes
    public function unlike(Post $post)
    {
        try {
            $user = Auth::user();
    
            // Check if the user had liked this post
            if (! $post->likedUsers()->where('user_id', $user->id)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have not liked this post.',
                ], 409);
            }
    
            // Remove the like and decrease the like count
            $post->likedUsers()->detach($user->id); // remove from pivot table
            $post->decrement('likes');
    
            return response()->json([
                'success' => true,
                'message' => 'post unliked!',
                'likes'   => $post->likes,
            ]);
        } catch (\Exception $e) {
            ResponseHelper::setExceptionResponse($e);
        }
    }


    // Get the likes of a post
    public function getLikes(Post $post)
    {
        try {
            return response()->json([
                'success' => true,
                'message' => 'Likes fetched successfully for the post: ' . $post->id,
                'data' => [
                    'likes' => $post->likes
                ]
            ]);
        } catch (\Exception $e) {
            ResponseHelper::setExceptionResponse($e);
        }
    }    


    // Get the posts liked by the authenticated user
    public function getLikedPostsByAuthUser()
    {
        try {
            // Get the auth user instance
            $user = Auth::user();

            return response()->json([
                'success' => true,
                'message' => 'Liked posts by the user: ' . "'$user->name'" . ' have been fetched successfully',
                'data' => [
                    'posts' => $user->likedPosts // Attach the liked posts by the auth user
                ]
            ]);
        } catch (\Exception $e) {
            ResponseHelper::setExceptionResponse($e);
        }
    }

    // Get a post's comments
    public function getCommentsForPost($post_id) 
    {
        try {
            $comments = Comment::with([
                'user:id,name', 
                'post:id,title'
            ])
            ->where('post_id', $post_id)->get();

            return response()->json([
                'success' => true,
                'message' => 'Data fetched succesfully',
                'comments' => $comments
            ], 200);
        } catch(\Exception $e) {
            ResponseHelper::setExceptionResponse($e);
        }
    }

    // Show a post
    public function show(string $id)
    {
        try {
            $post = Post::find($id);

            if(!$post) {
                return response()->json([
                    'success' => false,
                    'message' => 'No posts found',
                    'data' => 'Unfound data'
                ], 404);
            }

            return response()->json([
                'status' => 200,
                'message' => "Data fetched successfully",
                'data' => $post
            ]);

        } catch(\Exception $e) {
            return ResponseHelper::setExceptionResponse($e);
        }
    }

    // Update a post
    public function update(Request $request, string $id)
    {
        try {
            // Get Admins IDs
            $adminIds = User::where('is_admin', true)->pluck('id')->toArray();

            // Set the validator
            $validator = Validator::make($request->all(), [
                'user_id'       => ['nullable', 'integer', Rule::in($adminIds)],
                'category_id'   => ['nullable','string','max:4','unique:categories', 'exists:categories,id'],
                'title'         => ['nullable', 'string','max:255'],
                'description'   => ['nullable', 'string', 'max:1000'],
                'content'       => ['nullable', 'string', 'max:2000'],
                'image'         => ['required', 'image', 'mimes:jpeg,jpg,webp', 'max:2048']
            ]);

            // Extra validation, validating that at least one of the columns is set
            $validator->after(function($validator) use ($request) {
                if(!collect($request->only(['user_id', 'category_id', 'title', 'description', 'content', 'image']))
                    ->filter()
                    ->isNotEmpty()) {
                    $validator->errors()->add('fields', 'At least one field must be provided.');    
                }
            });

            // If a data isn't validated returns a 422
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            // Get validated Data
            $validated = $validator->validated();

            // Get the Post or Post image to modify
            $post = Post::find($id);

            // If the post isn't found, return a 404
            if (!$post) {
                return response()->json([
                    'success' => false,
                    'message' => "Post with id '$id' not found"
                ], 404);
            }

            // Update and save data
            $post->update($validated);

            // Return data
            return response()->json([
                'success' => true,
                'message' => 'Post updated successfully: "' . $id . '"',
                'data' => $post,
            ], 200);

        } catch(\Exception $e) {
            return ResponseHelper::setExceptionResponse($e);
        }
    }

    // Add a post
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id'   => ['nullable','string','max:4', 'exists:categories,id'],
            'title'         => ['required', 'string','max:255'],
            'description'   => ['required', 'string', 'max:1000'],
            'content'       => ['nullable', 'string', 'max:2000'],
            'image'         => ['required', 'image', 'mimes:jpeg,jpg,webp', 'max:2048']
        ]);

        // If a data isn't validated, returns a 422
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Get validated Data
        $validated = $validator->validated();

        // Inject the user_id retrieved from the request, in the validated data
        $validated['user_id'] = (int)$request->user()->id;

        // |----------------- BEGIN TRANSACTION -------------------| //
        DB::beginTransaction();

        try {
            // Create a post
            $post = Post::create($validated);
            $post = Post::latest()->first();

            $filename = $validated['image']->getClientOriginalName();

            // Store the image file
            $imagePath = $request->file('image')->storeAs("post_images/" . $post->id, $filename, "public"); 

            // Create a PostImage row
            $postimage = PostImage::create([
                'post_id' => $post->id,
                'path' => $imagePath,
            ]);

            // |----------------- END TRANSACTION --------------------|
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Post created successfully: "' . $post->id . '"',
                'data' => [
                    'post' => $post,
                    'path' => $postimage->path,
                ]
            ], 201);

        } catch(\Exception $e) {
            DB::rollBack();
            return ResponseHelper::setExceptionResponse($e);
        }
    }

    // Delete a post
    public function destroy(Request $request, string $id) {
        $post = Post::find($id);
    
        if (!$post) {
            return response()->json([
                'success' => false,
                'message' => "Post not found"
            ], 404);
        }
    
        try {
            // Delete the images folder for this post
            Storage::disk('public')->deleteDirectory("post_images/{$id}");
    
            // Delete the post, which triggers cascade delete of DB records
            $post->delete();
    
            return response()->json([
                'success' => true,
                "message" => "Post with id {$id} and its images have been deleted successfully"
            ]);
        }
        catch (Exception $e) {
            ResponseHelper::setExceptionResponse($e);
        }
    }
}
