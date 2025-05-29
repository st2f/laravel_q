<?php

namespace App\Jobs;

use App\Mail\SendUserImage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendImagesInEmail implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $email,
        public string $filepath,
        public array $sizes
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $sendUserImage = new SendUserImage();

        foreach ($this->sizes as $size) {
            $sendUserImage->attach(
                ImageResize::newFilename($this->filepath, $size)
            );
        }

        Mail::to($this->email)->send($sendUserImage);
    }
}
