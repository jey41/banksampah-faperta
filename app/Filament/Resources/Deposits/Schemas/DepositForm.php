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
                            ->columnSpan(1)
                            ->live(),
                        TextInput::make('weight')
                            ->numeric()
                            ->required()
                            ->step(0.01)
                            ->minValue(0.01)
                            ->label('Berat (kg/L)')
                            ->columnSpan(1)
                            ->live(onBlur: true),
                    ])
                    ->columns(2)
                    ->minItems(1)
                    ->addActionLabel('Tambah Item Sampah')
                    ->defaultItems(1)
                    ->live(),
                Placeholder::make('total_calculation')
                    ->label('Kalkulasi Total Setoran')
                    ->content(function ($get) {
                        $items = $get('items') ?? [];
                        $totalWeight = 0;
                        $totalPrice = 0;

                        foreach ($items as $item) {
                            $trashPriceId = $item['trash_price_id'] ?? null;
                            $weight = floatval($item['weight'] ?? 0);

                            if ($trashPriceId && $weight > 0) {
                                $trashPrice = TrashPrice::find($trashPriceId);
                                if ($trashPrice) {
                                    $totalWeight += $weight;
                                    $totalPrice += $weight * $trashPrice->price_buy;
                                }
                            }
                        }

                        return new \Illuminate\Support\HtmlString("
                            <div class='p-4 bg-gray-50 rounded-xl border border-gray-200 space-y-2 dark:bg-gray-800 dark:border-gray-700'>
                                <div class='flex justify-between text-sm'>
                                    <span class='text-gray-500 dark:text-gray-400'>Total Volume/Berat:</span>
                                    <span class='font-bold text-gray-900 dark:text-white'>" . number_format($totalWeight, 2, ',', '.') . " kg/L</span>
                                </div>
                                <div class='flex justify-between text-sm'>
                                    <span class='text-gray-500 dark:text-gray-400'>Total Nilai Sampah:</span>
                                    <span class='font-bold text-primary-600 dark:text-primary-400'>Rp " . number_format($totalPrice, 0, ',', '.') . "</span>
                                </div>
                            </div>
                        ");
                    }),
                Textarea::make('notes')
                    ->maxLength(65535)
                    ->label('Catatan'),
            ]);
    }
}
