<?php

namespace App\Filament\Resources\UssdSessionResource\Pages;

use App\Filament\Resources\UssdSessionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUssdSession extends EditRecord
{
    protected static string $resource = UssdSessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
