<?php

use App\Http\Controllers\API\AuthenticationController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\CommentController;
use App\Http\Controllers\API\PostController;
use App\Http\Controllers\API\PostImageController;
use App\Http\Controllers\API\ProfileImageController;
use App\Http\Controllers\API\UserController;
use App\Http\Middleware\CheckGuard;
    use Illuminate\Support\Facades\Route;

    Route::group(['namespace' => 'App\Http\Controllers\API'], function () {
        // --------------- Register and Login ----------------//
        Route::post('register', [AuthenticationController::class, 'register'])->name('register');
        Route::post('login', [AuthenticationController::class, 'login'])->name('login');
        
        // --------------------------- ROUTES ----------------------//
        Route::middleware(['auth:sanctum', CheckGuard::class])->group(function () {
            
            // Auth routes
            Route::post('logout', [AuthenticationController::class, 'logout'])->name('logout');
            Route::get('userInfo', [AuthenticationController::class, 'userInfo'])->name('userInfo');


            /* ------------------------------------------- COMMENTS ---------------------------------------------- */

                /* ____ GETS _____ */
                    // -- Get all liked comments by the auth user 
                    // !! This route must be before the aapi Resource to prevent the show method trigger
                    Route::get("/comments/liked", [CommentController::class, 'getLikedCommentsByAuthUser'])->name('comments.liked');
                    
                    // -- Get a comment's replies
                    Route::get("/comments/{id}/replies", [CommentController::class, 'getCommentReplies'])->name('comments.replies');
                    
                    // -- Get a comment's likes
                    Route::get("/comments/{comment}/likes", [CommentController::class, 'getLikes'])->name('comments.likes');

                    // Get all the comments done by the current user 
                    Route::get("/comments/user", [CommentController::class, 'getCommentsForAuthUser'])->name('comments.user');


                            
                /* _____ POSTS _____ */ 
                    // -- Like a comment
                    Route::post("/comments/{comment}/like", [CommentController::class, 'like'])->name('comments.like');
                    
                    // -- Unlike a comment
                    Route::post("/comments/{comment}/unlike", [CommentController::class, 'like'])->name('comments.unlike');


                /* _____ DELETE _____*/
                    // -- Delete comment
                    Route::delete("/comments/{comment}", [CommentController::class, 'destroy'])->name('comments.destroy'); 
                
                /* _____ RESOURCE _____ */
                    Route::apiResource('comments', 'CommentController')->names([
                        'index' => 'comments.index',
                        'show' => 'comments.show',
                        'store' => 'comments.store',
                        'update' => 'comments.update',
                    ]);


            // --------------------------------------------- POSTS --------------------------------------------------
        
                /* _____ GETS _____ */
                    // -- Get a post's comments 
                    Route::get("/posts/{post_id}/comments", [PostController::class, 'getCommentsForPost'])->name('posts.comments');

                    // -- Get a post's likes number
                    Route::get("/posts/{post}/likes", [PostController::class, 'getLikes'])->name('posts.likes');

                    // -- Get all liked posts by the auth user 
                    // !! This route must be before the api Resource to prevent the show method trigger
                    Route::get("/posts/liked", [PostController::class, 'getLikedPostsByAuthUser'])->name('posts.liked');
                

                /* _____ POSTS _____ */
                    // Unlike post
                    Route::post("/posts/{post}/unlike", [PostController::class, 'unlike'])->name('posts.unlike');

                    // Like post
                    Route::post("/posts/{post}/like", [PostController::class, 'like'])->name('posts.unlike');

                
                /* _____ RESOURCE _____ */
                    Route::apiResource('posts', PostController::class)->names([
                        'index' => 'posts.index',
                        'show' => 'posts.show',
                        'store' => 'posts.store',
                        'update' => 'posts.update',
                        'destroy' => 'posts.destroy',
                    ]);


            // --------------------------------------------- USERS --------------------------------------------------
                // ______ PATCH _______
                // -- Update the current user data
                Route::patch("/users/{user}", [UserController::class, 'update'])->name('users.update');

                // -- Borrar
                Route::delete("/users/{user}", [UserController::class, 'destroy'])->name('users.destroy');

                // -- Predefined resource routes
                Route::apiResource('users', UserController::class)->names([
                    'index' => 'users.index',
                    'show' => 'users.show',
                    'destroy' => 'users.destroy',
                ]);
                

            // ------------------------------------------- CATEGORIES ------------------------------------------------

                Route::apiResource('categories', CategoryController::class)->names([
                    'index' => 'categories.index',
                    'show' => 'categories.show',
                    'store' => 'categories.store',
                    'update' => 'categories.update',
                    'destroy' => 'categories.destroy',
                ]);


            // ------------------------------------------ POST IMAGES ------------------------------------------------
            
            Route::apiResource('post_images', PostImageController::class)->names([
                'index'  => 'post_images.index',
                'show'   => 'post_images.show',
                'update' => 'post_images.update',
                'store'  => 'post_images.store',
            ]);


            // ----------------------------------------- PROFILE IMAGES ------------------------------------------------
                // _____ POSTS _____
                    // -- Upload auth user profile image
                    Route::post('/profile_images/upload', [ProfileImageController::class, "upload"])->name("profile_images.upload");

                // _____ GETS ______
                    // -- Get profile images of all users
                    Route::get('/profile_images', [ProfileImageController::class, "index"])->name("profile_images.index");

                    // -- Get profile image of the current autheticated user
                    Route::get('/profile_images/current', [ProfileImageController::class, "showCurrentUserProfileImage"])->name("profile_images.show");
        });
    });
?>
