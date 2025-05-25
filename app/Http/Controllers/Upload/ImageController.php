<?php

namespace App\Http\Controllers\Upload;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\ImageStoreRequest;
use App\Jobs\ImageProcessor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ImageController extends Controller
{
    /**
     * Display a listing of images.
     */
    public function index()
    {
        //
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
                'upload' . '/' . $request->user()->id,
                'public'
            );
            ImageProcessor::dispatch($request->user()->email, $filename);

        } catch (\Throwable $th) {
            die('Could not upload image: ' . $th->getMessage());
        }

        return to_route('image.add');
    }

    /**
     * Delete the image.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'filename' => ['required', 'string', 'max:255']
        ]);

        return to_route('image.add');
    }
}
