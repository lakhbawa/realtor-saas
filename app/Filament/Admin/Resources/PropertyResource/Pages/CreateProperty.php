<?php

namespace App\Filament\Admin\Resources\PropertyResource\Pages;

use App\Filament\Admin\Resources\PropertyResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProperty extends CreateRecord
{
    protected static string $resource = PropertyResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Auto-set user_id for tenant users
        if (auth()->user()?->isTenant()) {
            $data['user_id'] = auth()->id();
        }

        return $data;
    }
}
