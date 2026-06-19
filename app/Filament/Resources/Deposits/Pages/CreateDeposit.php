<?php

namespace App\Filament\Resources\Deposits\Pages;

use App\Filament\Resources\Deposits\DepositResource;
use App\Models\Deposit;
use App\Models\TrashPrice;
use App\Services\TransactionService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;

class CreateDeposit extends CreateRecord
{
    protected static string $resource = DepositResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        return DB::transaction(function () use ($data) {
            $totalPrice = 0;
            $weightTotal = 0;
            $itemsToCreate = [];

            foreach ($data['items'] as $itemData) {
                $trashPrice = TrashPrice::findOrFail($itemData['trash_price_id']);
                $itemWeight = (float) $itemData['weight'];
                $itemPrice = $itemWeight * $trashPrice->price_buy;

                $weightTotal += $itemWeight;
                $totalPrice += $itemPrice;

                $itemsToCreate[] = [
                    'trash_price_id' => $trashPrice->id,
                    'item_name' => $trashPrice->name,
                    'item_category' => $trashPrice->category,
                    'item_category_type' => $trashPrice->category_type,
                    'weight' => $itemWeight,
                    'price_per_unit' => $trashPrice->price_buy,
                    'total_price' => $itemPrice,
                    'total_carbon' => $itemWeight * ($trashPrice->carbon_factor ?? 0),
                ];
            }

            $isDonation = ($data['donation_category'] ?? 'umum') === 'donasi';

            $deposit = Deposit::create([
                'user_id' => $data['user_id'],
                'total_price' => $totalPrice,
                'weight_total' => $weightTotal,
                'status' => 'pending',
                'is_donation' => $isDonation,
                'donation_category' => $data['donation_category'] ?? 'umum',
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($itemsToCreate as $item) {
                $deposit->items()->create($item);
            }

            // Auto-approve since admin is creating it directly with real weights
            try {
                $approvalItems = $deposit->items->map(fn($item) => [
                    'id' => $item->id,
                    'weight' => $item->weight,
                ])->toArray();

                app(TransactionService::class)->approveDeposit($deposit, $approvalItems, auth()->id());

                Notification::make()
                    ->title('Setoran Berhasil Dibuat & Disetujui')
                    ->body('Setoran telah otomatis disetujui dan saldo nasabah telah diperbarui.')
                    ->success()
                    ->send();
            } catch (\Exception $e) {
                Notification::make()
                    ->title('Setoran Dibuat (Pending)')
                    ->body('Setoran berhasil dibuat tetapi gagal di-approve otomatis: ' . $e->getMessage())
                    ->warning()
                    ->send();
            }

            return $deposit;
        });
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
