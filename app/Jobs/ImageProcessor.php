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

    private $email;
    private $filepath;

    /**
     * Create a new job instance.
     */
    public function __construct(string $email, string $filepath)
    {
        $this->email = $email;
        $this->filepath = storage_path('app/public/') . $filepath;

        if (!file_exists($this->filepath)) {
            throw ValidationException::withMessages([
                'filepath' => 'Filepath does not exist: ' . $this->filepath,
            ]);
        }
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //dd($this->email, $this->filepath);
        try {
            $sendUserImage = new SendUserImage();
            $sendUserImage->attach($this->filepath);

            Mail::to($this->email)
                ->send($sendUserImage);

        } catch (\Throwable $th) {
            die('Could not send email: ' . $th->getMessage());
        }

    }
}
