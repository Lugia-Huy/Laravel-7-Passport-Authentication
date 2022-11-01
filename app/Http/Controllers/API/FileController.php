<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\File;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class FileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $files = File::latest()->paginate(5);

        return response()->json([
            'data' => $files,
            'pagination' => [
                'total' => $files->total(),
                'per_page' => $files->perPage(),
                'current_page' => $files->currentPage(),
                'last_page' => $files->lastPage(),
                'from' => $files->firstItem(),
                'to' => $files->lastItem()
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
        $fileName = $request->get("fileName");
        $extension = $request->get("ext");
        $request->file('file')->store('public/upload');

        $file = DB::table('files')->insert([
            'full_name' => $request->file('file')->hashName(),
            'only_name' => $fileName,
            'ext' => $extension,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        return response()->json(['success' => 'You have successfully upload file.']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $file = DB::table('files')->find($id);

        if (!$file) {
            return response()->json(['message' => 'File not found'], 404);
        }

        return response()->json($file, 200);
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
        $file = DB::table('files')->find($id);

        if (!$file) {
            return response()->json(['message' => 'File not found']);
        }

        $file = DB::table('files')->where('id', 'like', $id)->update([
            'only_name' => $request->get('name'),
            'updated_at' => Carbon::now(),
        ]);

        return response()->json($file, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $file = File::find($id);

        if (!$file) {
            return response()->json(['message' => 'file not found'], 404);
        }

        if ($file->full_name) {
            // get filename
            $filename = str_replace('storage/upload/', '', $file->full_name);
            // remove old file from storage
            unlink(storage_path('app/public/upload/' . $filename));
        }

        $status = $file->delete();

        return response()->json([
            'status' => $status,
            'message' => $status ? 'File deleted' : 'Error deleting file'
        ]);
    }
}

