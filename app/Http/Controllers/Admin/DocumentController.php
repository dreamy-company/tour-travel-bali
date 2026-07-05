<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GuideProfile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class DocumentController extends Controller
{
    /**
     * Serve a private document file securely to authorized admins.
     */
    public function show(GuideProfile $profile, string $type): Response
    {
        $allowedTypes = ['ktp_photo', 'ktpp_file', 'skck_file', 'surat_sehat_file'];

        if (! in_array($type, $allowedTypes, true)) {
            abort(404, 'Document type not found.');
        }

        $path = $profile->getAttribute($type);

        if (! $path || ! is_string($path) || ! Storage::disk('local')->exists($path)) {
            abort(404, 'File not found on disk.');
        }

        return Storage::disk('local')->response($path);
    }
}
