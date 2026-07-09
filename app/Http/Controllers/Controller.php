<?php

namespace App\Http\Controllers;

use App\Models\Media;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    /**
     * Persist an uploaded image in MongoDB and return its path.
     *
     * Local-disk storage does not survive on Vercel (read-only filesystem,
     * ephemeral /tmp), so images live in the media collection and are served
     * by MediaController via /media/{id} and /storage/media/{id}. The
     * returned "media/{id}" keeps existing asset('storage/'.$path) and
     * asset($path) call sites working unchanged.
     */
    protected function uploadImage($file, $directory = 'uploads')
    {
        if (!$file) {
            return null;
        }

        $media = Media::create([
            'directory' => $directory,
            'name' => $file->getClientOriginalName(),
            'mime' => $file->getMimeType(),
            'data' => base64_encode(file_get_contents($file->getRealPath())),
        ]);

        return 'media/' . $media->id;
    }
}
