<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UssdSessionResource\Pages;
use App\Models\UssdSession;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Indicator;
use Illuminate\Database\Eloquent\Builder;

class UssdSessionResource extends Resource
{
    protected static ?string $model = UssdSession::class;
    protected static ?string $navigationIcon = 'heroicon-o-phone';
    protected static ?string $navigationGroup = 'Monitoring';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('session_id')
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone_number')
                    ->maxLength(255),
                Forms\Components\TextInput::make('case_no')
                    ->maxLength(255),
                Forms\Components\TextInput::make('step_no')
                    ->maxLength(255),
                Forms\Components\Textarea::make('input_message')
                    ->maxLength(255),
                Forms\Components\Textarea::make('response_message')
                    ->maxLength(255),
                Forms\Components\Select::make('status')
                    ->options([
                        'completed' => 'Completed',
                        'incomplete' => 'Incomplete',
                        'failed' => 'Failed',
                    ]),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('session_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('case_no'),
                Tables\Columns\TextColumn::make('step_no'),
                Tables\Columns\TextColumn::make('input_message')
                    ->wrap(),
                Tables\Columns\TextColumn::make('response_message')
                    ->wrap(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'completed',
                        'warning' => 'incomplete',
                        'danger' => 'failed',
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'completed' => 'Completed',
                        'incomplete' => 'Incomplete',
                        'failed' => 'Failed',
                    ]),
                Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('From'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
 
                        if ($data['created_from'] ?? null) {
                            $indicators[] = Indicator::make('Created from ' . carbon($data['created_from'])->toFormattedDateString())
                                ->removeField('created_from');
                        }
 
                        if ($data['created_until'] ?? null) {
                            $indicators[] = Indicator::make('Created until ' . carbon($data['created_until'])->toFormattedDateString())
                                ->removeField('created_until');
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
            'index' => Pages\ListUssdSessions::route('/'),
        ];
    }
}