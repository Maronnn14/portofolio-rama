<?php

namespace Database\Seeders;

use App\Models\Contribution;
use App\Models\Education;
use App\Models\Experience;
use App\Models\PersonalInfo;
use App\Models\Project;
use App\Models\SiteSetting;
use App\Models\Skill;
use App\Models\SoftSkill;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

/**
 * Seeds the content added by PORTFOLIO_NAVIGATION_SPEC: education timeline,
 * soft skills, community contributions, and article bodies for existing entries.
 */
class PortfolioContentSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedSiteSettings();
        $this->seedEducation();
        $this->seedSoftSkills();
        $this->seedContributions();
        $this->seedPlaceholderCv();
        $this->backfillProjectArticles();
        $this->backfillExperienceArticles();
        $this->featureSkills();
    }

    private function seedSiteSettings(): void
    {
        if (! SiteSetting::query()->where('key', 'general')->exists()) {
            SiteSetting::query()->create([
                'key' => 'general',
                'value' => [
                    'site_title' => 'Rama Adin — Full Stack Developer',
                    'meta_description' => 'Full Stack Developer crafting digital experiences with code, clarity, and care.',
                    'og_image' => null,
                    'footer_text' => 'Built with passion & code',
                    'copyright_text' => null,
                    'contact_email' => null,
                    'availability' => 'Open to opportunities',
                ],
            ]);
        }
    }

    private function seedPlaceholderCv(): void
    {
        $existing = PersonalInfo::query()->where('key', 'cv_file')->value('value');

        if (filled($existing) && Storage::disk('public')->exists($existing)) {
            return;
        }

        $directory = storage_path('app/public/cv');
        $path = $directory.'/cv-placeholder.pdf';

        if (! is_dir($directory)) {
            mkdir($directory, 0775, true);
        }

        if (! is_file($path)) {
            file_put_contents($path, $this->makePlaceholderPdf());
        }

        PersonalInfo::query()->updateOrCreate(['key' => 'cv_file'], ['value' => 'cv/cv-placeholder.pdf']);
        PersonalInfo::query()->updateOrCreate(['key' => 'cv_label'], ['value' => 'Curriculum Vitae']);
        PersonalInfo::query()->updateOrCreate(['key' => 'cv_version'], ['value' => '2026.1']);
        PersonalInfo::query()->updateOrCreate(['key' => 'cv_updated_at'], ['value' => now()->toDateString()]);
    }

    private function makePlaceholderPdf(): string
    {
        $lines = [
            [26, 760, 'Rama Adin'],
            [13, 736, 'Full Stack Developer - Indonesia'],
            [11, 700, 'PLACEHOLDER CV'],
            [11, 680, 'This file is a placeholder generated for the portfolio demo.'],
            [11, 662, 'Replace it in Admin -> About Me -> Profile (Biodata & Branding) -> CV / Resume.'],
            [11, 620, 'Contact: rama@example.com  |  github.com/Maronnn14'],
        ];

        $content = '';
        foreach ($lines as [$size, $y, $text]) {
            $escaped = str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
            $content .= "BT /F1 {$size} Tf 72 {$y} Td ({$escaped}) Tj ET\n";
        }

        $objects = [
            1 => '<< /Type /Catalog /Pages 2 0 R >>',
            2 => '<< /Type /Pages /Kids [3 0 R] /Count 1 >>',
            3 => '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 4 0 R >> >> /Contents 5 0 R >>',
            4 => '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>',
            5 => '<< /Length '.strlen($content)." >>\nstream\n{$content}endstream",
        ];

        $pdf = "%PDF-1.4\n";
        $offsets = [];

        foreach ($objects as $number => $body) {
            $offsets[$number] = strlen($pdf);
            $pdf .= "{$number} 0 obj\n{$body}\nendobj\n";
        }

        $xref = strlen($pdf);
        $pdf .= "xref\n0 ".(count($objects) + 1)."\n0000000000 65535 f \n";

        foreach ($offsets as $offset) {
            $pdf .= sprintf("%010d 00000 n \n", $offset);
        }

        $pdf .= "trailer\n<< /Size ".(count($objects) + 1)." /Root 1 0 R >>\nstartxref\n{$xref}\n%%EOF\n";

        return $pdf;
    }

    private function seedEducation(): void
    {
        $entries = [
            [
                'institution' => 'University of Technology',
                'program' => 'Bachelor of Computer Science',
                'start_year' => '2020',
                'end_year' => '2024',
                'location' => 'Indonesia',
                'description' => 'Focused on software engineering, algorithms, and distributed systems. Graduated with honours while actively leading campus technology communities.',
                'highlights' => ['GPA 3.8 / 4.0', 'Thesis: real-time collaborative editing', 'Best Capstone Project finalist'],
                'sort_order' => 0,
            ],
            [
                'institution' => 'Bangkit Academy',
                'program' => 'Cloud Computing Learning Path',
                'start_year' => '2023',
                'end_year' => '2023',
                'location' => 'Remote',
                'description' => 'Intensive industry-led program covering cloud fundamentals, scalable backend services, and collaborative capstone delivery.',
                'highlights' => ['Graduated with distinction', 'Capstone built with Laravel + GCP'],
                'sort_order' => 1,
            ],
            [
                'institution' => 'SMA Negeri 1',
                'program' => 'Science Track',
                'start_year' => '2017',
                'end_year' => '2020',
                'location' => 'Indonesia',
                'description' => 'Built the first foundations in programming through the school computer club and national informatics olympiad preparation.',
                'highlights' => ['School computer club lead', 'Regional informatics olympiad participant'],
                'sort_order' => 2,
            ],
        ];

        foreach ($entries as $entry) {
            Education::query()->firstOrCreate(
                ['institution' => $entry['institution'], 'program' => $entry['program']],
                $entry,
            );
        }
    }

    private function seedSoftSkills(): void
    {
        $skills = [
            ['name' => 'Communication', 'description' => 'Translating technical decisions into clear language for teammates, stakeholders, and clients — written and verbal.'],
            ['name' => 'Teamwork', 'description' => 'Comfortable pairing, reviewing code, and sharing context so the whole team can move faster and safer.'],
            ['name' => 'Problem Solving', 'description' => 'Breaking ambiguous problems into small, testable steps and validating assumptions before writing production code.'],
            ['name' => 'Adaptability', 'description' => 'Quick to learn new stacks and tooling; has shipped with Laravel, React, Vue, and cloud platforms within the same year.'],
            ['name' => 'Time Management', 'description' => 'Plans work in small increments, keeps estimates honest, and communicates blockers early.'],
        ];

        foreach ($skills as $index => $skill) {
            SoftSkill::query()->firstOrCreate(
                ['name' => $skill['name']],
                array_merge($skill, ['sort_order' => $index]),
            );
        }
    }

    private function seedContributions(): void
    {
        $entries = [
            [
                'name' => 'Google Developer Student Clubs',
                'role' => 'Web Development Lead',
                'organization' => 'Google Developer Student Clubs',
                'period_start' => '2022',
                'period_end' => '2024',
                'description' => 'Led a 12-person web development division, running workshops and mentoring students building their first production applications.',
                'body' => <<<'MD'
## What I did

As the Web Development Lead I owned the technical curriculum for the campus chapter and ran a monthly hands-on workshop series.

### Highlights

- Grew the division from 4 to 12 active members.
- Ran 14 workshops covering HTML/CSS fundamentals, JavaScript, and Laravel.
- Mentored 3 teams through their capstone submissions; two placed in the top five.

> The best part of the role was watching someone ship their first deployed app after weeks of debugging.
MD,
                'links' => [
                    ['label' => 'Chapter profile', 'url' => 'https://developers.google.com/community/gdsc'],
                ],
                'cover_image' => 'https://picsum.photos/seed/gdsc/1200/700',
                'status' => 'published',
                'sort_order' => 0,
            ],
            [
                'name' => 'Open Source Contributions',
                'role' => 'Contributor',
                'organization' => 'Laravel ecosystem',
                'period_start' => '2021',
                'period_end' => 'Present',
                'description' => 'Small but steady contributions to open-source tooling: documentation fixes, bug reports, and pull requests for packages I use daily.',
                'body' => <<<'MD'
## Why I contribute

Open source is how I learned to code, so I try to leave the tools I depend on a little better than I found them.

### Recent work

- Documentation improvements for Filament and Laravel packages.
- Reproduced and reported edge-case bugs with minimal reproduction repositories.
- Reviewed pull requests in community packages during release windows.

You can find the public trail on my GitHub profile.
MD,
                'links' => [
                    ['label' => 'GitHub profile', 'url' => 'https://github.com/Maronnn14'],
                ],
                'cover_image' => 'https://picsum.photos/seed/opensource/1200/700',
                'status' => 'published',
                'sort_order' => 1,
            ],
            [
                'name' => 'Campus Coding Club',
                'role' => 'Mentor',
                'organization' => 'University of Technology',
                'period_start' => '2021',
                'period_end' => '2023',
                'description' => 'Mentored first-year students through their first collaborative project and introduced them to Git-based workflows.',
                'body' => <<<'MD'
## Mentoring approach

I paired with small groups on a single shared project for a semester, starting from `git clone` and ending with a deployed app.

### Outcomes

- 20+ students completed their first pull request.
- Introduced code review habits that the club kept after I graduated.
MD,
                'links' => [],
                'cover_image' => 'https://picsum.photos/seed/codingclub/1200/700',
                'status' => 'published',
                'sort_order' => 2,
            ],
        ];

        foreach ($entries as $entry) {
            Contribution::query()->firstOrCreate(
                ['name' => $entry['name'], 'role' => $entry['role']],
                $entry,
            );
        }
    }

    private function backfillProjectArticles(): void
    {
        $bodies = [
            'Portfolio Website' => <<<'MD'
## Overview

This portfolio is a deliberately small, fast site: semantic Blade templates, a Tailwind design system, and no client-side framework for the core experience.

## My role

Solo design and development — from the initial moodboard to deployment and the admin dashboard.

## Challenges

- Keeping the editorial, dark aesthetic readable at every breakpoint.
- Making every piece of copy editable without a redeploy.
- Shipping a guestbook and gallery without letting them dominate the page weight.

## Outcome

The site scores in the green on Lighthouse for accessibility and best practices, and the Filament dashboard lets me publish new case studies in minutes.
MD,
            'E-Commerce Platform' => <<<'MD'
## Overview

A full-stack storefront with a product catalog, cart persistence, and a lightweight admin for inventory.

## Problem

Small sellers often stitch together several tools that do not share a single source of truth.

## My role

Backend architecture, checkout flow, and the inventory dashboard.

## Outcome

Checkout completion improved in usability testing, and inventory updates stopped requiring spreadsheet exports.
MD,
            'Exam Management System' => <<<'MD'
## Overview

An online examination platform for educators: question banks, timed sessions, auto-grading, and result exports.

## Challenges

- Preventing accidental question leaks before an exam starts.
- Keeping the timer fair across unstable connections.
- Producing readable score reports for non-technical teachers.

## Outcome

Used by several classes during a pilot semester; grading time per exam dropped from hours to minutes.
MD,
            'Task Manager Pro' => <<<'MD'
## Overview

A collaborative task board with drag-and-drop columns, team workspaces, and a real-time activity log.

## What I learned

Real-time collaboration is mostly a conflict-resolution problem. Optimistic UI helps, but clear server-side ordering rules matter more.
MD,
            'API Gateway Service' => <<<'MD'
## Overview

A centralized gateway handling authentication, rate limiting, request routing, and API versioning for a microservices setup.

## Design notes

- Token introspection cached at the edge to keep p99 latency low.
- Per-key rate limits with a sliding window.
- Version routing kept explicit instead of implicit header magic.
MD,
            'Weather Dashboard' => <<<'MD'
## Overview

A small dashboard that turns a public weather API into an at-a-glance view with interactive charts.

## What I learned

Caching and graceful degradation matter more than chart polish when the upstream API is flaky.
MD,
        ];

        foreach ($bodies as $name => $body) {
            Project::query()
                ->where('name', $name)
                ->whereNull('body')
                ->update(['body' => $body, 'published_at' => now()]);
        }
    }

    private function backfillExperienceArticles(): void
    {
        $entries = [
            'Full Stack Developer' => [
                'achievements' => [
                    'Architected REST APIs serving 10K+ daily active users.',
                    'Cut average page load time by 38% through query and asset optimisation.',
                    'Introduced automated testing and CI checks for every pull request.',
                ],
                'body' => <<<'MD'
## Scope

I lead development of enterprise web applications built on Laravel and Vue.js, working closely with product and design.

## Day to day

- Designing database schemas and API contracts.
- Reviewing pull requests and mentoring two junior developers.
- Owning deployment pipelines and production incident response.

## Outcome

The platform now ships weekly, and the team has a documented release checklist instead of tribal knowledge.
MD,
            ],
            'Frontend Developer' => [
                'achievements' => [
                    'Delivered 15+ responsive client sites on schedule.',
                    'Raised average Lighthouse accessibility score to 95+.',
                ],
                'body' => <<<'MD'
## Scope

Built responsive, high-performance marketing and product sites for agency clients.

## Focus areas

- Accessibility and keyboard navigation.
- Motion design that respects reduced-motion preferences.
- Cross-browser compatibility and performance budgets.
MD,
            ],
            'Web Developer Intern' => [
                'achievements' => [
                    'Shipped UI components used across the SaaS dashboard.',
                    'Documented the onboarding flow for future interns.',
                ],
                'body' => <<<'MD'
## Scope

Contributed to a SaaS platform as part of a small agile team.

## What I worked on

- Frontend UI components and form validation.
- Backend API endpoints for internal reporting.
- Writing tests for the features I shipped.
MD,
            ],
        ];

        foreach ($entries as $role => $payload) {
            Experience::query()
                ->where('role', $role)
                ->where(fn ($query) => $query->whereNull('body')->orWhereNull('achievements'))
                ->get()
                ->each(function (Experience $experience) use ($payload): void {
                    $experience->fill(array_filter([
                        'body' => $experience->body ?: $payload['body'],
                        'achievements' => $experience->achievements ?: $payload['achievements'],
                        'status' => $experience->status ?: 'published',
                    ]))->save();
                });
        }
    }

    private function featureSkills(): void
    {
        if (Skill::query()->where('featured', true)->exists()) {
            return;
        }

        Skill::query()
            ->orderBy('sort_order')
            ->limit(10)
            ->update(['featured' => true]);
    }
}
