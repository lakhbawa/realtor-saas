<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Property;
use App\Models\Testimonial;
use App\Models\ContactSubmission;
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
        return app('tenant');
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

        $baseData = [
            'tenant' => $tenant,
            'site' => $tenant->site,
            'template' => $template,
        ];

        return view("templates.{$template}.{$view}", array_merge($baseData, $data));
    }

    public function home()
    {
        $tenant = $this->getTenant();

        $featuredProperties = Property::withoutGlobalScopes()
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

        return $this->view('home', [
            'featuredProperties' => $featuredProperties,
            'testimonials' => $testimonials,
        ]);
    }

    public function properties(Request $request)
    {
        $tenant = $this->getTenant();

        $query = Property::withoutGlobalScopes()
            ->where('user_id', $tenant->id)
            ->where('status', 'active');

        // Filters
        if ($request->filled('type')) {
            $query->where('property_type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('listing_status', $request->status);
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->filled('bedrooms')) {
            $query->where('bedrooms', '>=', $request->bedrooms);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $properties = $query->latest()->paginate(12);

        return $this->view('properties', [
            'properties' => $properties,
        ]);
    }

    public function property(string $slug)
    {
        $tenant = $this->getTenant();

        $property = Property::withoutGlobalScopes()
            ->where('user_id', $tenant->id)
            ->where('slug', $slug)
            ->where('status', 'active')
            ->firstOrFail();

        $relatedProperties = Property::withoutGlobalScopes()
            ->where('user_id', $tenant->id)
            ->where('id', '!=', $property->id)
            ->where('status', 'active')
            ->where('property_type', $property->property_type)
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
}
