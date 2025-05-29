<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class ImageResize implements ShouldQueue
{
    use Queueable;

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
}
