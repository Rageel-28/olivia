<?php

namespace App\Filament\Developer\Resources\Aplikasis\Pages;

use App\Filament\Developer\Resources\Aplikasis\AplikasiResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAplikasi extends EditRecord
{
    protected static string $resource = AplikasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
