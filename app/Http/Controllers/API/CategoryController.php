<?php

namespace App\Http\Controllers\API;

use App\Category;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $categories  = Category::latest()->paginate(5);
        return response()->json([
            'data' => $categories,
            'pagination' => [
                'total' => $categories->total(),
                'per_page' => $categories->perPage(),
                'current_page' => $categories->currentPage(),
                'last_page' => $categories->lastPage(),
                'from' => $categories->firstItem(),
                'to' => $categories->lastItem()
            ],
        ], 200);
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

            $category = DB::table('categories')->insert([
                'name' => $request->get('name'),
                'image' => $request->file('image')->hashName(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        } else{
            $category = DB::table('categories')->insert([
                'name' => $request->get('name'),
                'image' => $request->get('image'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }

        return response()->json($category, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $category = DB::table('categories')->find($id);

        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        return response()->json($category, 200);
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
     * @return \Illuminate\Http\JsonResponse
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
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['message' => 'Category not found']);
        }

        //$path = $request->file('image')->store('public/images');

        if ($request->hasFile('image')){
            $path = $request->file('image')->store('public/images');

            $category = DB::table('categories')->where('id', 'like', $id)->update([
                'name' => $request->get('name'),
                'image' => $request->file('image')->hashName(),
                'updated_at' => Carbon::now(),
            ]);
        } else{
            $category = DB::table('categories')->where('id', 'like', $id)->update([
                'name' => $request->get('name'),
                'updated_at' => Carbon::now(),
            ]);
        }

        return response()->json($category, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        $status = $category->delete();

        return response()->json([
            'status' => $status,
            'message' => $status ? 'Category deleted' : 'Error deleting Category'
        ]);
    }
}
