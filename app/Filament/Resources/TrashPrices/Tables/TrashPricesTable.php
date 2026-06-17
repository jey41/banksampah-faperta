<?php

namespace App\Filament\Resources\TrashPrices\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;

class TrashPricesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('Nama Sampah'),
                TextColumn::make('category')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'plastik' => 'success',
                        'kertas' => 'info',
                        'logam' => 'warning',
                        'kaca' => 'gray',
                        'minyak_jelantah' => 'primary',
                        default => 'danger',
                    })
                    ->label('Kategori'),
                TextColumn::make('price_buy')
                    ->money('IDR', locale: 'id')
                    ->sortable()
                    ->label('Harga Beli (Nasabah)'),
                TextColumn::make('price_sell')
                    ->money('IDR', locale: 'id')
                    ->sortable()
                    ->label('Harga Jual (Pabrik)'),
                TextColumn::make('unit')
                    ->label('Satuan'),
                TextColumn::make('carbon_factor')
                    ->sortable()
                    ->label('Faktor Karbon'),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->options([
                        'plastik' => 'Plastik',
                        'kertas' => 'Kertas',
                        'logam' => 'Logam',
                        'kaca' => 'Kaca',
                        'minyak_jelantah' => 'Minyak Jelantah',
                        'lainnya' => 'Lainnya',
                    ])
                    ->label('Kategori'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
