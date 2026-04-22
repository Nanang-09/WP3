<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Portfolio;
use App\Models\Testimonial;

class HomeController extends Controller
{
    public function index()
    {
        $services = Service::active()->featured()->orderBy('sort_order')->take(6)->get();
        $portfolios = Portfolio::featured()->latest()->take(4)->get();
        $testimonials = Testimonial::active()->latest()->take(4)->get();

        return view('home', compact('services', 'portfolios', 'testimonials'));
    }
}
