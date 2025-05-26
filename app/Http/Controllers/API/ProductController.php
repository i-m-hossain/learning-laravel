<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Events\ProductCreated;
use App\Events\ProductUpdated;
use App\Events\ProductDeleted;
use App\Services\Discount\DiscountCalculator;
use App\Services\Discount\Factories\DiscountStrategyFactory;
// use App\Services\Discount\Strategies\BuyXGetYDiscountStrategy;
// use App\Services\Discount\Strategies\FixedAmountDiscountStrategy;
// use App\Services\Discount\Strategies\PercentageDiscountStrategy;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    private DiscountCalculator $discountCalculator;

    public function __construct(DiscountCalculator $discountCalculator)
    {
        $this->discountCalculator = $discountCalculator;
    }
    /**
     * Display a listing of the products.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::with('category')->get();
        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    /**
     * Store a newly created product in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|string',
        ]);

        $product = Product::create($validated);
        
        // Trigger the product created event (Observer pattern)
        // ProductCreated::dispatch($product);

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully',
            'data' => $product
        ], 201);
    }

    /**
     * Display the specified product.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        return response()->json([
            'success' => true,
            'data' => $product->load('category')
        ]);
    }

    /**
     * Update the specified product in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'price' => 'sometimes|numeric|min:0',
            'category_id' => 'sometimes|exists:categories,id',
            'stock' => 'sometimes|integer|min:0',
            'image' => 'nullable|string',
        ]);

        $product->update($validated);
        
        // Trigger the product updated event (Observer pattern)
        // ProductUpdated::dispatch($product);

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully',
            'data' => $product
        ]);
    }

    /**
     * Remove the specified product from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        // Trigger the product deleted event (Observer pattern)
        // ProductDeleted::dispatch($product);
        
        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully'
        ]);
    }

    public function getProductWithDiscount(Request $request, $productId ):JsonResponse
    {
        $product =Product::findOrFail($productId);
        $quantity = $request->input('quantity', 1);

        $promoCode = $request->input('promoCode');
        $strategy = (new DiscountStrategyFactory())->createFromPromotionCode($promoCode);
        $discountAmount = $strategy->calculate($product->price);
        
        return response()->json([
            'product' => $product,
            'original_price' => $product->price,
            'discount_description' => $this->discountCalculator->getDescription(),
            'discount_amount' => $discountAmount,
            'final_price' =>  $product->price- $discountAmount,
            'quantity' => $quantity,
            'total' => $discountAmount * $quantity
        ]);
    }

    
}