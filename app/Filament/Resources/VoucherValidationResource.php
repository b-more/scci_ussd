<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VoucherValidationResource\Pages;
use App\Models\VoucherValidation;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Indicator;

class VoucherValidationResource extends Resource
{
    protected static ?string $model = VoucherValidation::class;
    protected static ?string $navigationIcon = 'heroicon-o-ticket';
    protected static ?string $navigationGroup = 'Monitoring';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('voucher_number')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone_number')
                    ->tel()
                    ->required(),
                Forms\Components\Select::make('status')
                    ->options([
                        'valid' => 'Valid',
                        'invalid' => 'Invalid',
                        'used' => 'Used',
                        'expired' => 'Expired',
                    ])
                    ->required(),
                Forms\Components\KeyValue::make('scci_response')
                    ->label('SCCI Response Data'),
                Forms\Components\TextInput::make('seed_company'),
                Forms\Components\TextInput::make('seed_type'),
                Forms\Components\TextInput::make('batch_number'),
                Forms\Components\DateTimePicker::make('validation_date')
                    ->required(),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('voucher_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone_number')
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'valid',
                        'danger' => 'invalid',
                        'warning' => 'used',
                        'secondary' => 'expired',
                    ]),
                Tables\Columns\TextColumn::make('seed_company')
                    ->searchable(),
                Tables\Columns\TextColumn::make('validation_date')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'valid' => 'Valid',
                        'invalid' => 'Invalid',
                        'used' => 'Used',
                        'expired' => 'Expired',
                    ]),
                Filter::make('validation_date')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('From Date'),
                        Forms\Components\DatePicker::make('until')
                            ->label('Until Date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('validation_date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('validation_date', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
 
                        if ($data['from'] ?? null) {
                            $indicators[] = Indicator::make('From ' . $data['from'])
                                ->removeField('from');
                        }
 
                        if ($data['until'] ?? null) {
                            $indicators[] = Indicator::make('Until ' . $data['until'])
                                ->removeField('until');
                        }
 
                        return $indicators;
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVoucherValidations::route('/'),
        ];
    }

    // public static function getWidgets(): array
    // {
    //     return [
    //         VoucherValidationResource\Widgets\ValidationStatsOverview::class,
    //     ];
    // }
}