<?php

namespace App\Filament\Resources\PickupRequests;

use App\Filament\Resources\PickupRequests\Pages\EditPickupRequest;
use App\Filament\Resources\PickupRequests\Pages\ListPickupRequests;
use App\Filament\Resources\PickupRequests\Schemas\PickupRequestForm;
use App\Filament\Resources\PickupRequests\Tables\PickupRequestsTable;
use App\Models\PickupRequest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PickupRequestResource extends Resource
{
    protected static ?string $model = PickupRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTruck;

    protected static ?string $navigationLabel = 'Permintaan Jemput';

    protected static ?string $pluralModelLabel = 'Permintaan Jemput';

    protected static ?string $modelLabel = 'Permintaan Jemput';

    public static function form(Schema $schema): Schema
    {
        return PickupRequestForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PickupRequestsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPickupRequests::route('/'),
            'edit' => EditPickupRequest::route('/{record}/edit'),
        ];
    }
}
