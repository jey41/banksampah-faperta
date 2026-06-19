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
                Select::make('withdrawal_method')
                    ->options([
                        'tunai' => 'Tunai (Ambil di Lokasi)',
                        'transfer_bank' => 'Transfer Bank / E-Wallet',
                    ])
                    ->required()
                    ->default('tunai')
                    ->label('Metode Penarikan')
                    ->live(),
                TextInput::make('amount')
                    ->numeric()
                    ->required()
                    ->label('Jumlah Penarikan (Rp)'),
                TextInput::make('admin_fee')
                    ->numeric()
                    ->disabled()
                    ->default(0)
                    ->label('Biaya Admin (Rp)')
                    ->helperText('Otomatis Rp2.500 untuk bank non-BTN'),
                TextInput::make('bank_name')
                    ->required()
                    ->label('Bank / E-Wallet'),
                Select::make('bank_type')
                    ->options([
                        'btn' => 'BTN',
                        'bca' => 'BCA',
                        'mandiri' => 'Mandiri',
                        'bni' => 'BNI',
                        'bri' => 'BRI',
                        'bpr' => 'BPR',
                        'lainnya' => 'Lainnya',
                    ])
                    ->label('Jenis Bank')
                    ->helperText('BTN = bebas biaya admin'),
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
