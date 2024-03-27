<?php

namespace App\Filament\Resources\ShortUrlResource\Pages;

use App\Filament\Resources\ShortUrlResource;
use AshAllenDesign\ShortURL\Classes\KeyGenerator;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditShortUrl extends EditRecord
{
    protected static string $resource = ShortUrlResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
       if (!$data['url_key']) {
           $data['url_key'] = (new KeyGenerator())->generateRandom();
       }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->previousUrl;
    }
}
