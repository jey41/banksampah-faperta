<?php

namespace App\Filament\Resources\Withdrawals\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use App\Models\User;

class WithdrawalForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->options(User::where('role', 'nasabah')->pluck('name', 'id'))
                    ->required()
                    ->label('Nasabah'),
                TextInput::make('amount')
                    ->numeric()
                    ->required()
                    ->label('Jumlah Penarikan (Rp)'),
                TextInput::make('bank_name')
                    ->required()
                    ->label('Bank / E-Wallet'),
                TextInput::make('account_number')
                    ->required()
                    ->label('No. Rekening / No. HP'),
                TextInput::make('account_name')
                    ->required()
                    ->label('Nama Penerima'),
                Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->required()
                    ->label('Status'),
                Textarea::make('notes')
                    ->maxLength(65535)
                    ->label('Catatan / Keterangan'),
                Select::make('validated_by')
                    ->options(User::whereIn('role', ['admin', 'petugas'])->pluck('name', 'id'))
                    ->disabled()
                    ->label('Diverifikasi Oleh'),
            ]);
    }
}
