<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Site;
use App\Models\Template;
use App\Models\Property;
use App\Models\Testimonial;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        $admin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'subdomain' => 'admin-user',
                'password' => Hash::make('password'),
                'is_admin' => true,
                'subscription_status' => 'active',
            ]
        );

        // Create demo tenant user
        $tenant = User::updateOrCreate(
            ['email' => 'demo@example.com'],
            [
                'name' => 'John Smith',
                'subdomain' => 'johnsmith',
                'password' => Hash::make('password'),
                'is_admin' => false,
                'subscription_status' => 'active',
            ]
        );

        // Get a template
        $template = Template::where('slug', 'modern')->first();

        // Create site for demo tenant
        $site = Site::updateOrCreate(
            ['user_id' => $tenant->id],
            [
                'template_id' => $template?->id,
                'site_name' => 'John Smith Realty',
                'tagline' => 'Your Dream Home Awaits',
                'email' => 'john@johnsmithrealty.com',
                'phone' => '(555) 123-4567',
                'address' => '123 Main Street, Suite 100',
                'city' => 'Beverly Hills',
                'state' => 'CA',
                'zip' => '90210',
                'bio' => 'With over 15 years of experience in the real estate industry, I specialize in helping families find their perfect homes in the Los Angeles area. My commitment to personalized service and deep knowledge of the local market ensures that every client receives the attention they deserve.',
                'primary_color' => '#4F46E5',
                'facebook' => 'https://facebook.com/johnsmithrealty',
                'instagram' => 'https://instagram.com/johnsmithrealty',
                'linkedin' => 'https://linkedin.com/in/johnsmithrealty',
                'meta_title' => 'John Smith Realty - Beverly Hills Real Estate Agent',
                'meta_description' => 'Find your dream home in Beverly Hills with John Smith Realty. Over 15 years of experience helping families find their perfect homes.',
                'is_published' => true,
            ]
        );

        // Create demo properties
        $properties = [
            [
                'title' => 'Modern Beverly Hills Estate',
                'slug' => 'modern-beverly-hills-estate',
                'description' => "This stunning contemporary estate offers the ultimate in luxury living. Featuring 6 bedrooms and 8 bathrooms across 12,000 square feet of meticulously designed living space.\n\nThe open-concept main level includes a gourmet kitchen with top-of-the-line appliances, a formal dining room, and a spacious living area that opens to the infinity pool.",
                'price' => 8500000,
                'bedrooms' => 6,
                'bathrooms' => 8,
                'square_feet' => 12000,
                'address' => '456 Sunset Blvd',
                'city' => 'Beverly Hills',
                'state' => 'CA',
                'zip' => '90210',
                'status' => 'active',
                'is_featured' => true,
            ],
            [
                'title' => 'Charming Craftsman Bungalow',
                'slug' => 'charming-craftsman-bungalow',
                'description' => "A beautifully restored 1920s Craftsman bungalow in the heart of Pasadena. This 3-bedroom gem features original hardwood floors, built-in cabinetry, and a stunning wood-burning fireplace.\n\nThe updated kitchen maintains its period charm while offering modern conveniences.",
                'price' => 1250000,
                'bedrooms' => 3,
                'bathrooms' => 2,
                'square_feet' => 1800,
                'address' => '789 Oak Street',
                'city' => 'Pasadena',
                'state' => 'CA',
                'zip' => '91101',
                'status' => 'active',
                'is_featured' => true,
            ],
            [
                'title' => 'Downtown Luxury Penthouse',
                'slug' => 'downtown-luxury-penthouse',
                'description' => "Experience elevated living in this spectacular penthouse in the heart of downtown Los Angeles. Floor-to-ceiling windows showcase breathtaking city views from every room.\n\nThis 3,500 sq ft residence features 3 bedrooms, 3.5 bathrooms, a private rooftop terrace, and premium finishes throughout.",
                'price' => 3200000,
                'bedrooms' => 3,
                'bathrooms' => 3.5,
                'square_feet' => 3500,
                'address' => '100 Grand Avenue, PH1',
                'city' => 'Los Angeles',
                'state' => 'CA',
                'zip' => '90012',
                'status' => 'active',
                'is_featured' => true,
            ],
            [
                'title' => 'Santa Monica Beach Rental',
                'slug' => 'santa-monica-beach-rental',
                'description' => "Steps from the sand! This bright and airy 2-bedroom apartment offers the quintessential beach lifestyle. Wake up to ocean views and fall asleep to the sound of waves.\n\nFeatures include an updated kitchen, in-unit laundry, and a private balcony perfect for morning coffee.",
                'price' => 4500,
                'bedrooms' => 2,
                'bathrooms' => 2,
                'square_feet' => 1100,
                'address' => '200 Ocean Avenue, Unit 305',
                'city' => 'Santa Monica',
                'state' => 'CA',
                'zip' => '90401',
                'status' => 'active',
                'is_featured' => false,
            ],
        ];

        foreach ($properties as $propertyData) {
            Property::updateOrCreate(
                ['user_id' => $tenant->id, 'slug' => $propertyData['slug']],
                array_merge($propertyData, ['user_id' => $tenant->id])
            );
        }

        // Create demo testimonials
        $testimonials = [
            [
                'client_name' => 'Sarah & Michael Johnson',
                'content' => 'John made the home buying process incredibly smooth. His knowledge of the Beverly Hills market is unmatched, and he found us our dream home in just two weeks!',
                'rating' => 5,
                'is_published' => true,
            ],
            [
                'client_name' => 'David Chen',
                'content' => 'As a first-time home buyer, I was nervous about the whole process. John guided me every step of the way and was always available to answer my questions. Highly recommend!',
                'rating' => 5,
                'is_published' => true,
            ],
            [
                'client_name' => 'Jennifer Martinez',
                'content' => 'John helped us sell our home in record time and above asking price. His marketing strategy and negotiation skills are top-notch. We couldn\'t be happier with the results.',
                'rating' => 5,
                'is_published' => true,
            ],
        ];

        foreach ($testimonials as $testimonialData) {
            Testimonial::updateOrCreate(
                ['user_id' => $tenant->id, 'client_name' => $testimonialData['client_name']],
                array_merge($testimonialData, ['user_id' => $tenant->id])
            );
        }
    }
}
