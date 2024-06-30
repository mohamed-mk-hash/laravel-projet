<?php

namespace App\Http\Controllers\Front;

use App\Models\Product;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ShopeController extends Controller
{
    public function index(Request $request, $categorySlug = null, $subCategorySlug = null)
    {
        $categories = Category::orderBy('name', 'ASC')
            ->with('sub_category')
            ->where('status', 1)
            ->get();
    
        $productsQuery = Product::with('product_images')
            ->where('status', 1)
            ->latest('id');
    
        $categorySelected = null;
        $subCategorySelected = null;
    
        if (!empty($categorySlug)) {
            $category = Category::where('slug', $categorySlug)->first();
            if ($category) {
                $productsQuery = $productsQuery->where('category_id', $category->id);
                $categorySelected = $category->id;
            }
        }
    
        if (!empty($subCategorySlug)) {
            $subCategory = SubCategory::where('slug', $subCategorySlug)->first();
            if ($subCategory) {
                $productsQuery = $productsQuery->where('sub_category_id', $subCategory->id);
                $subCategorySelected = $subCategory->id;
            }
        }
    
        if ($request->has('price_min') && $request->has('price_max')) {
            $minPrice = intval($request->get('price_min'));
            $maxPrice = intval($request->get('price_max'));
    
            if ($minPrice >= 0 && $maxPrice > 0) {
                $productsQuery = $productsQuery->whereBetween('price', [$minPrice, $maxPrice]);
            }
        }
    
        if ($request->get('sort') != '') {
            if ($request->get('sort') == 'latest') {
                $productsQuery = $productsQuery->orderBy('id', 'DESC');
            } elseif ($request->get('sort') == 'price_asc') {
                $productsQuery = $productsQuery->orderBy('price', 'ASC');
            } else {
                $productsQuery = $productsQuery->orderBy('price', 'DESC');
            }
        } else {
            $productsQuery = $productsQuery->orderBy('id', 'DESC');
        }
    
        $products = $productsQuery->get();
    
        return response()->json([
            'status' => true,
            'categories' => $categories,
            'products' => $products,
            'categorySelected' => $categorySelected,
            'subCategorySelected' => $subCategorySelected,
            'priceMax' => $request->get('price_max', 1000),
            'priceMin' => $request->get('price_min', 0),
            'sort' => $request->get('sort')
        ], 200);  // 200 OK
    }
    

    // product details & related product
    public function product($slug)
    {
        $product = Product::where('slug', $slug)
            ->with('product_images')
            ->first();

        if ($product == null) {
            return response()->json([
                'status' => false,
                'message' => 'Product not found'
            ], 404);  // 404 Not Found
        }

        $relatedProducts = [];
        if (!empty($product->related_products)) {
            $productArray = array_filter(explode(',', $product->related_products));
            $relatedProducts = Product::whereIn('id', $productArray)
                ->with('product_images')
                ->get();
        }

        return response()->json([
            'status' => true,
            'product' => $product,
            'relatedProducts' => $relatedProducts
        ], 200);  // 200 OK
    }


























//     public function index(Request $request, $categorySlug = null, $subCategorySlug = null)
//     {
//         $categories = Category::orderBy('name', 'ASC')->with('sub_category')->where('status', 1)->get();
//         $products = Product::where('status', 1);

//         $categorySelected = null;
//         $subCategorySelected = null;

//         if (!empty($categorySlug)) {
//             $category = Category::where('slug', $categorySlug)->first();
//             $products = $products->where('category_id', $category->id);
//             $categorySelected = $category->id;
//         }

//         if (!empty($subCategorySlug)) {
//             $subCategory = SubCategory::where('slug', $subCategorySlug)->first();
//             $products = $products->where('sub_category_id', $subCategory->id);
//             $subCategorySelected = $subCategory->id;
//         }

//         if ($request->has('price_min') && $request->has('price_max')) {
//             $minPrice = intval($request->get('price_min'));
//             $maxPrice = intval($request->get('price_max'));

//             if ($minPrice >= 0 && $maxPrice > 0) {
//                 $products = $products->whereBetween('price', [$minPrice, $maxPrice]);
//             }
//         }

//         if($request->get('sort') != ''){
//             if($request->get('sort') == 'latest'){
//                 $products = $products->orderBy('id','DESC');
//             }elseif($request->get('sort') == 'price_asc'){
//                 $products = $products->orderBy('price','ASC');
//             }else{
//                 $products = $products->orderBy('price','DESC');
//             }
//         }else{
//             $products = $products->orderBy('id','DESC');
//         }
        
//         $products = $products->orderBy('id', 'DESC')->get();

//         $data['categories'] = $categories;
//         $data['products'] = $products;
//         $data['categorySelected'] = $categorySelected;
//         $data['subCategorySelected'] = $subCategorySelected;
//         $data['priceMax'] = $request->get('price_max', 1000);
//         $data['priceMin'] = $request->get('price_min', 0);
//         $data['sort'] = $request->get('sort');

//         return view('front.shope', $data);
//     }

//     public function product($slug){
//         $product = Product::where('slug',$slug)->with('product_images')->first();
//         if($product == null){
//             abort(404);
//         }

//         $relatedProducts = [];
//         //fetsh related product
//         if (!empty($product->related_products)) {
//             $productArray = array_filter(explode(',', $product->related_products));
//             $relatedProducts = Product::whereIn('id', $productArray)->with('product_images')->get();
//         }

//         $data['product'] = $product;
//         $data['relatedProducts'] = $relatedProducts;

//         return view('front.product',$data);
//     }
}
