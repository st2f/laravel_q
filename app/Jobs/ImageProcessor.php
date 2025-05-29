<?php

namespace App\Jobs;

use App\Mail\SendUserImage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
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

        ImageResize::dispatch($filepath, 500);
        ImageResize::dispatch($filepath, 600);
        ImageResize::dispatch($filepath, 700);

        $sendUserImage = new SendUserImage();
        $sendUserImage
            ->attach(ImageResize::newFilename($info, 500))
            ->attach(ImageResize::newFilename($info, 600))
            ->attach(ImageResize::newFilename($info, 700));

        Mail::to($this->email)
            ->send($sendUserImage);
    }
}
