<?php

namespace App\Filament\Resources\TrashPrices\Pages;

use App\Filament\Resources\TrashPrices\TrashPriceResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTrashPrice extends EditRecord
{
    protected static string $resource = TrashPriceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
