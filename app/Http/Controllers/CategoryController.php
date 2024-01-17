<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = DB::table('categories')
            ->when($request->input('name'), function ($query, $name) {
                return $query->where('name', 'like', '%' . $name . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(3);
        $type_menu = 'category';
        // $categories = \App\Models\Category::paginate(5);
        return view('pages.category.index', compact('categories', 'type_menu'));
    }

    public function create()
    {
        $categories = \App\Models\Category::all();
        $type_menu = 'category';
        return view('pages.category.create', compact('categories', 'type_menu'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|min:3|unique:categories',
            'image' => 'required|image|mimes:png,jpg,jpeg',
        ]);

        $filename = time() . '.' . $request->image->extension();
        $request->image->storeAs('public/categories', $filename);
        $category = new \App\Models\Category;
        $category->name = $request->name;
        $category->description = $request->description;
        $category->image = $filename;
        $category->save();
        return redirect()->route('category.index')->with('success', 'category berhasil dibuat');
    }

    public function edit($id)
    {
        $category = \App\Models\Category::findOrFail($id);
        $type_menu = 'category';
        return view('pages.category.edit', compact('category', 'type_menu'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|min:3|unique:categories,name,' . $id,
            'image' => 'image|mimes:png,jpg,jpeg',
        ]);

        $category = \App\Models\Category::findOrFail($id);

        // Handle image upload if a new image is provided
        if ($request->hasFile('image')) {
            // Delete the old image (optional)
            Storage::delete('public/categories/' . $category->image);

            // Upload the new image
            $filename = time() . '.' . $request->image->extension();
            $request->image->storeAs('public/categories', $filename);

            // Update the category with the new image
            $category->update([
                'name' => $request->name,
                'description' => $request->description,
                'image' => $filename,
            ]);
        } else {
            // Update the category without changing the image
            $category->update([
                'name' => $request->name,
                'description' => $request->description,
            ]);
        }

        return redirect()->route('category.index')->with('success', 'Category berhasil diupdate');
    }

    public function destroy($id)
    {
        $category = \App\Models\Category::findOrFail($id);
        if ($category->image) {
            // Delete the old image (optional)
            Storage::delete('public/categories/' . $category->image);
        }
        $category->delete();
        return redirect()->route('category.index')->with('success', 'category berhasil dihapus');
    }
}
