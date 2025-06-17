<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Filesystem\FilesystemAdapter;
use League\Flysystem\Local\LocalFilesystemAdapter;

class UserStorageService
{
    protected Filesystem $disk;
    protected string $diskName;
    protected int $userId;
    protected string $uploadDir;

    public function __construct(int $userId, string $diskName = null, string $uploadDir = 'upload')
    {
        $this->userId = $userId;
        $this->diskName = $diskName ?? config('filesystems.image_disk', 'public');
        $this->disk = Storage::disk($this->diskName);
        $this->uploadDir = $uploadDir;
    }

    public static function forUser(int $userId): static
    {
        return new static($userId);
    }

    public function getUserDir(): string
    {
        return "{$this->uploadDir}/{$this->userId}";
    }

    public function storeUploadedFile(UploadedFile $file): string
    {
        return $file->store($this->getUserDir(), $this->diskName);
    }

    public function getPath(string $filename): string
    {
        return $this->disk->path("{$this->getUserDir()}/{$filename}");
    }

    public function getUrl(string $filename): string
    {
        return $this->disk->url("{$this->getUserDir()}/{$filename}");
    }

    public function exists(string $filename): bool
    {
        return $this->disk->exists("{$this->getUserDir()}/{$filename}");
    }

    public function delete(string $filename): bool
    {
        return $this->disk->delete("{$this->getUserDir()}/{$filename}");
    }

    public function isLocal(): bool
    {
        if ($this->disk instanceof FilesystemAdapter) {
            return $this->disk->getAdapter() instanceof LocalFilesystemAdapter;
        }

        return false;
    }

    /**
     * Safely get the local path to the file (for processing).
     * If using cloud storage, copy to tmp and return tmp path.
     */
    public function prepareLocalFile(string $filename): string
    {
        $relativePath = "{$this->getUserDir()}/{$filename}";

        if ($this->isLocal()) {
            return $this->disk->path($relativePath);
        }

        // Cloud: copy to local temp
        $tmpDir = storage_path('app/tmp');
        if (!is_dir($tmpDir)) {
            mkdir($tmpDir, 0755, true);
        }

        $tmpPath = "{$tmpDir}/{$this->userId}_{$filename}";
        file_put_contents($tmpPath, $this->disk->get($relativePath));
        return $tmpPath;
    }

    /**
     * Optionally delete a temp file after processing
     */
    public function cleanupTempFile(string $tmpPath): void
    {
        if (file_exists($tmpPath)) {
            unlink($tmpPath);
        }
    }
}
