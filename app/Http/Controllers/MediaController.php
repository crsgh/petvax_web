<?php

namespace App\Http\Controllers;

use App\Models\Media;

class MediaController extends Controller
{
    /**
     * Stream an uploaded image stored in MongoDB. Vercel's serverless
     * filesystem is read-only, so uploads live in the media collection
     * instead of storage/app/public.
     */
    public function show($id)
    {
        $media = Media::findOrFail($id);

        return response(base64_decode($media->data), 200)
            ->header('Content-Type', $media->mime ?: 'application/octet-stream')
            ->header('Cache-Control', 'public, max-age=31536000, immutable');
    }
}
