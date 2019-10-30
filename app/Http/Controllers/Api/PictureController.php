<?php

namespace App\Http\Controllers\Api;

use App\Models\Picture;
use App\Services\Picture\Process;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\PictureResourceCollection;

class PictureController extends Controller
{
    /**
     * Return a collection of picture metadata for the authenticated user.
     *
     * @return PictureResourceCollection
     */
    public function index()
    {
        return new PictureResourceCollection(Picture::where('user_id', '=', auth()->user()->id)->paginate());
    }

    /**
     * Store a newly created resource in storage.
     * @param Process $pictureProcessor
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Process $pictureProcessor): JsonResponse
    {
        if ($pictureProcessor->store()) {

            return response()->json(['message' => 'File uploaded succesfuly.']);
        }

        return response()->json(['message' => 'File failed to process.'], 400);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Find the picture or fail
        if ($picture = Picture::where('pic_id', '=', $id)->where('user_id', '=', auth()->user()->id)->first()) {

            $file = Storage::disk('pictures')->get(join('/', [auth()->user()->id, $picture->pic_filename]));
            $finfo = new \finfo(FILEINFO_MIME_TYPE);

            return response()->make($file, 200, ['Content-Type' => $finfo->buffer($file)]);
        }

        abort(404);
    }

}
