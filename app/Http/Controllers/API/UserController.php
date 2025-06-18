<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserUpdateRequest;
use App\Models\Comment;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all() ?? [];

        if(empty($users)) {
            return response()->json([
                'code' => 500,
                'status' => 'error',
                'message' => 'Unfound users'
            ], 500);
        }

        return response()->json([
            'code' => 200,
            'status' => "success",
            'message' => "Users Info fetched successfully",
            'data' => $users
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        try {
            $user = User::findOrFail($id);

            return response()->json([
                'response_code' => 200,
                'status'        => 'success',
                'message'       => 'User info fetched successfully',
                'data_user_info' => [
                    'id'       => $user->id,
                    'username' => $user->username,
                    'email'    => $user->email,
                ],
            ]);
        } catch (\Exception $e) {    
            return response()->json([
                'response_code' => 500,
                'status'        => 'error',
                'message'       => $e->getMessage() ?? 'User not found', 
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserUpdateRequest $request, User $user)
    {
        //
        try {

        } catch(Exception $e) {
            ResponseHelper::setExceptionResponse($e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }
}
