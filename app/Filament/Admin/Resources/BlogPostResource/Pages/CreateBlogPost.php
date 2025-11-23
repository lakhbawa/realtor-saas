<?php

namespace App\Filament\Admin\Resources\BlogPostResource\Pages;

use App\Filament\Admin\Resources\BlogPostResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBlogPost extends CreateRecord
{
    protected static string $resource = BlogPostResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Auto-set user_id for tenant users
        if (auth()->user()?->isTenant()) {
            $data['user_id'] = auth()->id();
        }

        return $data;
    }
}
