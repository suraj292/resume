<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Resume</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            line-height: 1.4;
            color: #334155;
        }
        
        .container {
            padding: 40px 50px;
        }
        
        .two-column {
            display: table;
            width: 100%;
        }
        
        .left-column {
            display: table-cell;
            width: 33%;
            padding-right: 20px;
            vertical-align: top;
        }
        
        .right-column {
            display: table-cell;
            width: 67%;
            vertical-align: top;
        }
        
        /* Header */
        .header-name {
            font-size: 28px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: -0.5px;
            color: #0f172a;
            margin-bottom: 5px;
        }
        
        .header-title {
            font-size: 16px;
            font-weight: bold;
            color: <?php echo $accentColor ?? '#6366f1'; ?>;
            margin-bottom: 15px;
        }
        
        /* Section Titles */
        .section-title {
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #94a3b8;
            border-bottom: 2px solid <?php echo $accentColor ?? '#6366f1'; ?>;
            padding-bottom: 3px;
            margin-bottom: 10px;
            margin-top: 15px;
        }
        
        .section-title-left {
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #94a3b8;
            border-bottom: 2px solid <?php echo $accentColor ?? '#6366f1'; ?>;
            padding-bottom: 3px;
            margin-bottom: 8px;
            margin-top: 12px;
        }
        
        .section-title-left:first-child {
            margin-top: 0;
        }
        
        /* Contact */
        .contact-item {
            font-size: 9px;
            color: #64748b;
            margin-bottom: 5px;
            line-height: 1.5;
        }
        
        .contact-icon {
            color: <?php echo $accentColor ?? '#6366f1'; ?>;
            margin-right: 5px;
        }
        
        /* Skills */
        .skill-category {
            margin-bottom: 8px;
        }
        
        .skill-category-title {
            font-size: 8px;
            font-weight: bold;
            color: #94a3b8;
            margin-bottom: 3px;
        }
        
        .skill-tag {
            display: inline-block;
            background: <?php echo $accentColor ?? '#6366f1'; ?>20;
            color: <?php echo $accentColor ?? '#6366f1'; ?>;
            font-size: 8px;
            font-weight: 600;
            padding: 3px 8px;
            border-radius: 10px;
            margin-right: 3px;
            margin-bottom: 3px;
            border: 1px solid <?php echo $accentColor ?? '#6366f1'; ?>40;
        }
        
        /* Education */
        .education-item {
            margin-bottom: 8px;
            padding-left: 8px;
            border-left: 2px solid <?php echo $accentColor ?? '#6366f1'; ?>60;
        }
        
        .education-degree {
            font-size: 9px;
            font-weight: bold;
            color: #1e293b;
            margin-bottom: 2px;
        }
        
        .education-school {
            font-size: 8px;
            color: #64748b;
            margin-bottom: 1px;
        }
        
        .education-year {
            font-size: 8px;
            color: <?php echo $accentColor ?? '#6366f1'; ?>;
            font-weight: 600;
        }
        
        /* Summary */
        .summary-text {
            font-size: 9px;
            color: #64748b;
            line-height: 1.6;
            text-align: justify;
        }
        
        /* Experience */
        .experience-item {
            margin-bottom: 12px;
            page-break-inside: avoid;
            padding-left: 10px;
            border-left: 2px solid <?php echo $accentColor ?? '#6366f1'; ?>50;
            position: relative;
        }
        
        .experience-item::before {
            content: '';
            position: absolute;
            left: -4px;
            top: 5px;
            width: 6px;
            height: 6px;
            background: <?php echo $accentColor ?? '#6366f1'; ?>;
            border-radius: 50%;
        }
        
        .experience-title {
            font-size: 11px;
            font-weight: bold;
            color: #0f172a;
            margin-bottom: 2px;
        }
        
        .experience-company {
            font-size: 9px;
            color: <?php echo $accentColor ?? '#6366f1'; ?>;
            font-weight: 600;
            margin-bottom: 1px;
        }
        
        .experience-date {
            font-size: 8px;
            color: #94a3b8;
            margin-bottom: 5px;
            background: <?php echo $accentColor ?? '#6366f1'; ?>15;
            color: <?php echo $accentColor ?? '#6366f1'; ?>;
            padding: 2px 6px;
            border-radius: 8px;
            display: inline-block;
            font-weight: 600;
        }
        
        .experience-description {
            font-size: 9px;
            color: #64748b;
            line-height: 1.5;
        }
        
        .experience-description ul {
            margin-left: 12px;
            margin-top: 3px;
        }
        
        .experience-description li {
            margin-bottom: 3px;
        }

        /* Achievements */
        .achievement-item {
            margin-bottom: 12px;
            page-break-inside: avoid;
        }

        .achievement-bullet {
            color: #d97706;
            font-weight: bold;
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="two-column">
            <!-- Left Column -->
            <div class="left-column">
                <!-- Contact -->
                <div class="section-title-left">CONTACT</div>
                @if(!empty($data['personal']['email']))
                <div class="contact-item">
                    <span class="contact-icon">✉</span> {{ $data['personal']['email'] }}
                </div>
                @endif
                @if(!empty($data['personal']['phone']))
                <div class="contact-item">
                    <span class="contact-icon">☎</span> {{ $data['personal']['phone'] }}
                </div>
                @endif
                @if(!empty($data['personal']['location']))
                <div class="contact-item">
                    <span class="contact-icon">📍</span> {{ $data['personal']['location'] }}
                </div>
                @endif
                @if(!empty($data['personal']['linkedin']))
                <div class="contact-item">
                    <span class="contact-icon">🔗</span> {{ $data['personal']['linkedin'] }}
                </div>
                @endif
                @if(!empty($data['personal']['github']))
                <div class="contact-item">
                    <span class="contact-icon">⚡</span> {{ $data['personal']['github'] }}
                </div>
                @endif

                <!-- Skills -->
                @if(!empty($data['skills']) && count($data['skills']) > 0)
                <div class="section-title-left">SKILLS</div>
                @php
                    $skillsByCategory = [
                        'Backend' => [],
                        'Frontend' => [],
                        'DevOps' => [],
                        'Other' => []
                    ];
                    foreach ($data['skills'] as $skill) {
                        if (isset($skill['category'])) {
                            $skillsByCategory[$skill['category']][] = $skill['name'];
                        } else {
                            $skillsByCategory['Other'][] = $skill['name'];
                        }
                    }
                @endphp
                @foreach($skillsByCategory as $category => $skills)
                    @if(count($skills) > 0)
                    <div class="skill-category">
                        <div class="skill-category-title">{{ $category }}</div>
                        <div>
                            @foreach($skills as $skill)
                            <span class="skill-tag">{{ $skill }}</span>
                            @endforeach
                        </div>
                    </div>
                    @endif
                @endforeach
                @endif

                <!-- Education -->
                @if(!empty($data['education']) && count($data['education']) > 0)
                <div class="section-title-left">EDUCATION</div>
                @foreach($data['education'] as $edu)
                <div class="education-item">
                    <div class="education-degree">{{ $edu['degree'] ?? '' }}</div>
                    <div class="education-school">{{ $edu['school'] ?? '' }}</div>
                    <div class="education-year">{{ $edu['graduationDate'] ?? '' }}</div>
                </div>
                @endforeach
                @endif
            </div>

            <!-- Right Column -->
            <div class="right-column">
                <!-- Header -->
                <div class="header-name">{{ $data['personal']['name'] ?? 'YOUR NAME' }}</div>
                <div class="header-title">{{ $data['personal']['title'] ?? 'Professional Title' }}</div>

                <!-- Summary -->
                @if(!empty($data['summary']))
                <div class="section-title">SUMMARY</div>
                <div class="summary-text">{{ $data['summary'] }}</div>
                @endif

                <!-- Experience -->
                @if(!empty($data['experience']) && count($data['experience']) > 0)
                <div class="section-title">WORK EXPERIENCE</div>
                @foreach($data['experience'] as $job)
                <div class="experience-item">
                    <div class="experience-title">{{ $job['title'] ?? '' }}</div>
                    <div class="experience-company">{{ $job['company'] ?? '' }}</div>
                    <div class="experience-date">
                        {{ $job['startDate'] ?? '' }} - {{ $job['endDate'] ?? 'Present' }}
                    </div>
                    @if(!empty($job['description']))
                    <div class="experience-description">
                        <ul>
                            @foreach(explode("\n", $job['description']) as $bullet)
                                @if(trim($bullet))
                                <li>{{ trim($bullet, '• -') }}</li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>
                @endforeach
                @endif

                <!-- Achievements -->
                @if(!empty($data['achievements']) && count($data['achievements']) > 0)
                <div class="section-title">KEY ACHIEVEMENTS</div>
                <div class="experience-description">
                    <ul>
                        @foreach($data['achievements'] as $achievement)
                            @if(is_string($achievement) && trim($achievement))
                            <li>{{ trim($achievement) }}</li>
                            @endif
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>
        </div>
    </div>
</body>
</html>
