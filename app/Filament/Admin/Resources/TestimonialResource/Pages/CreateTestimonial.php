<?php

namespace App\Filament\Admin\Resources\TestimonialResource\Pages;

use App\Filament\Admin\Resources\TestimonialResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTestimonial extends CreateRecord
{
    protected static string $resource = TestimonialResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Auto-set user_id for tenant users
        if (auth()->user()?->isTenant()) {
            $data['user_id'] = auth()->id();
        }

        return $data;
    }
}
