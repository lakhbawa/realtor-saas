<?php

namespace App\Filament\Tenant\Resources\ContactSubmissionResource\Pages;

use App\Filament\Tenant\Resources\ContactSubmissionResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewContactSubmission extends ViewRecord
{
    protected static string $resource = ContactSubmissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('markRead')
                ->icon('heroicon-o-check')
                ->action(fn () => $this->record->markAsRead())
                ->hidden(fn () => $this->record->is_read),
            Actions\Action::make('reply')
                ->icon('heroicon-o-envelope')
                ->url(fn (): string => 'mailto:' . $this->record->email)
                ->openUrlInNewTab(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Auto-mark as read when viewing
        if (!$this->record->is_read) {
            $this->record->markAsRead();
        }

        return $data;
    }
}
