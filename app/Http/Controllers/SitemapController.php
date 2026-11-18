<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\Contribution;
use App\Models\Experience;
use App\Models\Project;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $urls = collect([
            $this->url(route('home'), null, '1.0', 'weekly'),
            $this->url(route('about.biodata'), null, '0.8', 'monthly'),
            $this->url(route('about.cv'), null, '0.7', 'monthly'),
            $this->url(route('about.interests'), null, '0.6', 'monthly'),
            $this->url(route('about.blog'), null, '0.7', 'weekly'),
            $this->url(route('skills'), null, '0.8', 'monthly'),
            $this->url(route('projects.index'), null, '0.9', 'weekly'),
            $this->url(route('experience.index'), null, '0.8', 'monthly'),
            $this->url(route('contributions.index'), null, '0.7', 'monthly'),
            $this->url(route('contact'), null, '0.6', 'yearly'),
        ]);

        Project::published()->orderByDesc('updated_at')->get()->each(function (Project $project) use ($urls) {
            $urls->push($this->url(route('projects.show', $project), $project->updated_at, '0.7', 'monthly'));
        });

        Experience::published()->orderByDesc('updated_at')->get()->each(function (Experience $experience) use ($urls) {
            $urls->push($this->url(route('experience.show', $experience), $experience->updated_at, '0.6', 'monthly'));
        });

        BlogPost::published()->orderByDesc('updated_at')->get()->each(function (BlogPost $post) use ($urls) {
            $urls->push($this->url(route('about.blog.show', $post), $post->updated_at, '0.6', 'monthly'));
        });

        Contribution::published()->orderByDesc('updated_at')->get()->each(function (Contribution $contribution) use ($urls) {
            $urls->push($this->url(route('contributions.show', $contribution), $contribution->updated_at, '0.6', 'monthly'));
        });

        return response()
            ->view('sitemap', ['urls' => $urls->filter()->values()])
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    public function robots(): Response
    {
        $lines = [
            'User-agent: *',
            'Disallow: /admin',
            'Disallow: /admin/',
            '',
            'Sitemap: '.route('sitemap'),
        ];

        return response(implode("\n", $lines)."\n")
            ->header('Content-Type', 'text/plain; charset=UTF-8');
    }

    /**
     * @return array{loc: string, lastmod: string|null, changefreq: string, priority: string}
     */
    private function url(string $loc, ?\DateTimeInterface $lastmod, string $priority, string $changefreq): array
    {
        return [
            'loc' => $loc,
            'lastmod' => $lastmod?->format(DATE_ATOM),
            'changefreq' => $changefreq,
            'priority' => $priority,
        ];
    }
}
