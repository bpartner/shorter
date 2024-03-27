<?php

namespace App\Filament\Resources\ShortUrlResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VisitsRelationManager extends RelationManager
{
    protected static string $relationship = 'visits';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('visitor')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('visitor')
            ->columns([
                Tables\Columns\TextColumn::make('ip_address'),
                Tables\Columns\TextColumn::make('operating_system')->sortable(),
                Tables\Columns\TextColumn::make('operating_system_version'),
                Tables\Columns\TextColumn::make('browser')->sortable(),
                Tables\Columns\TextColumn::make('browser_version'),
                Tables\Columns\TextColumn::make('device_type')->sortable(),
                Tables\Columns\TextColumn::make('visited_at')->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                //Tables\Actions\EditAction::make(),
                //Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
