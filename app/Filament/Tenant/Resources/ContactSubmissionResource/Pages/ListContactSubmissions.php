<?php

namespace App\Filament\Tenant\Resources\ContactSubmissionResource\Pages;

use App\Filament\Tenant\Resources\ContactSubmissionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListContactSubmissions extends ListRecords
{
    protected static string $resource = ContactSubmissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No create action - submissions come from public form
        ];
    }
}
