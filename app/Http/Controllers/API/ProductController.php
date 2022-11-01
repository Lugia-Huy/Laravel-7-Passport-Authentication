<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Product;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $products  = Product::all();

        return response()->json($products, 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        if ($request->hasFile('image')){
            $path = $request->file('image')->store('public/images');

            $product = DB::table('products')->insert([
                'name' => $request->get('name'),
                'category' => $request->get('category'),
                'image' => $request->file('image')->hashName(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        } else{
            $product = DB::table('products')->insert([
                'name' => $request->get('name'),
                'category' => $request->get('category'),
                'image' => $request->get('image'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }

        return response()->json($product, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $product = DB::table('products')->find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        return response()->json($product, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatenew(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found']);
        }

        //$path = $request->file('image')->store('public/images');

        if ($request->hasFile('image')){
            $path = $request->file('image')->store('public/images');

            $product = DB::table('products')->where('id', 'like', $id)->update([
                'name' => $request->get('name'),
                'category' => $request->get('category'),
                'image' => $request->file('image')->hashName(),
                'updated_at' => Carbon::now(),
            ]);
        } else{
            $product = DB::table('products')->where('id', 'like', $id)->update([
                'name' => $request->get('name'),
                'category' => $request->get('category'),
                'updated_at' => Carbon::now(),
            ]);
        }

        return response()->json($product, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $status = $product->delete();

        return response()->json([
            'status' => $status,
            'message' => $status ? 'Product deleted' : 'Error deleting product'
        ]);
    }
}

