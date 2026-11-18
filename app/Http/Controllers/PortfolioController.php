<?php

namespace App\Http\Controllers;

use App\Models\ContactSubmission;
use App\Models\Contribution;
use App\Models\Education;
use App\Models\Experience;
use App\Models\GalleryImage;
use App\Models\Interest;
use App\Models\Project;
use App\Models\Skill;
use App\Models\Social;
use App\Models\SoftSkill;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PortfolioController extends Controller
{
    public function home(): View
    {
        return view('portfolio.home', [
            'projects' => Project::published()
                ->featured()
                ->with('images')
                ->orderBy('sort_order')
                ->take(3)
                ->get(),
            'skills' => Skill::featured()->orderBy('sort_order')->get(),
            'experience' => Experience::published()->work()->orderBy('sort_order')->take(3)->get(),
            'socials' => Social::where('visible', true)->orderBy('sort_order')->get(),
            'gallery' => GalleryImage::where('visible', true)->orderBy('sort_order')->orderByDesc('created_at')->get(),
            'metaTitle' => null,
        ]);
    }

    public function about(): View
    {
        return view('portfolio.about', [
            'education' => Education::orderBy('sort_order')->get(),
            'experience' => Experience::published()->orderBy('sort_order')->get(),
            'interests' => Interest::orderBy('sort_order')->get(),
            'professionalExperiences' => Experience::published()->work()->orderBy('sort_order')->get(),
            'metaTitle' => 'About Me',
        ]);
    }

    public function biodata(): View
    {
        return view('portfolio.about.biodata', [
            'education' => Education::orderBy('sort_order')->get(),
            'softSkills' => SoftSkill::orderBy('sort_order')->get(),
            'experience' => Experience::published()->work()->orderBy('sort_order')->take(3)->get(),
            'metaTitle' => 'Biodata & Personal Branding',
        ]);
    }

    public function cv(): View
    {
        return view('portfolio.about.cv', [
            'education' => Education::orderBy('sort_order')->get(),
            'experience' => Experience::published()->work()->orderBy('sort_order')->get(),
            'skills' => Skill::orderBy('sort_order')->get()->groupBy('category'),
            'metaTitle' => 'Download CV',
        ]);
    }

    public function interests(): View
    {
        return view('portfolio.about.interests', [
            'interests' => Interest::orderBy('sort_order')->get(),
            'metaTitle' => 'Interests & Hobbies',
        ]);
    }

    public function skills(): View
    {
        $skills = Skill::with(['projectLinks', 'galleryItems'])
            ->orderBy('sort_order')
            ->get();

        $techSkills = $skills->where('skill_type', 'tech')->values();
        $otherSkills = $skills->where('skill_type', 'other')->values();

        return view('portfolio.skills', [
            'skills' => $skills,
            'techSkills' => $techSkills,
            'otherSkills' => $otherSkills,
            'hardSkills' => $techSkills->groupBy('category'),
            'softSkills' => SoftSkill::orderBy('sort_order')->get(),
            'projects' => Project::published()->orderBy('sort_order')->get(),
            'categories' => $techSkills->pluck('category')->filter()->unique()->values(),
            'otherCategories' => $otherSkills->pluck('category')->filter()->unique()->values(),
            'metaTitle' => 'Skills',
        ]);
    }

    public function projects(Request $request): View
    {
        $query = Project::published()->with('images');

        if ($search = trim((string) $request->query('q'))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('short_desc', 'like', "%{$search}%")
                    ->orWhere('full_desc', 'like', "%{$search}%")
                    ->orWhere('body', 'like', "%{$search}%")
                    ->orWhere('tech', 'like', "%{$search}%");
            });
        }

        if ($category = $request->query('category')) {
            $query->where('category', $category);
        }

        if ($tech = $request->query('tech')) {
            $query->whereJsonContains('tech', $tech);
        }

        $projects = $query->orderBy('sort_order')->get();

        return view('portfolio.projects', [
            'projects' => $projects,
            'categories' => Project::published()->select('category')->distinct()->orderBy('category')->pluck('category')->filter(),
            'techs' => Project::published()
                ->pluck('tech')
                ->flatten()
                ->filter()
                ->unique()
                ->sort()
                ->values(),
            'filters' => [
                'q' => $request->query('q', ''),
                'category' => $category,
                'tech' => $tech,
            ],
            'metaTitle' => 'Coding Projects',
        ]);
    }

    public function projectShow(Project $project): View
    {
        abort_unless($project->status === 'published' && (! $project->published_at || $project->published_at->isPast()), 404);

        $project->load('images');

        $previous = Project::published()
            ->where('sort_order', '>', $project->sort_order)
            ->orderBy('sort_order')
            ->first();

        $next = Project::published()
            ->where('sort_order', '<', $project->sort_order)
            ->orderByDesc('sort_order')
            ->first();

        return view('portfolio.project-detail', [
            'project' => $project,
            'previous' => $previous,
            'next' => $next,
            'metaTitle' => $project->name,
            'metaDescription' => $project->short_desc,
            'ogImage' => $project->thumbnail,
            'ogType' => 'article',
        ]);
    }

    public function legacyProjectDetail(Request $request): View
    {
        $project = null;
        $id = $request->query('id');

        if ($id !== null && $id !== '') {
            $project = is_numeric($id)
                ? Project::with('images')->find((int) $id)
                : Project::with('images')->where('slug', $id)->first();
        }

        return view('portfolio.project-detail', [
            'project' => $project,
            'previous' => null,
            'next' => null,
            'metaTitle' => $project?->name ?? 'Project',
            'metaDescription' => $project?->short_desc,
            'ogImage' => $project?->thumbnail,
            'ogType' => 'article',
        ]);
    }

    public function experience(Request $request): View
    {
        $query = Experience::published()->work();

        if ($search = trim((string) $request->query('q'))) {
            $query->where(function ($q) use ($search) {
                $q->where('role', 'like', "%{$search}%")
                    ->orWhere('company', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('body', 'like', "%{$search}%");
            });
        }

        if ($type = $request->query('type')) {
            $query->where('type', $type);
        }

        return view('portfolio.experience.index', [
            'experiences' => $query->orderBy('sort_order')->get(),
            'types' => Experience::published()->work()->select('type')->distinct()->orderBy('type')->pluck('type')->filter(),
            'filters' => ['q' => $request->query('q', ''), 'type' => $type],
            'metaTitle' => 'Work Experience',
        ]);
    }

    public function experienceShow(Experience $experience): View
    {
        abort_unless($experience->status === 'published' && (! $experience->published_at || $experience->published_at->isPast()), 404);

        return view('portfolio.experience.show', [
            'experience' => $experience,
            'metaTitle' => $experience->role.' · '.$experience->company,
            'metaDescription' => str($experience->description)->limit(160),
            'ogImage' => $experience->cover_url,
            'ogType' => 'article',
        ]);
    }

    public function contributions(Request $request): View
    {
        $query = Contribution::published();

        if ($search = trim((string) $request->query('q'))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('organization', 'like', "%{$search}%")
                    ->orWhere('role', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('body', 'like', "%{$search}%");
            });
        }

        return view('portfolio.contributions.index', [
            'contributions' => $query->orderBy('sort_order')->get(),
            'filters' => ['q' => $request->query('q', '')],
            'metaTitle' => 'Contributions, Activities & Organizations',
        ]);
    }

    public function contributionShow(Contribution $contribution): View
    {
        abort_unless($contribution->status === 'published' && (! $contribution->published_at || $contribution->published_at->isPast()), 404);

        return view('portfolio.contributions.show', [
            'contribution' => $contribution,
            'metaTitle' => $contribution->name,
            'metaDescription' => str($contribution->description)->limit(160),
            'ogImage' => $contribution->cover_url,
            'ogType' => 'article',
        ]);
    }

    public function contact(): View
    {
        return view('portfolio.contact', [
            'socials' => Social::where('visible', true)->orderBy('sort_order')->get(),
            'metaTitle' => 'Contact',
        ]);
    }

    public function storeContact(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190'],
            'subject' => ['required', 'string', 'max:190'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
        ]);

        ContactSubmission::create(array_merge($data, ['ip_address' => $request->ip()]));

        return redirect()
            ->to(route('contact').'#contact-form')
            ->with('contact_success', 'Your message has been sent. I will reply as soon as possible.');
    }
}
