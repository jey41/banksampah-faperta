<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Select::make('role')
                    ->options([
                        'admin' => 'Admin',
                        'petugas' => 'Petugas',
                        'nasabah' => 'Nasabah',
                    ])
                    ->required(),
                Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'verified' => 'Verified',
                        'rejected' => 'Rejected',
                    ])
                    ->required(),
                TextInput::make('phone')
                    ->tel()
                    ->maxLength(255),
                Textarea::make('address')
                    ->maxLength(65535),
                TextInput::make('saldo')
                    ->numeric()
                    ->default(0)
                    ->disabled(),
                TextInput::make('account_no')
                    ->disabled()
                    ->placeholder('Otomatis saat verifikasi'),
            ]);
    }
}
