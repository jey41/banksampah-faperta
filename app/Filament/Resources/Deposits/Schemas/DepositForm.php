<?php

namespace App\Filament\Resources\Deposits\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use App\Models\User;
use App\Models\TrashPrice;

class DepositForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->options(User::where('role', 'nasabah')->pluck('name', 'id'))
                    ->required()
                    ->searchable()
                    ->label('Nasabah'),
                Select::make('donation_category')
                    ->options([
                        'umum' => 'Tabungan Pribadi (Saldo masuk rekening nasabah)',
                        'donasi' => 'Sedekah / Donasi (Disumbangkan untuk program sosial)',
                    ])
                    ->required()
                    ->default('umum')
                    ->label('Kategori Setoran')
                    ->helperText('"Tabungan" = saldo masuk ke rekening nasabah. "Donasi" = nilai sampah disumbangkan.'),
                Repeater::make('items')
                    ->label('Detail Item Sampah')
                    ->schema([
                        Select::make('trash_price_id')
                            ->options(
                                TrashPrice::where('category_type', 'umum')
                                    ->get()
                                    ->mapWithKeys(fn ($tp) => [
                                        $tp->id => "{$tp->name} — Rp " . number_format($tp->price_buy, 0, ',', '.') . " / {$tp->unit}",
                                    ])
                            )
                            ->required()
                            ->searchable()
                            ->label('Jenis Sampah')
                            ->columnSpan(1),
                        TextInput::make('weight')
                            ->numeric()
                            ->required()
                            ->step(0.01)
                            ->minValue(0.01)
                            ->label('Berat (kg/L)')
                            ->columnSpan(1),
                    ])
                    ->columns(2)
                    ->minItems(1)
                    ->addActionLabel('Tambah Item Sampah')
                    ->defaultItems(1),
                Textarea::make('notes')
                    ->maxLength(65535)
                    ->label('Catatan'),
            ]);
    }
}
