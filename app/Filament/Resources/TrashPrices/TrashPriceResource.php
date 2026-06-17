<?php

namespace App\Filament\Resources\TrashPrices;

use App\Filament\Resources\TrashPrices\Pages\CreateTrashPrice;
use App\Filament\Resources\TrashPrices\Pages\EditTrashPrice;
use App\Filament\Resources\TrashPrices\Pages\ListTrashPrices;
use App\Filament\Resources\TrashPrices\Schemas\TrashPriceForm;
use App\Filament\Resources\TrashPrices\Tables\TrashPricesTable;
use App\Models\TrashPrice;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TrashPriceResource extends Resource
{
    protected static ?string $model = TrashPrice::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static ?string $navigationLabel = 'Harga Master Sampah';

    protected static ?string $pluralModelLabel = 'Harga Master Sampah';

    protected static ?string $modelLabel = 'Harga Sampah';

    public static function form(Schema $schema): Schema
    {
        return TrashPriceForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TrashPricesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTrashPrices::route('/'),
            'create' => CreateTrashPrice::route('/create'),
            'edit' => EditTrashPrice::route('/{record}/edit'),
        ];
    }
}
