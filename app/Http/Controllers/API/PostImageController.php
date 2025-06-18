<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\PostImage\UpdateRequest;
use App\Models\PostImage;
use Exception;
use Illuminate\Database\Query\Processors\PostgresProcessor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PostImageController extends Controller
{
    public function index() {
        try {
            // Get all post images
            $postImages = PostImage::all();

            // Set the default response message
            $message = "Post images fetched successfully";
            
            // Update the response message value if no post images found
            if(empty($postImages)) {
                $message = "No available post images";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'fetchedImages' => count($postImages),
                'data' => $postImages
            ], 200);

        } catch(\Exception $e) {
            return ResponseHelper::setExceptionResponse($e);        }
    }

    public function show(string $id) {
        try {
            $postImage = PostImage::find($id);

            if(!$postImage) {
                return response()->json([
                    'success' => false,
                    'message' => "No post image was found with the id " . "'$id'",
                    'dataFetched' => 0,
                    'data' => null
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => "Post image found",
                'dataFetched' => 1,
                'data' => $postImage
            ], 200);

        } catch(\Exception $e) {
            return ResponseHelper::setExceptionResponse($e);
        }
    }


    public function update(UpdateRequest $request, string $id) {
        // Get the Post Image row
        $postImage = PostImage::find($id);

        // If not found return an error response
        if(!$postImage) {
            return response()->json([
                'success' => false,
                'message' => "Unfound post with the id " . "'$id'",
                'data' => null
            ], 404);
        }

        try {
            // Get the validated data obtained from the personalized request
            $validated = $request->validated();

            // Get the image original name
            $filename = $validated['image']->getClientOriginalName();

            // Get the currently saved image path
            $imagepath = $postImage->path;

            // If exists delete it otherwise return a json error reponse 
            if (Storage::disk('public')->exists($imagepath)) {
                Storage::disk('public')->delete($imagepath);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => "Image not found.",
                ], 404);
            }

            // Store the new image and save it's path
            $newPath = $validated['image']->storeAs("post_images/$id", $filename, "public");


            $postImage->update([
                "path" => $newPath
            ]);

            // If no exception thrown, return a response with the update message
            return response()->json([
                'success' => true,
                'message' => "The post image with the id " . "'$id'" . " has been updated",
                'data' => PostImage::find($id)
            ], 201);

        } catch(\Exception $e) {
            return ResponseHelper::setExceptionResponse($e);
        }
    }
}
