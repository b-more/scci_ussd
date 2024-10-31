<?php

namespace App\Filament\Resources\VoucherValidationResource\Pages;

use App\Filament\Resources\VoucherValidationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVoucherValidation extends EditRecord
{
    protected static string $resource = VoucherValidationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
