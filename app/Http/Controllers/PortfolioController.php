<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class PortfolioController extends Controller
{
    public function home(): View
    {
        return view('portfolio.home');
    }

    public function about(): View
    {
        return view('portfolio.about');
    }

    public function projects(): View
    {
        return view('portfolio.projects');
    }

    public function projectDetail(): View
    {
        return view('portfolio.project-detail');
    }

    public function skills(): View
    {
        return view('portfolio.skills');
    }

    public function contact(): View
    {
        return view('portfolio.contact');
    }
}
