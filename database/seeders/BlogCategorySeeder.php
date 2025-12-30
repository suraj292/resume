<?php

namespace Database\Seeders;

use App\Models\BlogCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BlogCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Resume Tips',
            'ATS Strategy',
            'Career Advice',
            'Cover Letters',
            'Interview Prep',
            'Templates & Design',
            'Job Search',
            'Professional Development',
        ];

        foreach ($categories as $category) {
            BlogCategory::create([
                'name' => $category,
                'slug' => Str::slug($category),
            ]);
        }
    }
}
