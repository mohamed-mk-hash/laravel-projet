<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index(){
        // $admin = Auth::guard('admin')->user();
        // echo 'welcom' . $admin->name . '<a href=" '.route('admin.logout').'"> Logout </a>';
        return view('admin.dashboard');
    }

    public function logout(){
        $admin = Auth::guard('admin')->logout();
        return redirect()->route('admin.login');
    }
}
