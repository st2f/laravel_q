<?php

namespace App\Http\Controllers\Upload;

use App\Http\Controllers\Controller;
use App\Jobs\WordProcessor;
use App\Services\UserStorageService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class WordController extends Controller
{
    public function __construct(private UserStorageService $userStorage) {}

    public function index(): JsonResponse
    {
        $path = $this->userStorage->getPath('');
        $files = glob($path . '/*.pdf', GLOB_BRACE) ?: [];

        return response()->json(array_map('basename', $files));
    }

    public function show(string $file)
    {
        abort_unless($this->userStorage->exists($file), 404);
        $path = $this->userStorage->getPath($file);

        return response()->file($path);
    }

    public function create(): Response
    {
        return Inertia::render('uploadWord/Add');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'doc' => 'required|file|max:2048|mimes:docx,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ]);

        $storedPath = $this->userStorage->storeUploadedFile(
            $request->file('doc')
        );

        WordProcessor::dispatch($request->user()->email, basename($storedPath))
            ->onQueue('pdf');

        return to_route('doc.create');
    }

    public function destroy(string $file)
    {
        abort_unless($this->userStorage->exists($file), 404);

        $this->userStorage->delete($file);

        return response()->noContent();
    }
}
