<?php

namespace App\Filament\Admin\Resources\PageResource\Pages;

use App\Filament\Admin\Resources\PageResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePage extends CreateRecord
{
    protected static string $resource = PageResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Auto-set user_id for tenant users
        if (auth()->user()?->isTenant()) {
            $data['user_id'] = auth()->id();
        }

        return $data;
    }
}
