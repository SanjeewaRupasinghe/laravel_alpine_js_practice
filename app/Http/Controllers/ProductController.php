<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\ProductImage;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $products = Product::all();
        return view('products.product-index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {}

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        $product = Product::create([
            'slug' => Str::slug($request->name),
            'sku' => $this->generateSku(),
            'name' => $request->name,
            'price' => $request->price,
            'status' => $request->status,
            'description' => $request->description,
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                // save image
                $path = $image->store('products', 'public');

                // save image path to database
                $product->images()->create([
                    'image' => $path,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Product created successfully');
    }

    protected function generateSku()
    {
        do {
            $sku = Str::random(10);
        } while (Product::where('sku', $sku)->exists());

        return $sku;
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $existingImages = $request->existingImages ?? [];
        $hasExistingImages = count($existingImages) > 0;
        $hasNewImages = $request->hasFile('images');

        $product->update($request->only([
            'name',
            'price',
            'status',
            'description',
        ]));

        // handle deleted images
        $product->images()->whereNotIn('image',$existingImages)->get()
        ->each(function($image){
            Storage::disk('public')->delete($image->image);
            $image->delete();
        });

        // handle new images
        if($hasNewImages){
            foreach ($request->file('images') as $image) {
                // save image
                $path = $image->store('products', 'public');

                // save image path to database
                $product->images()->create([
                    'image' => $path,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Product updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        //
    }
}
