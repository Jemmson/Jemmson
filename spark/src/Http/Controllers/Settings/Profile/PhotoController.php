<?php

namespace Laravel\Spark\Http\Controllers\Settings\Profile;

use Illuminate\Http\Request;
use Cloudinary;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Spark\Http\Controllers\Controller;
use Laravel\Spark\Contracts\Interactions\Settings\Profile\UpdateProfilePhoto;

class PhotoController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Store the user's profile photo.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $this->interaction(
            $request, UpdateProfilePhoto::class,
            [$request->user(), $request->all()]
        );
    }

    public function uploadTaskImage(Request $request)
    {
        // get the file
        $file = $request->photo;

        // create a hash name for storage and retrieval
        $path = $file->hashName('originalName');

        $image = Cloudinary\Uploader::upload($file, [
            "public_id" => $path
        ]);

        if (empty($image)) {
            return response()->json(['message' => 'Error Uploading Image. Please Try Again'], 400);
        }

        $url = $image['secure_url'];

        $user = User::find(Auth::user()->getAuthIdentifier());
        $user->photo_url = $url;
        try {
            $user->save();
        } catch (\Exception $e) {
            Log::error('Saving Task Image: ' . $e->getMessage());
            return response()->json(['message' => 'error uploading image', errors => [$e->getMessage]], 400);
        }
    }
}
