<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckGuard
{
    protected array $allowedUserRoutes = [
        'posts' => [
            'posts.index',
            'posts.show',
            'posts.comments',
            'posts.unlike',
            'posts.like',
            'posts.liked',
            'posts.likes',
        ],
        'users' => [
            'users.index',
            'users.show',
            'users.destroy',
            'users.update',
            'users.comments'
        ],
        'comments' => [
            'comments.index',
            'comments.show',
            'comments.store',
            'comments.update',
            'comments.destroy',
            'comments.replies',
            'comments.unlike',
            'comments.like',
            'comments.liked'
        ],
        'post_images' => [
            'post_images.show'
        ],
        'auth' => [
            'userInfo',
            'logout'
        ],
        'profile_images' => [
            'profile_images.upload',
            'profile_images.show'
        ]
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get the auth user data
        $user = $request->user() ?? null;

        // If the user is null return a 403 error
        if( !$user ) {
            return response()->json([
                'response_code' => 403,
                'status'        => 'error',
                'message'       => 'Unauthorized access',
            ], 403);
        }

        // If the user is an admin allow all the endpoints and keep the request
        if ($user->is_admin == 1) {
            return $next($request);
        }

        // Get the current route name
        $routename = $request->route()->getName();

        // Set a tracker variable to check route existance in the available routes
        $routeFound = false;

        // Iterate over the array
        foreach ($this->allowedUserRoutes as $model => $routes) {
            if (in_array($routename, $routes)) {
                $routeFound = true;
            }
        }

        //  If found continue with the request
        if($routeFound == true) {
            return $next($request);
        }

        // If not found return a 403 error
        return response()->json([
            'response_code' => 403,
            'status'        => 'error',
            'message'       => 'Unauthorized access'
        ], 403);

        return $next($request);
    }
}
