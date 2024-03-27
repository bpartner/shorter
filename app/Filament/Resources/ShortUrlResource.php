<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShortUrlResource\Pages;
use App\Filament\Resources\ShortUrlResource\RelationManagers;
use App\Models\ShortUrl;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ShortUrlResource extends Resource
{
    protected static ?string $model = ShortUrl::class;
    protected static ?string $label = 'URL';
    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $navigationIcon = 'heroicon-o-link';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                                          ->columnSpanFull(),
                Forms\Components\TextInput::make('destination_url')
                                          ->activeUrl()
                                          ->label('URL'),
                Forms\Components\TextInput::make('url_key')
                                          ->label('Key')
                                          ->disabledOn('edit')
                                          ->unique(ignorable: fn($record) => $record)
                                          ->placeholder('Leave blank for auto generate'),
                Forms\Components\Section::make('Basic options')
                                        ->collapsed()
                                        ->persistCollapsed()
                                        ->columns(3)
                                        ->schema([
                                            Forms\Components\Checkbox::make('single_use')
                                                                     ->label('Use only once')
                                                                     ->default(false),
                                            Forms\Components\Checkbox::make('forward_query_params')
                                                                     ->label('Forward query params')
                                                                     ->default(true),
                                            Forms\Components\Checkbox::make('track_visits')
                                                                     ->label('Track visitors')
                                                                     ->default(true)
                                                                     ->reactive(),
                                            Forms\Components\TextInput::make('redirect_status_code')
                                                                      ->default(301),
                                            Forms\Components\DateTimePicker::make('activated_at'),
                                            Forms\Components\DateTimePicker::make('deactivated_at'),
                                        ]),
                Forms\Components\Section::make('Visitors options')
                                        ->hidden(fn(Forms\Get $get): bool => !$get('track_visits'))
                                        ->collapsed()
                                        ->persistCollapsed()
                                        ->columns(3)
                                        ->schema([
                                            Forms\Components\Checkbox::make('track_ip_address')
                                                                     ->default(true),
                                            Forms\Components\Checkbox::make('track_operating_system')
                                                                     ->default(true),
                                            Forms\Components\Checkbox::make('track_operating_system_version')
                                                                     ->default(true),
                                            Forms\Components\Checkbox::make('track_browser')
                                                                     ->default(true),
                                            Forms\Components\Checkbox::make('track_browser_version')
                                                                     ->default(true),
                                            Forms\Components\Checkbox::make('track_referer_url')
                                                                     ->default(true),
                                            Forms\Components\Checkbox::make('track_device_type')
                                                                     ->default(true),
                                        ]),


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                                         ->label('Title'),
                TextColumn::make('short_url')
                          ->state(
                              static function (Model $record): string {
                                  /**@var ShortUrl $record */
                                  return 'https://' . config('app.base_domain') . "/$record->url_key";
                              }
                          )
                          ->tooltip('Click to copy')
                          ->copyable()
                          ->copyMessage('Address was copy')
                          ->copyMessageDuration(1500)
                          ->label('Short URL'),
                TextColumn::make('activated_at'),
                TextColumn::make('deactivated_at'),
                Tables\Columns\TextColumn::make('visits')
                                         ->state(static function (Model $record): string {
                                             /**@var ShortUrl $record */
                                             return $record->visits()
                                                           ->count() ?? 0;
                                         })
                                         ->label('Visits'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
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
            RelationManagers\VisitsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListShortUrls::route('/'),
            'create' => Pages\CreateShortUrl::route('/create'),
            'edit'   => Pages\EditShortUrl::route('/{record}/edit'),
            'view'   => Pages\ViewShortUrl::route('/{record}'),
        ];
    }
}
