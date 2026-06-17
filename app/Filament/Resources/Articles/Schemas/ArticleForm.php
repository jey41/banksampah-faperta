<?php

namespace App\Filament\Resources\Articles\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;

class ArticleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->label('Judul Artikel'),
                TextInput::make('slug')
                    ->disabled()
                    ->placeholder('Otomatis dibuat dari judul')
                    ->label('Slug'),
                Textarea::make('content')
                    ->required()
                    ->rows(10)
                    ->label('Konten Artikel'),
                FileUpload::make('image_path')
                    ->image()
                    ->imageEditor()
                    ->disk('public')
                    ->imageResizeMode('cover')
                    ->imageCropAspectRatio('16:9')
                    ->maxSize(5120) // 5MB
                    ->directory('articles')
                    ->visibility('public')
                    ->label('Gambar Banner'),
                Select::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Dipublikasikan',
                    ])
                    ->required()
                    ->label('Status'),
            ]);
    }
}

