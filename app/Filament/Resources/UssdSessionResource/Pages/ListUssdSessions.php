<?php

namespace App\Filament\Resources\UssdSessionResource\Pages;

use App\Filament\Resources\UssdSessionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUssdSessions extends ListRecords
{
    protected static string $resource = UssdSessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
