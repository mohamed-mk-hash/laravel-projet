<?php

namespace App\Http\Controllers\Front;

use App\Models\Order;
use App\Models\Country;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use App\Models\CustomerAddress;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    // public function addToCart(Request $request)
    // {
    //     $product = Product::with('product_images')->find($request->id);

    //     if ($product == null) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Product not Found',
    //         ], 404); // 404 Not Found
    //     }

    //     $cartContent = Cart::content();
    //     $productAlreadyExist = false;

    //     foreach ($cartContent as $item) {
    //         if ($item->id == $product->id) {
    //             $productAlreadyExist = true;
    //             break;
    //         }
    //     }

    //     if (!$productAlreadyExist) {
    //         Cart::add($product->id, $product->titel, 1, $product->price, [
    //             'productImage' => $product->product_images->first() ?? ''
    //         ]);

    //         $status = true;
    //         $message = $product->titel . ' added to cart';
    //     } else {
    //         $status = false;
    //         $message = $product->titel . ' already added to cart';
    //     }

    //     return response()->json([
    //         'status' => $status,
    //         'message' => $message,
    //     ], 200); // 200 OK
    // }

    // public function cart()
    // {
    //     $cartContent = Cart::content();

    //     return response()->json([
    //         'status' => true,
    //         'cartContent' => $cartContent,
    //     ], 200); // 200 OK
    // }

    // public function updateCart(Request $request)
    // {
    //     $rowId = $request->rowId;
    //     $qty = $request->qty;
    //     $itemInfo = Cart::get($rowId);

    //     if ($itemInfo == null) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Item not found in cart',
    //         ], 404); // 404 Not Found
    //     }

    //     $product = Product::find($itemInfo->id);

    //     if ($product == null) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Product not found',
    //         ], 404); // 404 Not Found
    //     }

    //     // Check qty available in stock
    //     if ($product->track_qty == 'Yes') {
    //         if ($qty <= $product->qty) {
    //             Cart::update($rowId, $qty);
    //             $status = true;
    //             $message = 'Cart updated successfully';
    //         } else {
    //             $status = false;
    //             $message = 'Requested qty(' . $qty . ') not available in stock';
    //         }
    //     } else {
    //         Cart::update($rowId, $qty);
    //         $status = true;
    //         $message = 'Cart updated successfully';
    //     }

    //     return response()->json([
    //         'status' => $status,
    //         'message' => $message,
    //     ], $status ? 200 : 400); // 200 OK or 400 Bad Request
    // }

    // public function deleteItem(Request $request)
    // {
    //     $itemInfo = Cart::get($request->rowId);

    //     if ($itemInfo == null) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Item not found in cart',
    //         ], 404); // 404 Not Found
    //     }

    //     Cart::remove($request->rowId);

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Item removed from cart successfully',
    //     ], 200); // 200 OK
    // }

    public function checkout()
{
    $countries = Country::orderBy('name', 'ASC')->get();

    return response()->json([
        'status' => true,
        'countries' => $countries,
    ]);
}


