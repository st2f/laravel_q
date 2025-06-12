<?php

namespace App\Jobs;

use App\Mail\SendUserPdf;
use App\Services\UserStorageService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Settings;

class WordProcessor implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public string $email,
        public string $filename
    ){}

    public function handle(): void {
        $basePath = resolve(UserStorageService::class)->basePath();
        $filepath = $basePath . $this->filename;
        $info = pathinfo($basePath . $this->filename);

        $rendererName = Settings::PDF_RENDERER_DOMPDF;
        $rendererLibraryPath = base_path('/vendor/dompdf/dompdf');
        Settings::setPdfRenderer($rendererName, $rendererLibraryPath);

        $wordDocument = IOFactory::load($filepath);

        $writer = IOFactory::createWriter($wordDocument, 'PDF');
        $pdfPath = $info['dirname'] . '/' . $info['filename'] . '.pdf';
        $writer->save($pdfPath);

        $sendUsersPdf = new SendUserPdf();
        $sendUsersPdf->attach($pdfPath);

        \Mail::to($this->email)->send($sendUsersPdf);
        unlink($filepath);
    }
}
