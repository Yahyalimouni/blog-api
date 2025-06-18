<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Comment\StoreRequest;
use App\Models\Comment;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Auth as FacadesAuth;

class CommentController extends Controller
{
    // Show all comments
    public function index()
    {
        // Logic to get all comments
        return response()->json([
            'response_code' => 200,
            'status'        => 'success',
            'data'          => Comment::all(),
        ]);
    }


    // Get a specific comment replies
    public function getCommentReplies($id) {
        try {
            $comment = Comment::find($id);
            $replies = $comment->replies;

            return response()->json([
                'success' => true,
                'message' => 'Comment ' . $id . ' replies fetched successfully',
                'data' => $replies
            ]);
        } catch(\Exception $e) {
            ResponseHelper::setExceptionResponse($e);
        }
    }


    // Increment a comments likes
    public function like(Comment $comment)
    {
        try {
            $user = Auth::user();
            $userAlreadyLiked = $comment->likedUsers()->where('user_id', $user->id)->exists();

            if ($userAlreadyLiked) {
                return response()->json([
                    'success' => false,
                    'message' => 'Already liked this comment.',
                ], 409);
            }

            // Add w row in the comment_user table with the user_id and comment liked
            $comment->likedUsers()->attach($user->id);
            // Incrementing likes
            $comment->increment('likes');
        
            return response()->json([
                'success' => true,
                'message' => 'Comment liked!',
                'likes'   => $comment->likes
            ]);
        } catch(\Exception $e) {
            ResponseHelper::setExceptionResponse($e);
        }
    }


    // Decrement a comment likes
    public function unlike(Comment $comment)
    {
        try {
            $user = Auth::user();
    
            // Check if the user had liked this comment
            if (! $comment->likedUsers()->where('user_id', $user->id)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have not liked this comment.',
                ], 409);
            }
    
            // Remove the like and decrease the like count
            $comment->likedUsers()->detach($user->id); // remove from pivot table
            $comment->decrement('likes');
    
            return response()->json([
                'success' => true,
                'message' => 'Comment unliked!',
                'likes'   => $comment->likes,
            ]);
        } catch (\Exception $e) {
            ResponseHelper::setExceptionResponse($e);
        }
    }


    // Get the likes of a comment
    public function getLikes(Comment $comment)
    {
        try {
            return response()->json([
                'success' => true,
                'message' => 'Likes fetched successfully for the comment: ' . $comment->id,
                'data' => [
                    'likes' => $comment->likes
                ]
            ]);
        } catch (\Exception $e) {
            ResponseHelper::setExceptionResponse($e);
        }
    }    


    // Get the comments liked by the authenticated user
    public function getLikedCommentsByAuthUser()
    {
        try {
            // Get the auth user instance
            $user = Auth::user();

            return response()->json([
                'success' => true,
                'message' => 'Liked comments by the user: ' . $user->id . ' have been fetched successfully',
                'data' => [
                    'comments' => $user->likedComments // Attach the liked comments by the auth user
                ]
            ]);
        } catch (\Exception $e) {
            ResponseHelper::setExceptionResponse($e);
        }
    }

    // Get all comments of a user
    public function getCommentsForAuthUser()
    {
        try {
            $comments = Comment::with([
                'user:id,name', 
                'post:id,title'
            ])
            ->where('user_id', Auth::user()->id)->get();

            return response()->json([
                'success' => true,
                'message' => "Comments fetched succesfully",
                'data' => $comments
            ]);
        } catch( \Exception $e) {
            ResponseHelper::setExceptionResponse($e);
        }
    }


    // Add new comment
    public function store(StoreRequest $request) {
        // Get the validated data from the personalized request
        $validated = $request->validated();
        
        try {
            // Get the user sending the request to fetch it's id: 
            $user = Auth::user();
            
            // Push the user_id to the validated array
            $validated['user_id'] = $user->id;
            
            // Create a comment row with the validated data
            $comment = Comment::create($validated);

            // Return response
            return response()->json([
                'success' => true,
                'message' => "Comment added succesfully by: " . $user->name,
                'data' => Comment::find($comment->id) 
            ], 201);
            
        } catch(Exception $e) {
            ResponseHelper::setExceptionResponse($e);
        } 
    }

    public function destroy(Comment $comment) 
    {   
        try {
            // Delete comment
            $comment->delete();

            return response()->json([
                'success' => true,
                'message' => 'The comment: ' . $comment->id . ' has been deleted successfully',
            ]);
        } catch (\Exception $e) {
            ResponseHelper::setExceptionResponse($e);
        }
    }
}