public function processCheckout(Request $request)
{
    // Validate the request
    $validator = Validator::make($request->all(), [
        'first_name' => 'required|min:3',
        'last_name' => 'required',
        'email' => 'required|email',
        'country' => 'required',
        'address' => 'required|min:10',
        'city' => 'required',
        'mobile' => 'required',
        'cartItems' => 'required|array', // Ensure cartItems are included
        
        'cartItems.*.id' => 'required|integer', // Ensure the id is included
        'cartItems.*.name' => 'required|string',
        'cartItems.*.price' => 'required|numeric',
        'cartItems.*.purchaseCount' => 'required|integer',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => false,
            'message' => 'Please fix the errors',
            'errors' => $validator->errors(),
        ], 422); // 422 Unprocessable Entity
    }

    // Fetch the authenticated user
    $user = Auth::guard('api')->user();

    if (!$user) {
        return response()->json([
            'status' => false,
            'message' => 'User not authenticated',
        ], 401); // 401 Unauthorized
    }

    // Save user address (if needed)
    CustomerAddress::updateOrCreate(
        ['user_id' => $user->id],
        [
            'user_id' => $user->id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'country_id' => $request->country,
            'address' => $request->address,
            'appartment' => $request->appartment,
            'city' => $request->city,
            'state' => $request->state,
            'zip' => $request->zip,
            'mobile' => $request->mobile,
        ]
    );

    // Calculate totals and create the order
    $subTotal = collect($request->cartItems)->sum(function ($item) {
        return $item['price'] * $item['purchaseCount'];
    });
    $shipping = 0; // Add your logic to calculate shipping
    $grandTotal = $subTotal + $shipping;

    // Create a new Order instance and set each attribute individually
    $order = new Order();
    $order->user_id = $user->id;
    $order->subtotal = $subTotal;
    $order->shipping = $shipping;
    $order->grand_total = $grandTotal;
    $order->payment_status = 'not paid';
    $order->status = 'pending';
    $order->first_name = $request->first_name;
    $order->last_name = $request->last_name;
    $order->email = $request->email;
    $order->mobile = $request->mobile;
    $order->country_id = $request->country;
    $order->address = $request->address;
    $order->appartment = $request->appartment;
    $order->city = $request->city;
    $order->state = $request->state;
    $order->zip = $request->zip;
    $order->notes = $request->order_notes;
    $order->save();

    // Store Order Items
    foreach ($request->cartItems as $item) {
        $orderItem = new OrderItem();
        $orderItem->order_id = $order->id;
        $orderItem->product_id = $item['id'];
        $orderItem->name = $item['name'];
        $orderItem->qty = $item['purchaseCount'];
        $orderItem->price = $item['price'];
        $orderItem->total = $item['price'] * $item['purchaseCount'];
        $orderItem->save();
    }

    // Destroy the cart
    Cart::destroy();

    return response()->json([
        'status' => true,
        'orderId' => $order->id,
        'message' => 'Order saved successfully.',
    ], 201); // 201 Created
}





    public function thankYou($id)
    {
        return response()->json([
            'status' => true,
            'orderId' => $id,
            'message' => 'Thank you for your order.',
        ]);
    }



    

//     public function addToCart(Request $request){
//         $product = Product::with('product_images')->find($request->id);

//         if( $product == null){
//             return response()->json([
//                 'status' => false,
//                 'message' => 'Product not Found',
//             ]);
//         }

//         if(Cart::count() > 0){
//             //if product not found in the cart, then add product to the cart
//             $cartContent = Cart::content();
//             $productAlreadyExist = false;

//             foreach($cartContent as $item){
//                 if($item->id == $product->id){
//                     $productAlreadyExist = true;
//                 }
//             }

//             if($productAlreadyExist == false){
                
//                 //Cart is Empty
//                 Cart::add($product->id, $product->titel, 1, $product->price, ['productImage' => (!empty($product->product_images) ? $product->product_images->first() : '')]);
                
//                 $status = true;
//                 $message = $product->titel.' added to cart';

//             }else{

//                 $status = false;
//                 $message = $product->titel.' Already added to cart';

//             }

//         }else{

//             //Cart is Empty
//             Cart::add($product->id, $product->titel, 1, $product->price, ['productImage' => (!empty($product->product_images) ? $product->product_images->first() : '')]);
           
//             $status = true;
//             $message = $product->titel.' added to cart';

//         }

//         return response()->json([
//             'status' => $status,
//             'message' => $message,
//         ]);
//     }

//     public function cart(){
//         // dd(Cart::content());
//         $cartContent = Cart::content();
//         // dd($cartContent);
//         $data['cartContent'] = $cartContent;

