<?php

namespace App\Filament\Resources\TrashPrices\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;

class TrashPriceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label('Nama Sampah'),
                Select::make('category')
                    ->options([
                        'plastik' => 'Plastik',
                        'kertas' => 'Kertas',
                        'logam' => 'Logam',
                        'kaca' => 'Kaca',
                        'minyak_jelantah' => 'Minyak Jelantah',
                        'lainnya' => 'Lainnya',
                    ])
                    ->required()
                    ->label('Kategori'),
                TextInput::make('price_buy')
                    ->numeric()
                    ->required()
                    ->label('Harga Beli (Nasabah)'),
                TextInput::make('price_sell')
                    ->numeric()
                    ->required()
                    ->label('Harga Jual (Pabrik)'),
                TextInput::make('unit')
                    ->default('kg')
                    ->required()
                    ->label('Satuan'),
                TextInput::make('carbon_factor')
                    ->numeric()
                    ->step('0.01')
                    ->default(0.00)
                    ->label('Faktor Karbon (kg CO2)'),
            ]);
    }
}
