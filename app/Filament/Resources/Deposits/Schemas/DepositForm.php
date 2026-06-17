<?php

namespace App\Filament\Resources\Deposits\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use App\Models\User;

use Filament\Forms\Components\Toggle;

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
                Toggle::make('is_donation')
                    ->label('Sedekah/Donasi Sampah')
                    ->helperText('Jika aktif, saldo hasil setoran tidak akan masuk ke rekening nasabah.')
                    ->default(false),
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
