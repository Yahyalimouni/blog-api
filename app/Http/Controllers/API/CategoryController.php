<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class CategoryController extends Controller
{   
    protected const VALIDCATEGORIES = [
        'CITY',
        'TOUR',
        'ACTS'
    ];

    protected const CATEGORYVALDIATIONS = [
        'id' => ['required','string','max:4','unique:categories'],
        'name' => 'required|string|max:255',
        'description' => 'nullable|string|max:1000',
    ];

    /**
     * Display a listing of the resource.
    */
    public function index()
    {
        try {
            $categories = Category::all() ?? [];

            if(empty($categories)) {
                return response()->json([
                    'status' => 201,
                    'message' => 'No categories found',
                    'data' => 'Unfound data'
                ]);
            }

            return response()->json([
                'status' => 200,
                'message' => "Data fetched successfully",
                'data' => $categories
            ]);

        } catch(\Exception $e) {
            ResponseHelper::setExceptionResponse($e);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        try {
            $validator = Validator::make($request->all(), self::CATEGORYVALDIATIONS);
        
            // If data not validated, return a 422 json error
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $validated = $validator->validated();

            $category = Category::create([
                'id' => strtoupper($validated['id']),
                'name' => $validated['name'],
                'description' => $validated['description']
            ]);

            return response()->json([
                'success' => true,
                'message' => "Category inserted succesfully",
                'data' => $category
            ]);


        } catch(\Exception $e) {
            return response()->json([
                'success' => false,
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        try {
            $category = Category::find($id);
    
            // If the category isn't found, return a 404
            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => "Category with id $id not found"
                ], 404);
            }
    
            return response()->json([
                'success' => true,
                'message' => 'Category fetched successfully ' . $id,
                'data' => $category,
            ], 200);
        } catch( Exception $e ) {
            return response()->json([
                'success' => false,
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
                'line' => $e->getLine()
            ], 422);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            // Data validation
            $validator = Validator::make($request->all(), [
                'id' => ['nullable','string','max:4','unique:categories'],
                'name' => ['nullable', 'string','max:255'],
                'description' => ['nullable', 'string', 'max:1000']
            ]);

            // Extra validation
            $validator->after(function ($validator) use ($request){
                if (
                    !filled($request->input('id')) &&
                    !filled($request->input('name')) &&
                    !filled($request->input('description'))
                ) {
                    $validator->errors()->add('fields', 'At least one of ID, Name or Description must be provided');
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

            // Get the category
            $category = Category::find($id);

            // If the category isn't found, return a 404
            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => "Category with id $id not found"
                ], 404);
            }

            // Update and save data
            $category->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Category updated successfully ' . $id,
                'data' => $category,
            ], 200);

        } catch(\Exception $e) {
            return response()->json([
                'success' => false,
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
                'line' => $e->getLine()
            ], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        try {
            $category = Category::find($id);
    
            // If the category isn't found, return a 404
            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => "Category with id $id not found"
                ], 404);
            }
    
            // Delete the category
            $category->delete();
    
            return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully ' . $id,
                'data' => null,
            ], 200);
        } catch( Exception $e ) {
            return response()->json([
                'success' => false,
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
                'line' => $e->getLine()
            ], 422);
        }
    }
}
