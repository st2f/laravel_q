<?php

namespace App\Jobs;

use App\Mail\SendUserPdf;
use App\Models\User;
use App\Services\UserStorageService;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Settings;

class WordProcessor implements ShouldQueue {
    use Dispatchable, Batchable, Queueable, InteractsWithQueue, SerializesModels;

    public function __construct(
        public string $email,
        public string $filename
    ){}

    public function handle(): void {

        $user = User::where('email', $this->email)->firstOrFail();

        if (!$user->id || !$this->filename) {
            $this->batch()->cancel();
        }

        $storage = UserStorageService::forUser($user->id);
        $filepath = $storage->prepareLocalFile($this->filename);

        $rendererName = Settings::PDF_RENDERER_DOMPDF;
        $rendererLibraryPath = base_path('/vendor/dompdf/dompdf');
        Settings::setPdfRenderer($rendererName, $rendererLibraryPath);

        $wordDocument = IOFactory::load($filepath);

        $writer = IOFactory::createWriter($wordDocument, 'PDF');
        $info = pathinfo($filepath);
        $pdfPath = $info['dirname'] . '/' . $info['filename'] . '.pdf';
        $writer->save($pdfPath);

        $sendUsersPdf = new SendUserPdf();
        $sendUsersPdf->attach($pdfPath);

        \Mail::to($this->email)->send($sendUsersPdf);
        unlink($filepath);

        //$this->dispatch('word-processed');
    }
}
