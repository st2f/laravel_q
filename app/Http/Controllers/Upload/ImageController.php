<?php

namespace App\Http\Controllers\Upload;

use App\Http\Controllers\Controller;
use App\Jobs\ImageProcessor;
use App\Services\ImageStorageService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ImageController extends Controller
{
    public function __construct(private ImageStorageService $imageStorage) {}

    public function index(Request $request): JsonResponse
    {
        $path = $this->imageStorage->userPath($request->user()->id);
        $files = glob($path . '/*.jpg', GLOB_BRACE) ?: [];

        return response()->json(array_map('basename', $files));
    }

    public function show(Request $request, string $file)
    {
        $path = $this->imageStorage->fullPath($request->user()->id, $file);
        abort_unless(file_exists($path), 404);

        return response()->file($path); // or response()->download($path)
    }

    public function create(Request $request): Response
    {
        return Inertia::render('upload/Add');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'image' => 'required|file|max:2048|mimes:jpg,png',
        ]);

        $filename = $this->imageStorage->storeUploadedFile(
            $request->user()->id,
            $request->file('image')
        );

        ImageProcessor::dispatch($request->user()->email, $filename);

        return to_route('image.create');
    }

    public function destroy(Request $request, string $file)
    {
        $path = $this->imageStorage->fullPath($request->user()->id, $file);
        abort_unless(file_exists($path), 404);

        $this->imageStorage->delete($request->user()->id, $file);

        return response()->noContent();
    }
}
