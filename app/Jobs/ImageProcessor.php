<?php

namespace App\Jobs;

use App\Mail\SendUserImage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class ImageProcessor implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public string $email, public string $filename)
    {
    }

    /**
     * Execute the job.
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
        $sizes = [100, 300, 700];

        foreach ($sizes as $size) {
            $jobs[] = new ImageResize($filepath, $size);
        }

        // sequential
        Bus::chain([
            ...$jobs,
            new SendImagesInEmail($this->email, $filepath, $sizes),
            function() {
                // another background job
            }
        ])->catch(function() {
            // do something
        })->dispatch();
    }
}
