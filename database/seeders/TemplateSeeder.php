<?php

namespace Database\Seeders;

use App\Models\Template;
use Illuminate\Database\Seeder;

class TemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            [
                'name' => 'Classic Professional',
                'slug' => 'classic-professional',
                'description' => 'A timeless, ATS-friendly resume template',
                'is_premium' => false,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Modern Minimal',
                'slug' => 'modern-minimal',
                'description' => 'Clean and modern design with optimal whitespace',
                'is_premium' => false,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Executive Bold',
                'slug' => 'executive-bold',
                'description' => 'Premium template for senior professionals',
                'is_premium' => true,
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Creative Designer',
                'slug' => 'creative-designer',
                'description' => 'Stylish template for creative professionals',
                'is_premium' => true,
                'is_active' => true,
                'sort_order' => 4,
            ],
        ];

        foreach ($templates as $template) {
            Template::create($template);
        }

        $this->command->info('Templates created successfully!');
    }
}
