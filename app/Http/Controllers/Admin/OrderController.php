<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    public function index(Request $request){

        $orders = Order::latest('orders.created_at')->select('orders.*','users.name','users.email');
        $orders = $orders->leftJoin('users','users.id', 'orders.user_id');

        if($request->get('keyword') != ""){
            $orders = $orders->where('users.name','like','%' .$request->keyword. '%');
            $orders = $orders->orWhere('users.email','like','%' .$request->keyword. '%');
            $orders = $orders->orWhere('orders.id','like','%' .$request->keyword. '%');
        }

        $orders = $orders->paginate(10);

        return view('admin.order.index', [
            'orders' => $orders
        ]);
    }
    
    public function detail($orderId){
        $order = Order::select('orders.*','countries.name as countryName')
                    ->where('orders.id', $orderId)
                    ->leftJoin('countries','countries.id','orders.country_id')
                    ->first();
        
        $orderItems = OrderItem::where('order_id', $orderId)->get();

        return view('admin.order.detail', [
            'order' => $order,
            'orderItems' => $orderItems,
        ]);
    }

    public function changeOrderStatus(Request $request, $orderId){
        $order = Order::find($orderId);
        $order->status = $request->status;
        $order->shipped_date = $request->shipped_date;
        $order->save();

        session()->flash('success', 'Order status updated successfully');
        return response()->json([
            'status' => true,
            'message' => 'Order status updated successfully'
        ]);
    }
}
