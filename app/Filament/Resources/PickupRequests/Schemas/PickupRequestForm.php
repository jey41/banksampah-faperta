<?php

namespace App\Filament\Resources\PickupRequests\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use App\Models\User;

class PickupRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->options(User::where('role', 'nasabah')->pluck('name', 'id'))
                    ->disabled()
                    ->label('Nasabah'),
                TextInput::make('pickup_address')
                    ->disabled()
                    ->label('Alamat Penjemputan'),
                TextInput::make('pickup_phone')
                    ->disabled()
                    ->label('Nomor Telepon'),
                TextInput::make('estimated_distance')
                    ->disabled()
                    ->suffix(' km')
                    ->label('Estimasi Jarak'),
                TextInput::make('latitude')
                    ->disabled()
                    ->label('Latitude'),
                TextInput::make('longitude')
                    ->disabled()
                    ->label('Longitude'),
                DatePicker::make('pickup_date')
                    ->disabled()
                    ->label('Tanggal Penjemputan'),
                TextInput::make('pickup_time')
                    ->disabled()
                    ->label('Jam Penjemputan'),
                Select::make('status')
                    ->options([
                        'pending' => 'Menunggu',
                        'assigned' => 'Ditugaskan',
                        'completed' => 'Selesai',
                        'cancelled' => 'Dibatalkan',
                    ])
                    ->required()
                    ->label('Status'),
                Select::make('assigned_to')
                    ->options(User::whereIn('role', ['admin', 'petugas'])->pluck('name', 'id'))
                    ->label('Petugas Ditugaskan')
                    ->searchable()
                    ->helperText('Pilih petugas yang akan melakukan penjemputan.'),
                Textarea::make('notes')
                    ->disabled()
                    ->label('Catatan dari Nasabah'),
            ]);
    }
}
