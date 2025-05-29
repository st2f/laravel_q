<?php

namespace App\Jobs;

use Illuminate\Bus\Batch;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Bus;
use Illuminate\Validation\ValidationException;

class ImageProcessor implements ShouldQueue
{
    use Batchable, Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public string $email, public string $filename)
    {
    }

    /**
     * Execute the job.
     * @throws \Throwable
     */
    public function handle(): void
    {
        //dd($this->email, $this->filename);

        $info = pathinfo(storage_path('app/public/') . $this->filename);
        $filepath = $info['dirname'] . '/' . $info['basename'];

        if (!file_exists($filepath)) {
            throw ValidationException::withMessages([
                'filepath' => 'Filepath does not exist: ' . $filepath,
            ]);
        }

        $jobs = [];
        $sizes = [100, 300];

        foreach ($sizes as $size) {
            $jobs[] = new ImageResize($filepath, $size);
        }

        // parallel
        $email = $this->email;// to prevent $this to be serialized

        Bus::batch([
            ...$jobs,
        ])->then(function(Batch $batch) use ($filepath, $sizes, $email) {
            SendImagesInEmail::dispatch($email, $filepath, $sizes);
        })->catch(function (Batch $batch, Throwable $e) {
            dd($e->getMessage());
        })->dispatch();

        //$this->dispatch('image-processed');
    }
}
