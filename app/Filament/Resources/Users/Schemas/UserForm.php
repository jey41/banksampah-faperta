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
                TextInput::make('password')
                    ->password()
                    ->revealable()
                    ->dehydrated(fn (?string $state) => filled($state))
                    ->required(fn (string $operation): bool => $operation === 'create')
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
                TextInput::make('umur')
                    ->numeric()
                    ->label('Umur'),
                Select::make('gender')
                    ->options([
                        'L' => 'Laki-laki',
                        'P' => 'Perempuan',
                    ])
                    ->label('Jenis Kelamin'),
                Select::make('status_pekerjaan')
                    ->options([
                        'bekerja' => 'Bekerja',
                        'tidak_bekerja' => 'Tidak Bekerja',
                        'pelajar' => 'Pelajar',
                        'mahasiswa' => 'Mahasiswa',
                        'pensiun' => 'Pensiun',
                        'lainnya' => 'Lainnya',
                    ])
                    ->label('Status Pekerjaan'),
                TextInput::make('universitas')
                    ->maxLength(255)
                    ->label('Universitas/Instansi'),
                TextInput::make('fakultas')
                    ->maxLength(255)
                    ->label('Fakultas/Jurusan'),
                Select::make('pendidikan_terakhir')
                    ->options([
                        'sd' => 'SD',
                        'smp' => 'SMP',
                        'sma' => 'SMA/SMK',
                        's1' => 'S1',
                        's2' => 'S2',
                        's3' => 'S3',
                    ])
                    ->label('Pendidikan Terakhir'),
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
