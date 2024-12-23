<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::query();

        if ($request->has('name')) {
           $products->where('name', 'like', '%' . $request->input('name') . '%');
        }

        $results = $products->get();

        if ($results->isEmpty()) {
          return response()->json([
            'error' => 'No Product found for the query'
          ], 404);
        }
        return response()->json($products->get(), 200);

    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'quantity' => 'required|integer',
            'category' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "message" => "Error!",
                "data" => $validator->errors()->all()
            ]);
        }

        $category = Category::firstOrCreate([
            'name' => $request->category
        ]);

        $product = Product::create([
            "name" => $request->name,
            "description" => $request->description,
            "price" => $request->price,
            "quantity" => $request->quantity,
            "category_id" => $category->id
        ]);

        return new ProductResource($product);
    }

    public function show(Product $product)
{
    try {
        return new ProductResource($product);
    } catch (ModelNotFoundException $e) {
        return response()->json([
            'error' => 'Product not found',
            'message' => 'The requested product does not exist.',
        ], 404);
    }
}

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'quantity'=> 'required|integer|min:0',
            'category' => 'nullable|string|max:255',
        ]);
        $product->update([
            'name' => $request->input('name'),
            'price' => $request->input('price'),
            'description' => $request->input('description'),
            'category' => $request->input('category')
        ]);

        return response()->json([
            "status" => 1,
            "message" => "Product Updated",
            "data" => new ProductResource($product)
        ]);
    }

    public function destroy(Product $product)
    {

        $product->delete();

        return response()->json([
            "status" => 1,
            "message" => "Product Deleted",
            "data" => $product
        ]);
    }
}
