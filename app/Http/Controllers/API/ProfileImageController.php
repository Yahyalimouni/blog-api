<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileImage\ProfileImageRequest;
use App\Models\ProfileImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileImageController extends Controller
{
    public function index()
    {
        try {
            $profileImages = ProfileImage::all();

            if(!$profileImages) {
                return response()->json([
                    'success' => false,
                    'message' => "No profile images were found",
                    'dataFetched' => 0,
                    'data' => null
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => "Post image found",
                'dataFetched' => 1,
                'data' => $profileImages
            ], 200);
        } catch(\Exception $e) {
            return ResponseHelper::setExceptionResponse($e);
        }
    }


    // Show the current user profile image 
    public function showCurrentUserProfileImage() {
        $user = Auth::user();
        $profileImg = $user->profileImage;
        
        return response()->json([
            'success' => true, 
            'message' => "User profile image fetched successfully",
            'data' => $profileImg,
        ]);
    }

    //
    public function upload(ProfileImageRequest $request)
    {
        try {
            // Get the valdiated data by the personalized request 
            $request = $request->validated();

            // Get the uploaded image
            $image = $request['image'];
            $user = Auth::user();

            // Get old profile image
            $oldProfileImage = $user->profileImage;

            // dd($oldProfileImage);

            // Check it it exists to delete it
            if($oldProfileImage) {
                if(Storage::disk('public')->exists($oldProfileImage->path)) {
                    Storage::disk('public')->delete($oldProfileImage->path);
                }
            }

            // Set filename
            $filename = $user->id . '_' . time() . '_' . $image->getClientOriginalName();
            $newPath = $image->storeAs('profile_images/' . "{$user->id}", $filename, 'public');

            // Update or create the profile image record
            $user->profileImage()->updateOrCreate(
                ['user_id' => $user->id],
                ['path' => $newPath]
            );

            return response()->json([
                'success' => true,
                'message' => 'Profile image uploaded successfully',
                'data' => [
                    'path' => $newPath
                ]
            ], 201);
        } catch(\Exception $e) {
            return ResponseHelper::setExceptionResponse($e);
        }
    }
}
