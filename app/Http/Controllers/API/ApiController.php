<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ApiController extends Controller
{
    public function getCategories()
    {
        $data = Category::all();
        return response()->json($data, 200);
    }

    public function sellerGetOrders($seller_id)
    {
        $data = Order::query()->where('seller_id', $seller_id)->with('product', 'buyer')->orderBy('id', 'desc')->get();

        return response()->json($data, 200);
    }


    public function sellerGetProducts($id)
    {
        $data = Product::query()->where('seller_id', $id)->with('category', 'seller')->orderBy('id', 'desc')->get();

        return response()->json($data, 200);
    }

    public function buyerGetProducts()
    {
        $data = Product::query()->with('category', 'seller')->orderBy('id', 'desc')->get();

        return response()->json($data, 200);
    }

    public function buyerGetOrders($buyer_id)
    {
        $data = Order::query()->where('buyer_id', $buyer_id)->with('product', 'seller')->orderBy('id', 'desc')->get();

        return response()->json($data, 200);
    }

    public function buyerCompleteOrder(Request $request)
    {
        Order::create([
            'buyer_id' => $request->buyer_id,
            'seller_id' => $request->seller_id,
            'product_id' => $request->product_id,
            'amount' => $request->amount,
            'address' => $request->address,

        ]);

        $data = Order::query()->where('buyer_id', $request->buyer_id)->orderBy('id', 'desc')->with('seller', 'product')->get();

        return response()->json($data, 200);
    }

    public function createProduct(Request $request)
    {
        $product = new Product();
        $product->seller_id = $request->seller_id;
        $product->category_id = $request->category_id;
        $product->product_name = $request->product_name;
        $product->product_description = $request->product_description;
        $product->quantity = $request->quantity;
        $product->price = $request->price;

        if ($request->hasFile('product_image')) {
            $product_image = $request->file('product_image');
            $imageName = Str::random(10) . '.' . $product_image->getClientOriginalExtension();
            Storage::disk('public')->putFileAs($request->file('product_image'), $imageName);;
            // $product_image->move(public_path('storage'), $imageName);
            $product->product_image = $imageName;
        }

        $product->save();

        $data = Product::query()->where('seller_id', $request->seller_id)->with('category', 'seller')->get();

        return response()->json($data);
    }

    public function login(Request $request)
    {
        $login = Auth::attempt($request->only('email', 'password'));
        if ($login) {
            $user = Auth::user();
            return response()->json([
                'status' => 'success',
                'data' => $user,
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'data' => "Invalid username or password",
            ]);
        }
    }

    public function SellerRegister(Request $request)
    {
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone_number = $request->phoneNumber;
        $user->longitude = $request->longitude;
        $user->latitude = $request->latitude;
        $user->role = 2;
        $user->password = Hash::make($request->password);
        $user->save();

        $categories = Category::all();
        $products = Product::all();

        return response()->json(['status' => 'success', 'user' => $user, 'products' => $products, 'categories' => $categories], 200);
    }
    public function buyerRegister(Request $request)
    {
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone_number = $request->phoneNumber;
        $user->role = 3;
        $user->password = Hash::make($request->password);
        $user->save();

        $categories = Category::all();
        $products = Product::all();

        return response()->json(['status' => 'success', 'user' => $user, 'products' => $products, 'categories' => $categories], 200);
    }

    public function updateProfile(Request $request)
    {
        $user = User::find($request->id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone_number = $request->phone_number;
        if ($request->password) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        return response()->json($user, 200);
    }
}
