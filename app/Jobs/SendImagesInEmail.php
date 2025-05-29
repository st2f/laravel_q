<?php

namespace App\Jobs;

use App\Mail\SendUserImage;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendImagesInEmail implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
