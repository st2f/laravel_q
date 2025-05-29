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
        $info = pathinfo($this->filepath);

        // create image manager with desired driver
        $manager = new ImageManager(new Driver());

        $image = $manager->read($this->filepath);
        $image->scale(height: $this->size);
        $encoded = $image->toJpg();
        $encoded->save( self::newFilename($info, $this->size));
    }

    public static function newFilename($info, $size): string
    {
        return $info['dirname'] . '/' . $info['filename'] . '-' . $size . '.jpg' ;
    }
}
