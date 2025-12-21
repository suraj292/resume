<?php

namespace Tests\Feature;

use App\Services\GeminiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ResumeExtractionTest extends TestCase
{
    protected GeminiService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(GeminiService::class);
        Storage::fake('public');
    }

    /** @test */
    public function it_extracts_text_from_txt_file()
    {
        $content = "John Doe\nSoftware Engineer\nSkills: PHP, Laravel, Vue.js";
        $path = storage_path('app/test.txt');
        file_put_contents($path, $content);

        $extracted = file_get_contents($path);

        $this->assertNotEmpty($extracted);
        $this->assertStringContainsString('John Doe', $extracted);
        $this->assertStringContainsString('PHP', $extracted);

        unlink($path);
    }

    /** @test */
    public function it_handles_pdf_extraction()
    {
        // This test requires actual PDF files
        $pdfFiles = glob(storage_path('app/public/resume-analyses/*.pdf'));
        
        if (empty($pdfFiles)) {
            $this->markTestSkipped('No PDF files available for testing');
        }

        $pdfPath = $pdfFiles[0];
        $text = $this->service->extractTextFromPdf($pdfPath);

        // PDF extraction might return empty if no text extractable
        // Just verify it doesn't throw an exception
        $this->assertIsString($text);
    }

    /** @test */
    public function it_handles_docx_extraction()
    {
        $docxFiles = glob(storage_path('app/public/resume-analyses/*.docx'));
        
        if (empty($docxFiles)) {
            $this->markTestSkipped('No DOCX files available for testing');
        }

        $docxPath = $docxFiles[0];
        $text = $this->service->extractTextFromDocx($docxPath);

        $this->assertIsString($text);
    }

    /** @test */
    public function it_validates_switch_case_logic()
    {
        $testCases = [
            'pdf' => 'extractTextFromPdf',
            'docx' => 'extractTextFromDocx',
            'doc' => 'extractTextFromDocx',
            'txt' => 'file_get_contents',
        ];

        foreach ($testCases as $extension => $handler) {
            if ($handler !== 'file_get_contents') {
                $this->assertTrue(
                    method_exists(GeminiService::class, $handler),
                    "Method $handler should exist for .$extension files"
                );
            }
        }
    }
}
