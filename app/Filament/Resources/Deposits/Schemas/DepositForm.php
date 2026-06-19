<?php

namespace App\Filament\Resources\Deposits\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use App\Models\User;

class DepositForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->options(User::where('role', 'nasabah')->pluck('name', 'id'))
                    ->required()
                    ->label('Nasabah'),
                TextInput::make('total_price')
                    ->numeric()
                    ->disabled()
                    ->label('Total Uang (Rp)'),
                TextInput::make('weight_total')
                    ->numeric()
                    ->disabled()
                    ->label('Total Berat (kg/L)'),
                Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->required()
                    ->label('Status'),
                Select::make('donation_category')
                    ->options([
                        'umum' => 'Umum',
                        'donasi' => 'Donasi',
                    ])
                    ->required()
                    ->default('umum')
                    ->label('Kategori Donasi')
                    ->helperText('"Umum" setor sampah biasa (saldo masuk rekening). "Donasi" hasil setor disumbangkan.'),
                Textarea::make('notes')
                    ->maxLength(65535)
                    ->label('Catatan'),
                Select::make('validated_by')
                    ->options(User::whereIn('role', ['admin', 'petugas'])->pluck('name', 'id'))
                    ->disabled()
                    ->label('Diverifikasi Oleh'),
            ]);
    }
}
