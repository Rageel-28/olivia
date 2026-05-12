<?php

namespace App\Filament\Developer\Resources\Aplikasis\Schemas;

use Filament\Schemas\Schema;

class AplikasiForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Components\Hidden::make('developer_id')
                    ->default(fn () => auth()->id()),
                \Filament\Components\TextInput::make('nama')
                    ->required()
                    ->maxLength(255),
                \Filament\Components\TextInput::make('package_name')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->placeholder('com.example.app'),
            ]);
    }
}
