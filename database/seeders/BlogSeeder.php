<?php

namespace Database\Seeders;

use App\Models\Blog;
use App\Models\BlogCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $blogs = [
            [
                'title' => '10 Hidden Keywords That Will Triple Your Interview Chances in 2024',
                'excerpt' => 'Stop guessing what recruiters want. We analyzed 100,000 job descriptions to find the power words that consistently beat the ATS algorithms.',
                'content' => $this->getFullContent('keywords'),
                'category' => 'ATS Strategy',
                'author' => 'Sarah Jenkins',
                'read_time' => 8,
                'is_featured' => true,
                'featured_image' => 'https://picsum.photos/seed/resume/1200/600',
            ],
            [
                'title' => 'How to Explain Employment Gaps on Your Resume',
                'excerpt' => "Don't let a career break hurt your chances. Here are 5 ATS-friendly ways to frame your time off positively.",
                'content' => $this->getFullContent('employment-gaps'),
                'category' => 'Resume Tips',
                'author' => 'Michael Chen',
                'read_time' => 5,
                'is_featured' => false,
                'featured_image' => 'https://picsum.photos/seed/work/1200/600',
            ],
            [
                'title' => 'Best Fonts for Resumes: What Recruiters Actually Read',
                'excerpt' => 'Is Times New Roman dead? We rank the top 10 fonts for readability and parsing compatibility.',
                'content' => $this->getFullContent('fonts'),
                'category' => 'Templates & Design',
                'author' => 'Emily Rodriguez',
                'read_time' => 4,
                'is_featured' => false,
                'featured_image' => 'https://picsum.photos/seed/office/1200/600',
            ],
            [
                'title' => 'Resume vs CV: Which One Do You Really Need?',
                'excerpt' => 'The definitive guide to understanding the differences and when to use each format.',
                'content' => $this->getFullContent('resume-cv'),
                'category' => 'Career Advice',
                'author' => 'David Thompson',
                'read_time' => 6,
                'is_featured' => false,
                'featured_image' => 'https://picsum.photos/seed/tech/1200/600',
            ],
            [
                'title' => 'Action Verbs List: 100+ Words to Replace "Responsible For"',
                'excerpt' => 'Stop using passive language. Energize your bullet points with these powerful action verbs.',
                'content' => $this->getFullContent('action-verbs'),
                'category' => 'Resume Tips',
                'author' => 'Jennifer Lee',
                'read_time' => 3,
                'is_featured' => false,
                'featured_image' => 'https://picsum.photos/seed/writing/1200/600',
            ],
            [
                'title' => '5 Things You Should Remove From Your Resume Immediately',
                'excerpt' => 'These outdated resume elements are costing you interviews. Learn what to remove and what to add instead.',
                'content' => $this->getFullContent('remove-items'),
                'category' => 'Resume Tips',
                'author' => 'Robert Martinez',
                'read_time' => 5,
                'is_featured' => true,
                'featured_image' => 'https://picsum.photos/seed/trend1/1200/600',
            ],
            [
                'title' => 'How to Beat the Applicant Tracking System',
                'excerpt' => 'Master the art of ATS optimization with these proven strategies that get your resume past the bots.',
                'content' => $this->getFullContent('beat-ats'),
                'category' => 'ATS Strategy',
                'author' => 'Amanda Foster',
                'read_time' => 7,
                'is_featured' => false,
                'featured_image' => 'https://picsum.photos/seed/trend2/1200/600',
            ],
            [
                'title' => 'The Perfect Cover Letter Template for 2024',
                'excerpt' => 'Stand out from the crowd with a cover letter that actually gets read. Includes free downloadable template.',
                'content' => $this->getFullContent('cover-letter'),
                'category' => 'Cover Letters',
                'author' => 'Sarah Jenkins',
                'read_time' => 6,
                'is_featured' => false,
                'featured_image' => 'https://picsum.photos/seed/cover/1200/600',
            ],
            [
                'title' => 'How to Answer "Tell Me About Yourself" in Interviews',
                'excerpt' => 'The most common interview question deserves a strategic answer. Here\'s the framework that works every time.',
                'content' => $this->getFullContent('interview-question'),
                'category' => 'Interview Prep',
                'author' => 'Michael Chen',
                'read_time' => 5,
                'is_featured' => false,
                'featured_image' => 'https://picsum.photos/seed/interview/1200/600',
            ],
            [
                'title' => 'LinkedIn Profile Optimization: A Complete Guide',
                'excerpt' => 'Your LinkedIn profile is your digital resume. Learn how to optimize every section for maximum visibility.',
                'content' => $this->getFullContent('linkedin'),
                'category' => 'Professional Development',
                'author' => 'Emily Rodriguez',
                'read_time' => 9,
                'is_featured' => false,
                'featured_image' => 'https://picsum.photos/seed/linkedin/1200/600',
            ],
            [
                'title' => 'Remote Work Resume: How to Showcase Virtual Experience',
                'excerpt' => 'Remote work is the new normal. Here\'s how to highlight your virtual collaboration skills effectively.',
                'content' => $this->getFullContent('remote-work'),
                'category' => 'Resume Tips',
                'author' => 'David Thompson',
                'read_time' => 6,
                'is_featured' => false,
                'featured_image' => 'https://picsum.photos/seed/remote/1200/600',
            ],
            [
                'title' => 'Career Change Resume: Transferable Skills That Matter',
                'excerpt' => 'Switching careers? Learn how to position your transferable skills to land interviews in a new field.',
                'content' => $this->getFullContent('career-change'),
                'category' => 'Career Advice',
                'author' => 'Jennifer Lee',
                'read_time' => 7,
                'is_featured' => false,
                'featured_image' => 'https://picsum.photos/seed/change/1200/600',
            ],
            [
                'title' => 'Salary Negotiation: How to Ask for What You Deserve',
                'excerpt' => 'Don\'t leave money on the table. Master the art of salary negotiation with these proven tactics.',
                'content' => $this->getFullContent('salary'),
                'category' => 'Career Advice',
                'author' => 'Robert Martinez',
                'read_time' => 8,
                'is_featured' => false,
                'featured_image' => 'https://picsum.photos/seed/salary/1200/600',
            ],
        ];

        foreach ($blogs as $blogData) {
            $category = BlogCategory::where('name', $blogData['category'])->first();
            
            if ($category) {
                Blog::create([
                    'title' => $blogData['title'],
                    'slug' => Str::slug($blogData['title']),
                    'excerpt' => $blogData['excerpt'],
                    'content' => $blogData['content'],
                    'category_id' => $category->id,
                    'author' => $blogData['author'],
                    'read_time' => $blogData['read_time'],
                    'is_featured' => $blogData['is_featured'],
                    'is_published' => true,
                    'published_at' => now()->subDays(rand(1, 30)),
                    'featured_image' => $blogData['featured_image'],
                    'views_count' => rand(100, 5000),
                    'sort_order' => 0,
                ]);
            }
        }
    }

    /**
     * Get full content for a blog post.
     */
    private function getFullContent(string $type): string
    {
        $contents = [
            'keywords' => '
                <p class="lead text-xl text-slate-500 font-light mb-8">
                    Most job seekers don\'t realize that their resume isn\'t read by a human first. It\'s read by a bot. And that bot is looking for very specific signals.
                </p>

                <h2>Why Keywords Matter</h2>
                <p>
                    Applicant Tracking Systems (ATS) are designed to filter out unqualified candidates. They do this by scanning for keywords found in the job description. If your resume lacks these specific terms, you might be rejected instantly, regardless of your actual qualifications.
                </p>
                <p>
                    However, not all keywords are created equal. Some are "hard skills" (like Python, SQL, or Figma), while others are "soft skills" or action verbs that demonstrate impact.
                </p>

                <h2>The Top 5 Action Verbs</h2>
                <p>Stop using "Responsible for" or "Managed". These words are passive. Instead, use words that imply result and ownership:</p>
                <ul>
                    <li><strong>Spearheaded:</strong> Shows leadership and initiative.</li>
                    <li><strong>Orchestrated:</strong> Implies handling complexity.</li>
                    <li><strong>Accelerated:</strong> Demonstrates efficiency and speed.</li>
                    <li><strong>Optimized:</strong> Shows you improved a process.</li>
                    <li><strong>Revitalized:</strong> Great for turning around failing projects.</li>
                </ul>

                <blockquote>
                    "The best resumes don\'t just list duties; they tell a story of impact using data and strong verbs."
                </blockquote>

                <h2>Conclusion</h2>
                <p>
                    Optimizing for ATS doesn\'t mean writing like a robot. It means ensuring the language you use to describe your human achievements aligns with the language the machine is programmed to value.
                </p>
            ',
            'employment-gaps' => '
                <p class="lead text-xl text-slate-500 font-light mb-8">
                    Employment gaps are more common than you think. Here\'s how to address them confidently on your resume.
                </p>

                <h2>Be Honest and Strategic</h2>
                <p>
                    The key to explaining employment gaps is honesty combined with strategic framing. Don\'t try to hide gaps—recruiters will notice. Instead, use them to show personal growth and resilience.
                </p>

                <h2>5 Ways to Frame Your Gap</h2>
                <ul>
                    <li><strong>Professional Development:</strong> Took courses, earned certifications, or learned new skills.</li>
                    <li><strong>Freelance/Consulting:</strong> Worked on independent projects or consulting gigs.</li>
                    <li><strong>Family Care:</strong> Took time to care for family members—this is valid and respected.</li>
                    <li><strong>Health Recovery:</strong> Focused on health and wellness—keep it brief and professional.</li>
                    <li><strong>Career Transition:</strong> Explored new career paths and industries.</li>
                </ul>

                <h2>What to Include</h2>
                <p>
                    If your gap involved any productive activity—volunteering, side projects, skill development—include it on your resume. This shows you remained engaged and motivated during your time away.
                </p>
            ',
            'fonts' => '
                <p class="lead text-xl text-slate-500 font-light mb-8">
                    Your font choice matters more than you think. Here\'s what recruiters and ATS systems prefer.
                </p>

                <h2>The Top 5 Resume Fonts</h2>
                <ol>
                    <li><strong>Calibri:</strong> Modern, clean, and ATS-friendly.</li>
                    <li><strong>Arial:</strong> Classic and universally readable.</li>
                    <li><strong>Helvetica:</strong> Professional and timeless.</li>
                    <li><strong>Garamond:</strong> Elegant for creative roles.</li>
                    <li><strong>Georgia:</strong> Readable and sophisticated.</li>
                </ol>

                <h2>Fonts to Avoid</h2>
                <p>
                    Avoid decorative fonts like Comic Sans, Papyrus, or anything overly stylized. These can confuse ATS systems and appear unprofessional to human readers.
                </p>

                <h2>Font Size Matters</h2>
                <p>
                    Use 10-12pt for body text and 14-16pt for your name. Anything smaller is hard to read; anything larger looks amateurish.
                </p>
            ',
            'resume-cv' => '
                <p class="lead text-xl text-slate-500 font-light mb-8">
                    Resume or CV? The answer depends on your location, industry, and career stage.
                </p>

                <h2>Resume vs CV: Key Differences</h2>
                <p>
                    A <strong>resume</strong> is a concise 1-2 page document highlighting your most relevant experience. A <strong>CV (Curriculum Vitae)</strong> is a comprehensive document listing all your academic and professional achievements.
                </p>

                <h2>When to Use a Resume</h2>
                <ul>
                    <li>Applying for jobs in the US, Canada, or most corporate roles</li>
                    <li>Targeting specific positions with tailored content</li>
                    <li>Early to mid-career professionals</li>
                </ul>

                <h2>When to Use a CV</h2>
                <ul>
                    <li>Academic, research, or medical positions</li>
                    <li>International job applications (especially in Europe)</li>
                    <li>Grant applications or fellowship programs</li>
                </ul>
            ',
            'action-verbs' => '
                <p class="lead text-xl text-slate-500 font-light mb-8">
                    Transform your resume from passive to powerful with these action verbs.
                </p>

                <h2>Leadership Verbs</h2>
                <p>Spearheaded, Directed, Orchestrated, Championed, Pioneered, Mobilized</p>

                <h2>Achievement Verbs</h2>
                <p>Achieved, Exceeded, Surpassed, Outperformed, Delivered, Accomplished</p>

                <h2>Improvement Verbs</h2>
                <p>Optimized, Streamlined, Enhanced, Revitalized, Transformed, Modernized</p>

                <h2>How to Use Them</h2>
                <p>
                    Don\'t just replace "Responsible for" with an action verb. Pair it with quantifiable results: "Spearheaded a cross-functional initiative that increased revenue by 25%."
                </p>
            ',
        ];

        // Default content for types not specified
        $defaultContent = '
            <p class="lead text-xl text-slate-500 font-light mb-8">
                This is a comprehensive guide to help you succeed in your job search and career development.
            </p>

            <h2>Introduction</h2>
            <p>
                In today\'s competitive job market, standing out requires more than just qualifications. You need to present yourself strategically and professionally.
            </p>

            <h2>Key Takeaways</h2>
            <ul>
                <li>Focus on results and quantifiable achievements</li>
                <li>Tailor your approach to each opportunity</li>
                <li>Stay current with industry trends and best practices</li>
                <li>Continuously develop your professional skills</li>
            </ul>

            <h2>Conclusion</h2>
            <p>
                Success in your career journey requires preparation, strategy, and persistence. Use these insights to position yourself for the opportunities you deserve.
            </p>
        ';

        return $contents[$type] ?? $defaultContent;
    }
}
