<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactSubmissionRequest;
use App\Mail\ContactFormSubmitted;
use App\Models\BlogPost;
use App\Models\ContactSubmission;
use App\Models\Page;
use App\Models\Property;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PublicSiteController extends TenantController
{
    public function home()
    {
        return $this->view('home', [
            'featuredProperties' => $this->featuredProperties(),
            'testimonials' => $this->topTestimonials(3),
            'recentPosts' => $this->recentBlogPosts(3),
        ]);
    }

    public function properties(Request $request)
    {
        $properties = Property::withoutGlobalScopes()
            ->with('images')
            ->where('tenant_id', $this->tenant()->id)
            ->where('status', 'active')
            ->when($request->filled('min_price'), fn($q) => $q->where('price', '>=', $request->min_price))
            ->when($request->filled('max_price'), fn($q) => $q->where('price', '<=', $request->max_price))
            ->when($request->filled('bedrooms'), fn($q) => $q->where('bedrooms', '>=', $request->bedrooms))
            ->when($request->filled('bathrooms'), fn($q) => $q->where('bathrooms', '>=', $request->bathrooms))
            ->when($request->filled('city'), fn($q) => $q->where('city', 'ilike', '%'.$request->city.'%'))
            ->when($request->filled('search'), fn($q) => $this->applySearch($q, $request->search))
            ->latest()
            ->paginate(12);

        return $this->view('properties', compact('properties'));
    }

    public function property(string $slug)
    {
        $property = $this->findProperty($slug);

        return $this->view('property', [
            'property' => $property,
            'relatedProperties' => $this->relatedProperties($property),
        ]);
    }

    public function about()
    {
        return $this->view('about', [
            'testimonials' => $this->topTestimonials(6),
        ]);
    }

    public function testimonials()
    {
        $query = $this->testimonialsQuery();

        return $this->view('testimonials', [
            'featuredTestimonials' => (clone $query)->where('is_featured', true)->get(),
            'testimonials' => (clone $query)->where('is_featured', false)->paginate(12),
            'totalTestimonials' => (clone $query)->count(),
            'averageRating' => round((clone $query)->avg('rating'), 1),
        ]);
    }

    public function contact()
    {
        return $this->view('contact');
    }

    public function submitContact(ContactSubmissionRequest $request)
    {
        $submission = ContactSubmission::create(array_merge(
            $request->validated(),
            ['tenant_id' => $this->tenant()->id]
        ));

        Mail::to($this->site()->email ?? config('mail.from.address'))
            ->queue(new ContactFormSubmitted($submission));

        return back()->with('success', 'Thank you for your message! We will get back to you soon.');
    }

    public function blog(Request $request)
    {
        $posts = $this->publishedBlogPostsQuery()
            ->when($request->filled('search'), fn($q) => $this->applyBlogSearch($q, $request->search))
            ->latest('published_at')
            ->paginate(9);

        return $this->view('blog', compact('posts'));
    }

    public function blogPost(string $slug)
    {
        $post = $this->publishedBlogPostsQuery()
            ->where('slug', $slug)
            ->firstOrFail();

        return $this->view('blog-post', [
            'post' => $post,
            'recentPosts' => $this->recentBlogPosts(3, $post->id),
        ]);
    }

    public function page(string $slug)
    {
        $page = Page::withoutGlobalScopes()
            ->where('tenant_id', $this->tenant()->id)
            ->where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        return $this->view('page', compact('page'));
    }

    protected function view(string $view, array $data = [])
    {
        return parent::view($view, array_merge($data, [
            'navPages' => $this->navigationPages(),
        ]));
    }

    private function featuredProperties()
    {
        return Property::withoutGlobalScopes()
            ->with('images')
            ->where('tenant_id', $this->tenant()->id)
            ->where('status', 'active')
            ->where('is_featured', true)
            ->latest()
            ->limit(6)
            ->get();
    }

    private function findProperty(string $slug)
    {
        return Property::withoutGlobalScopes()
            ->with('images')
            ->where('tenant_id', $this->tenant()->id)
            ->where('slug', $slug)
            ->where('status', 'active')
            ->firstOrFail();
    }

    private function relatedProperties(Property $property)
    {
        return Property::withoutGlobalScopes()
            ->with('images')
            ->where('tenant_id', $this->tenant()->id)
            ->where('id', '!=', $property->id)
            ->where('status', 'active')
            ->where(fn($q) => $q->where('city', $property->city)
                ->orWhereBetween('price', [$property->price * 0.7, $property->price * 1.3]))
            ->limit(3)
            ->get();
    }

    private function topTestimonials(int $limit)
    {
        return $this->testimonialsQuery()
            ->limit($limit)
            ->get();
    }

    private function testimonialsQuery()
    {
        return Testimonial::withoutGlobalScopes()
            ->with('property')
            ->where('tenant_id', $this->tenant()->id)
            ->where('is_published', true)
            ->ordered();
    }

    private function publishedBlogPostsQuery()
    {
        return BlogPost::withoutGlobalScopes()
            ->where('tenant_id', $this->tenant()->id)
            ->where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    private function recentBlogPosts(int $limit, ?int $excludeId = null)
    {
        return $this->publishedBlogPostsQuery()
            ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
            ->latest('published_at')
            ->limit($limit)
            ->get();
    }

    private function navigationPages()
    {
        return Page::withoutGlobalScopes()
            ->where('tenant_id', $this->tenant()->id)
            ->where('is_published', true)
            ->orderBy('sort_order')
            ->get();
    }

    private function applySearch($query, string $search)
    {
        return $query->where(fn($q) =>
            $q->where('title', 'ilike', "%{$search}%")
                ->orWhere('address', 'ilike', "%{$search}%")
                ->orWhere('city', 'ilike', "%{$search}%")
                ->orWhere('description', 'ilike', "%{$search}%")
        );
    }

    private function applyBlogSearch($query, string $search)
    {
        return $query->where(fn($q) =>
            $q->where('title', 'ilike', "%{$search}%")
                ->orWhere('excerpt', 'ilike', "%{$search}%")
                ->orWhere('content', 'ilike', "%{$search}%")
        );
    }
}
