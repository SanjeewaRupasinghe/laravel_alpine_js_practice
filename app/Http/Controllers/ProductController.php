<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{

    /**
     * Index page for products
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index(): View
    {
        $products = Product::all();
        return view('products.product-index', compact('products'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreProductRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreProductRequest $request): RedirectResponse
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

    /**
     * Generate a unique SKU for the product
     *
     * @return string
     */
    protected function generateSku(): string
    {
        do {
            $sku = Str::random(10);
        } while (Product::where('sku', $sku)->exists());

        return $sku;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateProductRequest  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
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
        $product->images()->whereNotIn('image', $existingImages)->get()
            ->each(function ($image) {
                Storage::disk('public')->delete($image->image);
                $image->delete();
            });

        // handle new images
        if ($hasNewImages) {
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
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Product $product): RedirectResponse
    {
        $product->images()->delete();
        $product->delete();
        return redirect()->back()->with('success', 'Product deleted successfully');
    }
}
