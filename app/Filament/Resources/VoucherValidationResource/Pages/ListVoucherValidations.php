<?php

namespace App\Filament\Resources\VoucherValidationResource\Pages;

use App\Filament\Resources\VoucherValidationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVoucherValidations extends ListRecords
{
    protected static string $resource = VoucherValidationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
