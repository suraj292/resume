#!/usr/bin/env php
<?php

// Test PDF export functionality

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Barryvdh\DomPDF\Facade\Pdf;

echo "Testing PDF Generation...\n\n";

// Sample resume data
$resumeData = [
    'personal' => [
        'name' => 'John Doe',
        'title' => 'Software Engineer',
        'email' => 'john@example.com',
        'phone' => '+1234567890',
        'location' => 'San Francisco, CA',
        'linkedin' => 'linkedin.com/in/johndoe',
        'github' => 'github.com/johndoe'
    ],
    'summary' => 'Experienced software engineer with 5+ years of experience.',
    'skills' => [
        ['name' => 'JavaScript', 'category' => 'Frontend'],
        ['name' => 'PHP', 'category' => 'Backend'],
        ['name' => 'Docker', 'category' => 'DevOps']
    ],
    'experience' => [
        [
            'title' => 'Senior Developer',
            'company' => 'Tech Corp',
            'startDate' => 'Jan 2020',
            'endDate' => 'Present',
            'description' => "Built amazing features\nImproved performance by 50%"
        ]
    ],
    'education' => [
        [
            'degree' => 'BS Computer Science',
            'school' => 'University of Tech',
            'graduationDate' => '2019'
        ]
    ],
    'achievements' => [
        'Led migration to microservices',
        'Reduced deployment time by 70%',
       'Mentored 5 junior developers'
    ]
];

try {
    echo "Generating PDF...\n";
    
    $pdf = Pdf::loadView('pdf.resume', [
        'data' => $resumeData,
        'template' => 'default',
        'accentColor' => '#6366f1'
    ]);
    
    $pdf->setPaper('a4', 'portrait');
    
    $outputPath = __DIR__ . '/storage/test-resume.pdf';
    $pdf->save($outputPath);
    
    echo "✅ PDF generated successfully!\n";
    echo "📄 Saved to: $outputPath\n";
    echo "📊 File size: " . filesize($outputPath) . " bytes\n";
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
