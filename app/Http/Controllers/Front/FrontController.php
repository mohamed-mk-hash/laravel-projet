<?php

namespace App\Http\Controllers\Front;

use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class FrontController extends Controller
{
    public function index()
    {
        $featuredProducts = Product::where('is_featured', 'Yes')
            ->where('status', 1)
            ->get();

        $latestProducts = Product::where('status', 1)
            ->orderBy('id', 'DESC')
            ->take(4)
            ->get();

        return response()->json([
            'status' => true,
            'featuredProducts' => $featuredProducts,
            'latestProducts' => $latestProducts,
        ], 200);  // 200 OK
    }

    public function addToWishlist(Request $request)
    {
        $user = Auth::guard('api')->user();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not authenticated'
            ]);
        }
    
        $product = Product::find($request->id);
        if ($product == null) {
            return response()->json([
                'status' => false,
                'message' => 'Product not found'
            ]);
        }

        Wishlist::updateOrCreate(
            [
                'user_id' => $user->id,
                'product_id' => $request->id
            ],
            [
                'user_id' => $user->id,
                'product_id' => $request->id
            ]
        );
    
        return response()->json([
            'status' => true,
            'message' => $product->title . ' added to your Wishlist'
        ]);
    }

    
    // public function index()
    // {
    //     $products = Product::where('is_featured','Yes')->where('status',1)->get();
    //     $data['featuredProducts'] = $products;
        
    //     $latestProducts = Product::orderBy('id','DESC')->where('status',1)->take(4)->get();
    //     $data['latestProducts'] = $latestProducts;

    //     return view('front.home',$data);
    // }

}
