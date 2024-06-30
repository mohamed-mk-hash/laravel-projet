<?php

namespace App\Http\Controllers\Front;

use App\Models\Order;
use App\Models\Wishlist;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function processRegister(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:5|confirmed',
        ]);

        if($validator->passes()){
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->password = Hash::make($request->password);
            $user->save();

            return response()->json([
                'status' => true,
                'message' => 'You have been registered successfully',
            ], 201);  // 201 Created
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);  // 422 Unprocessable Entity
        }
    }

    public function authenticate(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if($validator->passes()){
            if(Auth::attempt(['email' => $request->email, 'password' => $request->password], $request->get('remember'))){
                $user = Auth::user();
                $token = $user->createToken('authToken')->accessToken;
                $plainTextToken = $user->createToken('authToken')->plainTextToken;

                return response()->json([
                    'status' => true,
                    'token' => $token,
                    'plainTextToken' => $plainTextToken,
                    'message' => 'Login successful',
                ], 200);  // 200 OK
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Either Email / Password is incorrect',
                ], 401);  // 401 Unauthorized
            }
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);  // 422 Unprocessable Entity
        }
    }

    public function profile(Request $request)
    {
        $user = Auth::user();

        return response()->json([
            'status' => true,
            'user' => $user,
        ], 200);  // 200 OK
    }

    public function logout(Request $request)
    {
        Cart::destroy();

        $user = $request->user(); // Get the authenticated user
        $user->currentAccessToken()->delete(); // Delete the current access token
    
        // $user = Auth::user();
        // $user->token()->revoke();  // Revoke the token

        return response()->json([
            'status' => true,
            'message' => 'You have successfully logged out!',
        ], 200);  // 200 OK
    }

    public function orders(Request $request)
    {
        $user = Auth::user();
        $orders = Order::where('user_id', $user->id)->orderBy('created_at', 'DESC')->get();

        return response()->json([
            'status' => true,
            'orders' => $orders,
        ], 200);  // 200 OK
    }

    public function orderDetail($id, Request $request)
    {
        $user = Auth::user();
        $order = Order::where('user_id', $user->id)->where('id', $id)->first();

        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'Order not found',
            ], 404);  // 404 Not Found
        }

        $orderItems = OrderItem::where('order_id', $id)->get();

        return response()->json([
            'status' => true,
            'order' => $order,
            'orderItems' => $orderItems,
        ], 200);  // 200 OK
    }

    public function wishlist()
{
    $user = Auth::guard('api')->user();
    $wishlists = Wishlist::where('user_id', $user->id)
                    ->with('product.product_images') // Eager load product_images relation
                    ->get();

    return response()->json([
        'status' => true,
        'wishlists' => $wishlists,
    ]);
}

    public function removeProductFromWishlist(Request $request)
    {
        $user = Auth::guard('api')->user();
        $wishlist = Wishlist::where('user_id', $user->id)->where('product_id', $request->id)->first();

        if ($wishlist == null) {
            return response()->json([
                'status' => false,
                'message' => 'Product already removed',
            ]);
        } else {
            Wishlist::where('user_id', $user->id)->where('product_id', $request->id)->delete();

            return response()->json([
                'status' => true,
                'message' => 'Product removed successfully',
            ]);
        }
    }





    


    // public function login(){
    //     return view('front.account.login');
    // }

    // public function register(){
    //     return view('front.account.register');
    // }

    // public function processRegister(Request $request){

    //     $validator = Validator::make($request->all(),[
    //         'name' => 'required|min:3',
    //         'email' => 'required|email|unique:users',
    //         'password' => 'required|min:5|confirmed',
    //     ]);

    //     if($validator->passes()){

    //         $user = new User();
    //         $user->name = $request->name;
    //         $user->email = $request->email;
    //         $user->phone = $request->phone;
    //         $user->password = Hash::make($request->password);
    //         $user->save();

    //         session()->flash('success', 'You have been Registred Succeessfully');
    //         return response()->json([
    //             'status' => true,
    //         ]);
            
    //     }else{
    //         return response()->json([
    //             'status' => false,
    //             'errors' => $validator->errors(),
    //         ]);
    //     }
    // }

    // public function authenticate(Request $request){

    //     $validator = Validator::make($request->all(),[
    //         'email' => 'required|email',
    //         'password' => 'required',
    //     ]);

    //     if($validator->passes()){

    //         if(Auth::attempt(['email' => $request->email, 'password' => $request->password], $request->get('rememmber'))){

    //             if(session()->has('url.intended')){
    //                 // dd(session()->get('url.intended'));
    //                 return redirect(session()->get('url.intended'));
    //             }

    //             return redirect()->route('account.profile');

    //         }else{
    //             // session()->flash('error', 'Ether Email / Password is incorect');
    //             return redirect()->route('account.login')
    //                     ->withInput($request->only('email'))
    //                     ->with('error', 'Ether Email / Password is incorect');
    //         }

    //     }else{
    //         return redirect()->route('account.login')
    //         ->withErrors($validator)
    //         ->withInput($request->only('email'));
    //     }
    // }

    // public function profile(){
    //     return view('front.account.profile');
    // }

    // public function logout(){
    //     Auth::logout();
    //     return redirect()->route('account.login')
    //             ->with('success', 'You have Succeessfully Logedout!');
    // }

    // public function orders(){

    //     $user = Auth::user();

    //     $orders = Order::where('user_id', $user->id)->orderBy('created_at', 'DESC')->get();

    //     $data['orders'] = $orders;

    //     return view('front.account.order', $data);
    // }

    // public function orderDetail($id){
        
    //     $user = Auth::user();

    //     $data = [];
    //     $order = Order::where('user_id', $user->id)->where('id', $id)->first();
    //     $data['order'] = $order;
        
    //     $orderItems = OrderItem::where('order_id', $id)->get();
    //     $data['orderItems'] = $orderItems;

    //     return view('front.account.order-detail', $data);
    // }
}
