<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Property;
use App\Models\Testimonial;
use App\Models\ContactSubmission;
use App\Models\Page;
use App\Models\BlogPost;
use App\Mail\ContactFormSubmitted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class PublicSiteController extends Controller
{
    protected User $tenant;
    protected string $template;

    public function __construct()
    {
        // Tenant is resolved by middleware
    }

    protected function getTenant(): User
    {
        $tenant = app()->bound('tenant') ? app('tenant') : null;

        if (!$tenant) {
            abort(404, 'Site not found. Please access via subdomain.');
        }

        return $tenant;
    }

    protected function getTemplateName(): string
    {
        $site = $this->getTenant()->site;
        $template = $site?->template;

        return $template?->slug ?? 'modern';
    }

    protected function view(string $view, array $data = []): View
    {
        $tenant = $this->getTenant();
        $template = $this->getTemplateName();

        // Get published pages for navigation
        $navPages = Page::withoutGlobalScopes()
            ->where('user_id', $tenant->id)
            ->where('is_published', true)
            ->orderBy('sort_order')
            ->get();

        $baseData = [
            'tenant' => $tenant,
            'site' => $tenant->site,
            'template' => $template,
            'navPages' => $navPages,
        ];

        return view("templates.{$template}.{$view}", array_merge($baseData, $data));
    }

    public function home()
    {
        $tenant = $this->getTenant();

        $featuredProperties = Property::withoutGlobalScopes()
            ->with('images')
            ->where('user_id', $tenant->id)
            ->where('status', 'active')
            ->where('is_featured', true)
            ->latest()
            ->take(6)
            ->get();

        $testimonials = Testimonial::withoutGlobalScopes()
            ->where('user_id', $tenant->id)
            ->where('is_published', true)
            ->latest()
            ->take(3)
            ->get();

        $recentPosts = BlogPost::withoutGlobalScopes()
            ->where('user_id', $tenant->id)
            ->where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->latest('published_at')
            ->take(3)
            ->get();

        return $this->view('home', [
            'featuredProperties' => $featuredProperties,
            'testimonials' => $testimonials,
            'recentPosts' => $recentPosts,
        ]);
    }

    public function properties(Request $request)
    {
        $tenant = $this->getTenant();

        $query = Property::withoutGlobalScopes()
            ->where('user_id', $tenant->id)
            ->where('status', 'active');

        // Filters
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->filled('bedrooms')) {
            $query->where('bedrooms', '>=', $request->bedrooms);
        }

        if ($request->filled('bathrooms')) {
            $query->where('bathrooms', '>=', $request->bathrooms);
        }

        if ($request->filled('city')) {
            $query->where('city', 'ilike', '%' . $request->city . '%');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'ilike', "%{$search}%")
                    ->orWhere('address', 'ilike', "%{$search}%")
                    ->orWhere('city', 'ilike', "%{$search}%")
                    ->orWhere('description', 'ilike', "%{$search}%");
            });
        }

        $properties = $query->with('images')->latest()->paginate(12);

        return $this->view('properties', [
            'properties' => $properties,
        ]);
    }

    public function property(string $slug)
    {
        $tenant = $this->getTenant();

        $property = Property::withoutGlobalScopes()
            ->with('images')
            ->where('user_id', $tenant->id)
            ->where('slug', $slug)
            ->where('status', 'active')
            ->firstOrFail();

        // Get related properties by similar price range or location
        $relatedProperties = Property::withoutGlobalScopes()
            ->with('images')
            ->where('user_id', $tenant->id)
            ->where('id', '!=', $property->id)
            ->where('status', 'active')
            ->where(function ($query) use ($property) {
                $query->where('city', $property->city)
                    ->orWhereBetween('price', [
                        $property->price * 0.7,
                        $property->price * 1.3
                    ]);
            })
            ->take(3)
            ->get();

        return $this->view('property', [
            'property' => $property,
            'relatedProperties' => $relatedProperties,
        ]);
    }

    public function about()
    {
        $tenant = $this->getTenant();

        $testimonials = Testimonial::withoutGlobalScopes()
            ->where('user_id', $tenant->id)
            ->where('is_published', true)
            ->latest()
            ->take(6)
            ->get();

        return $this->view('about', [
            'testimonials' => $testimonials,
        ]);
    }

    public function contact()
    {
        return $this->view('contact');
    }

    public function submitContact(Request $request)
    {
        $tenant = $this->getTenant();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'message' => 'required|string|max:5000',
            'property_id' => 'nullable|exists:properties,id',
        ]);

        $submission = ContactSubmission::create([
            'user_id' => $tenant->id,
            'property_id' => $validated['property_id'] ?? null,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'message' => $validated['message'],
        ]);

        // Send email notification
        $notifyEmail = $tenant->site?->email ?? $tenant->email;
        Mail::to($notifyEmail)->queue(new ContactFormSubmitted($submission));

        return back()->with('success', 'Thank you for your message! We will get back to you soon.');
    }

    // Blog methods
    public function blog(Request $request)
    {
        $tenant = $this->getTenant();

        $query = BlogPost::withoutGlobalScopes()
            ->where('user_id', $tenant->id)
            ->where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'ilike', "%{$search}%")
                    ->orWhere('excerpt', 'ilike', "%{$search}%")
                    ->orWhere('content', 'ilike', "%{$search}%");
            });
        }

        $posts = $query->latest('published_at')->paginate(9);

        return $this->view('blog', [
            'posts' => $posts,
        ]);
    }

    public function blogPost(string $slug)
    {
        $tenant = $this->getTenant();

        $post = BlogPost::withoutGlobalScopes()
            ->where('user_id', $tenant->id)
            ->where('slug', $slug)
            ->where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->firstOrFail();

        $recentPosts = BlogPost::withoutGlobalScopes()
            ->where('user_id', $tenant->id)
            ->where('id', '!=', $post->id)
            ->where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->latest('published_at')
            ->take(3)
            ->get();

        return $this->view('blog-post', [
            'post' => $post,
            'recentPosts' => $recentPosts,
        ]);
    }

    // Custom pages
    public function page(string $slug)
    {
        $tenant = $this->getTenant();

        $page = Page::withoutGlobalScopes()
            ->where('user_id', $tenant->id)
            ->where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        return $this->view('page', [
            'page' => $page,
        ]);
    }
}
