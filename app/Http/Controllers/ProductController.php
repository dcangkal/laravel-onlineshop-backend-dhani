<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        // $products = DB::table('products')
        //     ->when($request->input('name'), function ($query, $name) {
        //         return $query->where('name', 'like', '%' . $name . '%');
        //     })
        //     ->orderBy('created_at', 'desc')
        //     ->paginate(10);

        $products = \App\Models\Product::paginate(5);
        $type_menu = 'product';
        return view('pages.products.index', compact('products', 'type_menu'));
    }

    public function create()
    {
        $categories = \App\Models\Category::all();
        $type_menu = 'product';
        return view('pages.products.create', compact('categories', 'type_menu'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|min:3|unique:products',
            'price' => 'required|integer',
            'stock' => 'required|integer',
            'image' => 'required|image|mimes:png,jpg,jpeg',
        ]);

        $filename = time() . '.' . $request->image->extension();
        $request->image->storeAs('public/products', $filename);
        // $data = $request->all();
        $product = new \App\Models\Product;
        $product->name = $request->name;
        $product->description = $request->description;
        $product->price = (int) $request->price;
        $product->stock = (int) $request->stock;
        $product->category_id = $request->category_id;
        $product->image = $filename;
        $product->save();
        return redirect()->route('product.index')->with('success', 'product berhasil dibuat');
    }

    public function edit($id)
    {
        $product = \App\Models\Product::findOrFail($id);
        $categories = \App\Models\Category::all();
        $type_menu = 'product';
        return view('pages.products.edit', compact('product', 'categories', 'type_menu'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|min:3|unique:products,name,' . $id,
            'image' => 'image|mimes:png,jpg,jpeg',
        ]);

        $product = \App\Models\Product::findOrFail($id);

        if ($request->hasFile('image')) {
            Storage::delete('public/products/' . $product->image);
            $filename = time() . '.' . $request->image->extension();
            $request->image->storeAs('public/products', $filename);
            $product->update([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'stock' => $request->stock,
                'category_id' => $request->category_id,
                'image' => $filename,
            ]);
        } else {
            $product->update([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'stock' => $request->stock,
                'category_id' => $request->category_id,
            ]);
        }
        return redirect()->route('product.index')->with('success', 'product berhasil diupdate');
    }

    public function destroy($id)
    {
        $product = \App\Models\Product::findOrFail($id);
        if ($product->image) {
            // Delete the old image (optional)
            Storage::delete('public/products/' . $product->image);
        }
        $product->delete();
        return redirect()->route('product.index')->with('success', 'product berhasil dihapus');
    }
}
