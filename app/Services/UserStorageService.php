<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class UserStorageService
{
    private string $disk;
    private string $uploadDir;

    public function __construct()
    {
        $this->disk = config('filesystems.image_disk', 'public'); // default to 'public'
        $this->uploadDir = 'upload';
    }

    public function getDisk(): string
    {
        return $this->disk;
    }

    public function getUserDir(int $userId): string
    {
        return $this->uploadDir . '/' . $userId;
    }

    public function basePath(): string
    {
        return Storage::disk($this->disk)->path('');
    }

    public function userPath(int $userId): string
    {
        return $this->basePath() . '/' . $this->getUserDir($userId);
    }

    public function fullPath(int $userId, string $filename = ''): string
    {
        $relativePath = $this->getUserDir($userId) . ($filename ? "/$filename" : '/does-not-exist');
        return Storage::disk($this->disk)->path($relativePath);
    }

    public function exists(int $userId, string $filename): bool
    {
        return Storage::disk($this->disk)->exists($this->getUserDir($userId) . "/$filename");
    }

    public function delete(int $userId, string $filename): bool
    {
        return Storage::disk($this->disk)->delete($this->getUserDir($userId) . "/$filename");
    }

    public function storeUploadedFile(int $userId, \Illuminate\Http\UploadedFile $file): string
    {
        return $file->store($this->uploadDir . '/' . $userId, $this->disk);
    }
}