//         return view('front.cart',$data);
//     }

    // public function updateCart(Request $request){
    //     $rowId = $request->rowId;
    //     $qty = $request->qty;
    //     $itemInfo = Cart::get($rowId);

    //     $product = Product::find($itemInfo->id);

    //     //check qty avilable in stock
    //     if($product->track_qty == 'Yes'){

    //         if($qty <= $product->qty){
    //             Cart::update($rowId, $qty);
    //             $status = true;
    //             $message = 'Cart Updated successfully';
    //             session()->flash('success', $message);

    //         }else{
    //             $status = false;
    //             $message = 'Requested qty('.$qty.') not Availabel in Stock';
    //             session()->flash('error', $message);
    //         }

    //     }else{

    //         Cart::update($rowId, $qty);
    //         $status = true;
    //         $message = 'Cart Updated successfully';
    //         session()->flash('success', $message);

    //     }

    //     return response()->json([
    //         'status' => true,
    //         'message' => $message,
    //     ]);
    // }

    // public function deleteItem(Request $request){
    //     $itemInfo = Cart::get($request->rowId);

    //     if($itemInfo == null){

    //         $errorMessage = 'Item not Found in Cart';
    //         session()->flash('error', $errorMessage);

    //         return response()->json([
    //             'status' => false,
    //             'message' => $errorMessage,
    //         ]);

    //     }

    //     cart::remove($request->rowId);

    //     $message = 'Item removed from cart successfully';
    //     session()->flash('success', $message);

    //     return response()->json([
    //         'status' => true,
    //         'message' => $message,
    //     ]);

    // }

    // public function checkout(){

    //     // if the Cart is Empty redirect to the Cart page
    //     if(Cart::count() == 0){
    //         return redirect()->route('front.cart');
    //     }

    //     // if the User is not Loged in then redirect him to the login page
    //     if(Auth::check() == false){

    //         if(!session()->has('url.intended')){
    //             session(['url.intended' => url()->current()]);
    //         }
    //         // dd(url()->current());

    //         return redirect()->route('account.login');
    //     }

    //     $customerAddress = CustomerAddress::where('user_id', Auth::user()->id)->first();

    //     session()->forget('url.intended');

    //     $countries = Country::orderBy('name', 'ASC')->get();

    //     return view('front.checkout', [
    //         'countries' => $countries,
    //         'customerAddress' => $customerAddress,
    //     ]);
    // }

    // public function processCheckout(Request $request){

    //     // step 1 Apply Validation
    //     $validator = Validator::make($request->all(),[
    //         'first_name' => 'required|min:3',
    //         'last_name' => 'required',
    //         'email' => 'required|email',
    //         'country' => 'required',
    //         'address' => 'required|min:10',
    //         'city' => 'required',
    //         'mobile' => 'required',
    //     ]);

    //     if($validator->fails()){
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Please fix the Errors',
    //             'errors' => $validator->errors(),
    //         ]);
    //     }

    //     // step 2 save user Address
    //     $user = Auth::user();

    //     CustomerAddress::updateOrCreate(
    //         ['user_id' => $user->id],
    //         [
    //             'user_id' => $user->id, 
    //             'first_name' => $request->first_name, 
    //             'last_name' => $request->last_name, 
    //             'email' => $request->email, 
    //             'country_id' => $request->country, 
    //             'address' => $request->address, 
    //             'appartment' => $request->appartment, 
    //             'city' => $request->city, 
    //             'state' => $request->state, 
    //             'zip' => $request->zip, 
    //             'mobile' => $request->mobile, 
    //             // 'notes' => $request->order_notes, 
    //         ]
    //     );

    //     // step 3 store data in orders table

    //     // if($request->payment_methode == 'COD'){

    //         // Calculate values
    //         $shipping = 0;
    //         $subTotal = Cart::subtotal(2, '.', ''); // Assuming Cart::subtotal() returns a formatted string
    //         $grandTotal = $subTotal + $shipping;

    //         // Create a new Order instance
    //         $order = new Order;

    //         // Assign calculated values
    //         $order->subtotal = $subTotal;
    //         $order->shipping = $shipping;
    //         $order->grand_total = $grandTotal;
    //         $order->payment_status = 'not paid';
    //         $order->status = 'pending';

    //         $order->user_id = $user->id;
    //         $order->first_name = $request->first_name;
    //         $order->last_name = $request->last_name;
    //         $order->email = $request->email;
    //         $order->mobile = $request->mobile;
    //         $order->country_id = $request->country;
    //         $order->address = $request->address;
    //         $order->appartment = $request->appartment;
    //         $order->city = $request->city;
    //         $order->state = $request->state;
    //         $order->zip = $request->zip;
    //         $order->notes = $request->order_notes;
    //         $order->save();

    //         // step 4 store Order Items in order items table

    //         foreach (Cart::content() as $item) {
    //             $orderItem = new OrderItem;
    //             $orderItem->product_id = $item->id;
    //             $orderItem->order_id = $order->id;
    //             $orderItem->name = $item->name;
    //             $orderItem->price = $item->price;
    //             $orderItem->qty = $item->qty;
    //             $orderItem->total = $item->price * $item->qty;
    //             $orderItem->save();
    //         }

    //         session()->flash('success', 'You have successfully placed your Order.');

    //         Cart::destroy();

    //         return response()->json([
    //             'status' => true,
    //             'orderId' => $order->id,
    //             'message' => 'Order saved successfully.',
    //         ]);

    //     // }
    // }

    // public function thankYou($id){
    //     return view('front.thanks', [
    //         'id' => $id,
    //     ]);
    // }
}
