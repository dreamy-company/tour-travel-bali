<?php

namespace App\Http\Controllers;

use App\Models\GuideProfile;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class GuidePhotoController extends Controller
{
    /**
     * Stream a verified guide's approved headshot publicly.
     *
     * Only headshots belonging to verified guides are served, and the
     * resolved path is constrained to the headshot directory to prevent
     * arbitrary file access.
     */
    public function show(GuideProfile $guideProfile): BinaryFileResponse
    {
        abort_unless($guideProfile->is_verified, 404, 'Photo not found.');

        $path = $guideProfile->headshot;

        abort_if(
            ! $path || ! str_starts_with($path, 'guide_documents/headshots/'),
            404,
            'Photo not found.'
        );

        $fullPath = storage_path('app/private/' . $path);

        abort_unless(is_file($fullPath), 404, 'Photo not found.');

        return response()->file($fullPath);
    }
}
