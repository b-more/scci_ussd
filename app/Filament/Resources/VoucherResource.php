<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VoucherResource\Pages;
use App\Models\Voucher;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;

class VoucherResource extends Resource
{
    protected static ?string $model = Voucher::class;
    protected static ?string $navigationIcon = 'heroicon-o-ticket';
    protected static ?string $navigationGroup = 'Seed Management';
    protected static ?int $navigationSort = 1;

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Basic Information')
                    ->schema([
                        Forms\Components\TextInput::make('voucher_number')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->placeholder('e.g., SCCI-2024-001'),
                        Forms\Components\TextInput::make('batch_number')
                            ->required()
                            ->placeholder('e.g., BTH-2024-001'),
                        Forms\Components\Select::make('seed_type')
                            ->required()
                            ->options([
                                'Maize' => 'Maize',
                                'Soybean' => 'Soybean',
                                'Groundnuts' => 'Groundnuts',
                                'Wheat' => 'Wheat',
                                'Rice' => 'Rice',
                                'Sunflower' => 'Sunflower',
                                'Beans' => 'Beans',
                                'Sorghum' => 'Sorghum',
                                'Cotton' => 'Cotton',
                            ]),
                        Forms\Components\TextInput::make('seed_variety')
                            ->required()
                            ->placeholder('e.g., SC-707'),
                        Forms\Components\Select::make('seed_class')
                            ->options([
                                'Certified' => 'Certified',
                                'Basic' => 'Basic',
                                'Pre-Basic' => 'Pre-Basic',
                                'Breeder' => 'Breeder',
                            ]),
                        Forms\Components\TextInput::make('quantity_kg')
                            ->numeric()
                            ->label('Quantity (KG)'),
                    ])->columns(2),

                Forms\Components\Section::make('Company Information')
                    ->schema([
                        Forms\Components\Select::make('seed_company_name')
                            ->required()
                            ->options([
                                'Seed Co Zambia' => 'Seed Co Zambia',
                                'Zamseed' => 'Zamseed',
                                'MRI Seed' => 'MRI Seed',
                                'Klein Karoo' => 'Klein Karoo',
                                'Afriseeds' => 'Afriseeds',
                                'Pioneer Seeds' => 'Pioneer Seeds',
                                'Pannar Seed' => 'Pannar Seed',
                            ]),
                        Forms\Components\TextInput::make('seed_company_license')
                            ->placeholder('License number'),
                    ])->columns(2),

                Forms\Components\Section::make('Testing Information')
                    ->schema([
                        Forms\Components\DatePicker::make('production_date'),
                        Forms\Components\DatePicker::make('testing_date'),
                        Forms\Components\DatePicker::make('packaging_date'),
                        Forms\Components\TextInput::make('laboratory_test_number'),
                        Forms\Components\TextInput::make('germination_rate')
                            ->suffix('%'),
                        Forms\Components\TextInput::make('purity_rate')
                            ->suffix('%'),
                        Forms\Components\TextInput::make('moisture_content')
                            ->suffix('%'),
                    ])->columns(2),

                Forms\Components\Section::make('Validity Period')
                    ->schema([
                        Forms\Components\DatePicker::make('valid_from')
                            ->required(),
                        Forms\Components\DatePicker::make('valid_until')
                            ->required(),
                        Forms\Components\Toggle::make('is_active')
                            ->default(true),
                    ])->columns(3),

                Forms\Components\Section::make('Location Information')
                    ->schema([
                        Forms\Components\Select::make('region')
                            ->options([
                                'Lusaka' => 'Lusaka',
                                'Copperbelt' => 'Copperbelt',
                                'Central' => 'Central',
                                'Eastern' => 'Eastern',
                                'Southern' => 'Southern',
                                'Northern' => 'Northern',
                                'Luapula' => 'Luapula',
                                'North-Western' => 'North-Western',
                                'Western' => 'Western',
                                'Muchinga' => 'Muchinga',
                            ]),
                        Forms\Components\TextInput::make('district'),
                        Forms\Components\TextInput::make('distribution_point'),
                    ])->columns(3),

                Forms\Components\Section::make('Administrative Details')
                    ->schema([
                        Forms\Components\TextInput::make('created_by'),
                        Forms\Components\TextInput::make('approved_by'),
                        Forms\Components\Textarea::make('comments')
                            ->rows(3),
                    ])->columns(2),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('voucher_number')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('seed_company_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('seed_type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('seed_variety')
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'active',
                        'warning' => 'used',
                        'danger' => 'expired',
                        'secondary' => 'suspended',
                    ]),
                Tables\Columns\IconColumn::make('is_used')
                    ->boolean(),
                Tables\Columns\TextColumn::make('used_by_phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('valid_until')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'used' => 'Used',
                        'expired' => 'Expired',
                        'suspended' => 'Suspended',
                    ]),
                SelectFilter::make('seed_company_name')
                    ->label('Company')
                    ->options([
                        'Seed Co Zambia' => 'Seed Co Zambia',
                        'Zamseed' => 'Zamseed',
                        'MRI Seed' => 'MRI Seed',
                        'Klein Karoo' => 'Klein Karoo',
                        'Afriseeds' => 'Afriseeds',
                        'Pioneer Seeds' => 'Pioneer Seeds',
                        'Pannar Seed' => 'Pannar Seed',
                    ]),
                Filter::make('valid_until')
                    ->form([
                        Forms\Components\DatePicker::make('valid_from'),
                        Forms\Components\DatePicker::make('valid_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['valid_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('valid_until', '>=', $date),
                            )
                            ->when(
                                $data['valid_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('valid_until', '<=', $date),
                            );
                    })
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVouchers::route('/'),
            'create' => Pages\CreateVoucher::route('/create'),
            'edit' => Pages\EditVoucher::route('/{record}/edit'),
        ];
    }
}