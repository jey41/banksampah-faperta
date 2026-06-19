<?php

namespace App\Filament\Resources\PickupRequests\Pages;

use App\Filament\Resources\PickupRequests\PickupRequestResource;
use Filament\Resources\Pages\EditRecord;

class EditPickupRequest extends EditRecord
{
    protected static string $resource = PickupRequestResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
