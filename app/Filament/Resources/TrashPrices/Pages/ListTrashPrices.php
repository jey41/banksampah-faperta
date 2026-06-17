<?php

namespace App\Filament\Resources\TrashPrices\Pages;

use App\Filament\Resources\TrashPrices\TrashPriceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTrashPrices extends ListRecords
{
    protected static string $resource = TrashPriceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
