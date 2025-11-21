<?php

namespace Database\Seeders;

use App\Models\Template;
use Illuminate\Database\Seeder;

class TemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'name' => 'Modern',
                'slug' => 'modern',
                'description' => 'A clean, contemporary design with bold typography and a focus on property imagery. Perfect for agents targeting first-time buyers and young professionals.',
                'preview_image' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Professional',
                'slug' => 'professional',
                'description' => 'A classic, elegant design with serif fonts and a navy blue color scheme. Ideal for established agents and teams with a traditional brand.',
                'preview_image' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Luxury',
                'slug' => 'luxury',
                'description' => 'An upscale, sophisticated design featuring refined typography and a dark color palette with gold accents. Best suited for luxury property specialists.',
                'preview_image' => null,
                'is_active' => true,
            ],
        ];

        foreach ($templates as $template) {
            Template::updateOrCreate(
                ['slug' => $template['slug']],
                $template
            );
        }
    }
}
