<?php

namespace App\Filament\Resources\PickupRequests\Pages;

use App\Filament\Resources\PickupRequests\PickupRequestResource;
use Filament\Resources\Pages\ListRecords;

class ListPickupRequests extends ListRecords
{
    protected static string $resource = PickupRequestResource::class;
}
