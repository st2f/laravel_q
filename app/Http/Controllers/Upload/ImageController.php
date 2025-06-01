<?php

namespace App\Http\Controllers\Upload;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\ImageStoreRequest;
use App\Jobs\ImageProcessor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ImageController extends Controller
{
    public const UPLOAD_DIR = 'upload';

    public static function storagePathUser($userId)
    {
        return storage_path('app/public/' . self::UPLOAD_DIR . '/' . $userId);
    }

    /**
     * Display a listing of images.
     */
    public function list(Request $request): JsonResponse
    {
        $path = self::storagePathUser($request->user()->id);
        $files = glob($path . '/*.jpg', GLOB_BRACE) ?: [];
        $fileNames = array_map('basename', $files);

        return response()->json($fileNames);
    }

    /**
     * show an image.
     */
    public function show(Request $request, string $file)
    {
        $path = self::storagePathUser($request->user()->id) . '/' . $file;

        abort_unless(file_exists($path), 404);

        return response()->file($path); // or response()->download($path)
    }

    /**
     * Show the image upload page.
     */
    public function add(Request $request): Response
    {
        return Inertia::render('upload/Add', [
        ]);
    }

    /**
     * Store the image.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'image' => 'required|file|max:2048|mimes:jpg,png',
        ]);

        try {
            $filename = $request->file('image')->store(
                self::UPLOAD_DIR . '/' . $request->user()->id,
                'public'
            );
        } catch (\Throwable $th) {
            die('Could not upload image: ' . $th->getMessage() . PHP_EOL);
        }

        ImageProcessor::dispatch($request->user()->email, $filename);

        return to_route('image.add');
    }

    /**
     * Delete the image.
     */
    public function destroy(Request $request, string $file)
    {
        $path = self::storagePathUser($request->user()->id) . '/' . $file;

        abort_unless(file_exists($path), 404);

        unlink($path);

        return response()->noContent();
    }
}
