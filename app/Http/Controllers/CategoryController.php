<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{

    public function index(Request $request)
    {
        $category = Category::query();

        if ($request->has('name')) {
           $category->where('name', 'like', '%' . $request->input('name') . '%');
        }

        $results = $category->get();

        if ($results->isEmpty()) {
          return response()->json([
            'error' => 'No Category found for the query'
          ], 404);
        }
        return response()->json($category->get(), 200);

    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
           'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "message" => "Error!",
                "data" => $validator->errors()->all()
            ]);
         }

         $product = Category::create([
            "name" => $request->name,
         ]);

         return new CategoryResource($product);

    }


    public function show(Category $category)
    {
        return new CategoryResource($category);
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category->update([
            'name' => $request->input('name')
        ]);

        return response()->json([
            "status" => 1,
            "message" => "Category Updated",
            "data" => new CategoryResource($category)
        ]);

    }

    public function destroy(Category $category)
    {
        $category->delete();

        return response()->json([
            "status" => 1,
            "message" => "Category Deleted",
            "data" => $category
         ]);
    }
}
