<?php

namespace App\Jobs;

use App\Jobs\Middleware\BackgroundJobLimiter;
use App\Services\UserStorageService;
use Illuminate\Bus\Batch;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Support\Facades\Bus;

class ImageProcessor implements ShouldQueue
{
    use Batchable, Queueable;

    public function __construct(
        public string $email,
        public string $filename
    ){}

    public function handle(): void
    {

        $storage = resolve(UserStorageService::class);
        $basePath = $storage->basePath();
        $info = pathinfo($basePath . '/' . $this->filename);
        $filepath = $info['dirname'] . '/' . $info['basename'];

        if (!file_exists($filepath)) {
            $this->batch()->cancel();
        }

        $jobs = [];
        $sizes = [100, 300];

        foreach ($sizes as $size) {
            $jobs[] = new ImageResize($filepath, $size);
        }

        // parallel : if a job failed, others will continue
        $email = $this->email;// to prevent $this to be serialized

        $pendingBatch = Bus::batch([
            ...$jobs,
        ])->then(function(Batch $batch) use ($filepath, $sizes, $email) {
            SendImagesInEmail::dispatch($email, $filepath, $sizes);
        })->catch(function (Batch $batch, Throwable $e) {
            //dd($e->getMessage());
        })->dispatch();

        // Bus::chain([
        //  $pendingBatch
        // ])->dispatch();

        //$this->dispatch('image-processed');
    }

    public function middleware(): array
    {
        //return [BackgroundJobLimiter::class];
        return [
            new RateLimited('background-jobs')
        ];
    }
}
