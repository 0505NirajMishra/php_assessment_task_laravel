<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        return view('products.index');
    }

    public function fetch()
    {
        return response()->json([
            'data' => Product::latest()->get()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_name' => 'required|string|max:255',
            'product_price' => 'required|numeric',
            'product_description' => 'required|string',
            'product_image' => 'required|array',
            'product_image.*' => 'image|mimes:jpg,jpeg,png,webp,jfif'
        ]);

        $images = [];

        if ($request->hasFile('product_image')) {
            foreach ($request->file('product_image') as $image) {
                $name = time().'_'.$image->getClientOriginalName();
                $image->move(public_path('uploads/products'), $name);
                $images[] = $name;
            }
        }

        Product::create([
            'product_name' => $request->product_name,
            'product_price' => $request->product_price,
            'product_description' => $request->product_description,
            'product_image' => $images
        ]);

        return response()->json(['success' => true]);
    }

    public function edit($id)
    {
        return response()->json(Product::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'product_name' => 'required|string|max:255',
            'product_price' => 'required|numeric',
            'product_description' => 'required|string',
            'product_image' => 'nullable|array',
            'product_image.*' => 'image|mimes:jpg,jpeg,png,webp,jfif'
        ]);

        $product = Product::findOrFail($id);
        $images = $product->product_image ?? [];

        if ($request->hasFile('product_image')) {
            foreach ($request->file('product_image') as $image) {
                $name = time().'_'.$image->getClientOriginalName();
                $image->move(public_path('uploads/products'), $name);
                $images[] = $name;
            }
        }

        $product->update([
            'product_name' => $request->product_name,
            'product_price' => $request->product_price,
            'product_description' => $request->product_description,
            'product_image' => $images
        ]);

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        Product::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }
}
