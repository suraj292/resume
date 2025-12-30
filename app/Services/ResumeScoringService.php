<?php

namespace App\Services;

class ResumeScoringService
{
    /**
     * Common keywords to look for if no Job Description is provided.
     */
    protected array $commonKeywords = [
        'communication', 'leadership', 'project management', 'teamwork',
        'problem solving', 'python', 'javascript', 'typescript', 'react', 'vue',
        'node', 'java', 'c++', 'sql', 'aws', 'docker', 'kubernetes', 'git',
        'agile', 'scrum', 'html', 'css', 'rest api'
    ];

    /**
     * Calculate the resume score.
     *
     * @param array $data The resume data structure.
     * @param string|null $jobDescription Optional job description text.
     * @return array
     */
    public function calculate(array $data, ?string $jobDescription = null): array
    {
        $score = 0;
        $checks = [];
        $suggestions = [];

        // 1. Contact Info (15%)
        $contactScore = 0;
        $basics = $data['basics'] ?? [];
        
        if (!empty($basics['fullName'])) $contactScore += 5;
        else $suggestions[] = 'Add your full name clearly.';

        if (!empty($basics['email'])) $contactScore += 5;
        else $suggestions[] = 'Add a professional email address.';

        if (!empty($basics['phone'])) $contactScore += 2.5;
        else $suggestions[] = 'Add a contact number.';

        if (!empty($basics['linkedin']) || !empty($basics['portfolio']) || !empty($basics['github'])) {
            $contactScore += 2.5;
        } else {
            $suggestions[] = 'Add a link to your LinkedIn, GitHub, or Portfolio.';
        }

        $score += $contactScore;
        $checks['contact_info'] = [
            'score' => $contactScore,
            'max' => 15,
            'passed' => $contactScore === 15
        ];

        // 2. Summary (10%)
        $summaryScore = 0;
        $summary = $basics['summary'] ?? '';
        $summaryLen = strlen($summary);
        
        if ($summaryLen > 150) { // Approx 20-30 words
            $summaryScore = 10;
        } elseif ($summaryLen > 0) {
            $summaryScore = 5;
            $suggestions[] = 'Your summary is too short. Aim for 2-3 sentences highlighting your key strengths.';
        } else {
            $suggestions[] = 'Add a professional summary to introduce yourself.';
        }

        $score += $summaryScore;
        $checks['summary'] = [
            'score' => $summaryScore,
            'max' => 10,
            'passed' => $summaryScore === 10
        ];

        // 3. Experience (25%)
        $experienceScore = 0;
        $experiences = $data['experience'] ?? [];
        // Filter out empty entries
        $validExp = array_filter($experiences, fn($e) => !empty($e['company']) && !empty($e['position']));
        $expCount = count($validExp);

        $hasDetailedExp = false;
        foreach ($validExp as $exp) {
            $responsibilities = $exp['responsibilities'] ?? [];
            if (is_array($responsibilities)) {
                foreach ($responsibilities as $resp) {
                    if (strlen($resp) > 50) { // Check for meaningful detail
                        $hasDetailedExp = true;
                        break 2;
                    }
                }
            }
        }

        if ($expCount >= 1) $experienceScore += 15;
        else $suggestions[] = 'Add at least one relevant work experience.';

        if ($hasDetailedExp) $experienceScore += 10;
        else if ($expCount >= 1) $suggestions[] = 'Improve your experience bullet points. Use action verbs and include results/metrics.';

        $score += $experienceScore;
        $checks['experience'] = [
            'score' => $experienceScore,
            'max' => 25,
            'passed' => $experienceScore === 25
        ];

        // 4. Skills (20%)
        $skillsScore = 0;
        $skillsData = $data['skills'] ?? [];
        $allSkills = [];
        
        // Handle both flattened array or categorized object structure
        if (isset($skillsData[0]) && is_string($skillsData[0])) {
             // Array of strings
             $allSkills = $skillsData;
        } else {
            // Object with categories
            foreach ($skillsData as $category => $skillList) {
                if (is_array($skillList)) {
                    $allSkills = array_merge($allSkills, $skillList);
                }
            }
        }
        
        $skillCount = count($allSkills);
        if ($skillCount >= 5) {
            $skillsScore = 20;
        } elseif ($skillCount > 0) {
            $skillsScore = 10;
            $suggestions[] = 'Add more technical or soft skills. Aim for at least 5 relevant skills.';
        } else {
            $suggestions[] = 'List your key skills to match job requirements.';
        }

        $score += $skillsScore;
        $checks['skills'] = [
            'score' => $skillsScore,
            'max' => 20,
            'passed' => $skillsScore === 20
        ];

        // 5. Education (10%)
        $educationScore = 0;
        $education = $data['education'] ?? [];
        $validEdu = array_filter($education, fn($e) => !empty($e['institution']));
        
        if (count($validEdu) >= 1) {
            $educationScore = 10;
        } else {
            $suggestions[] = 'Add your educational background.';
        }

        $score += $educationScore;
        $checks['education'] = [
            'score' => $educationScore,
            'max' => 10,
            'passed' => $educationScore === 10
        ];

        // 6. Keywords / Context (20%)
        $keywordScore = 0;
        $targetKeywords = $this->commonKeywords;
        
        if ($jobDescription) {
            $targetKeywords = $this->extractKeywordsSimple($jobDescription);
        }

        // Convert entire resume to string for search
        $resumeText = strtolower(json_encode($data));
        $matches = [];
        
        foreach ($targetKeywords as $keyword) {
             if (str_contains($resumeText, strtolower($keyword))) {
                 $matches[] = $keyword;
             }
        }

        $matchRate = count($targetKeywords) > 0 ? count($matches) / count($targetKeywords) : 0;
        
        // If JD provided, we are strict. If not, we check against common generic tech terms.
        // We cap the contribution at 20 points.
        // If match rate > 50%, we give full points for generic. Strictly scaling for JD.
        
        if ($jobDescription) {
            // Strict match for JD
            $keywordScore = min(20, ceil($matchRate * 30)); // Multiplier to help reach 20 easily if ~60% matched
        } else {
            // Baseline check
            $keywordScore = count($matches) >= 3 ? 20 : ceil((count($matches) / 3) * 20);
        }

        if ($keywordScore < 10) {
             $suggestions[] = $jobDescription 
                ? 'Your resume is missing key terms from the Job Description.' 
                : 'Include more common industry keywords (e.g., tools, languages, soft skills).';
        }

        $score += $keywordScore;
        $checks['keywords'] = [
            'score' => $keywordScore,
            'max' => 20,
            'passed' => $keywordScore >= 15
        ];

        return [
            'total_score' => min(100, round($score)),
            'breakdown' => $checks,
            'suggestions' => $suggestions,
            'matched_keywords' => array_slice($matches, 0, 10)
        ];
    }

    /**
     * Simple keyword extractor (regex based).
     */
    protected function extractKeywordsSimple(string $text): array
    {
        // Remove special chars and lowercase
        $clean = preg_replace('/[^a-zA-Z0-9\s]/', '', strtolower($text));
        $words = explode(' ', $clean);
        
        // Filter small words
        $keywords = array_filter($words, fn($w) => strlen($w) > 4);
        
        // Unique and re-index
        return array_values(array_unique($keywords));
    }
}
