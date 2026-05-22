<?php

namespace App\Http\Controllers;

use App\Models\Experience;
use App\Models\GalleryImage;
use App\Models\Interest;
use App\Models\Project;
use App\Models\Skill;
use App\Models\Social;
use Illuminate\View\View;

class PortfolioController extends Controller
{
    public function home(): View
    {
        return view('portfolio.home', [
            'projects' => Project::where('featured', true)->orderBy('sort_order')->get(),
            'skills' => Skill::orderBy('sort_order')->get(),
            'experience' => Experience::orderBy('sort_order')->get(),
            'socials' => Social::where('visible', true)->orderBy('sort_order')->get(),
            'gallery' => GalleryImage::where('visible', true)->orderBy('sort_order')->orderByDesc('created_at')->get(),
        ]);
    }

    public function about(): View
    {
        return view('portfolio.about', [
            'experience' => Experience::orderBy('sort_order')->get(),
            'interests' => Interest::orderBy('sort_order')->get(),
        ]);
    }

    public function projects(): View
    {
        return view('portfolio.projects', [
            'projects' => Project::orderBy('sort_order')->get(),
        ]);
    }

    public function projectDetail(): View
    {
        $project = null;
        if ($id = request('id')) {
            $project = Project::find($id);
        }
        return view('portfolio.project-detail', compact('project'));
    }

    public function skills(): View
    {
        return view('portfolio.skills', [
            'skills' => Skill::with(['projectLinks', 'galleryItems'])->orderBy('sort_order')->get(),
            'projects' => Project::all(),
        ]);
    }

    public function contact(): View
    {
        return view('portfolio.contact', [
            'socials' => Social::where('visible', true)->orderBy('sort_order')->get(),
        ]);
    }
}
