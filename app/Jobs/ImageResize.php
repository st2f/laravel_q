<?php

namespace App\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\SkipIfBatchCancelled;
use Illuminate\Queue\SerializesModels;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class ImageResize implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public string $filepath, public int $size)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // option 1
        //if ($this->batch()->cancelled()) {
        //return;
        //}

        // create image manager with desired driver
        $manager = new ImageManager(new Driver());

        $image = $manager->read($this->filepath);
        $image->scale(height: $this->size);
        $encoded = $image->toJpg();
        $encoded->save( self::newFilename($this->filepath, $this->size));
    }

    public static function newFilename($filepath, $size): string
    {
        $info = pathinfo($filepath);
        return $info['dirname'] . '/' . $info['filename'] . '-' . $size . '.jpg' ;
    }

    // option 2
    public function middleware(){
        return [SkipIfBatchCancelled::class];
    }
}
