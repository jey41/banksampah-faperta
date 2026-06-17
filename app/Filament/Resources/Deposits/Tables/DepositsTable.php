<?php

namespace App\Filament\Resources\Deposits\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;
use Filament\Support\Icons\Heroicon;
use App\Models\TrashPrice;
use Illuminate\Support\Facades\DB;

class DepositsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->searchable()
                    ->sortable()
                    ->label('Nasabah'),
                TextColumn::make('total_price')
                    ->money('IDR', locale: 'id')
                    ->sortable()
                    ->label('Total Uang'),
                TextColumn::make('weight_total')
                    ->suffix(' kg/L')
                    ->sortable()
                    ->label('Total Berat'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'approved' => 'success',
                        'pending' => 'warning',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->label('Status'),
                TextColumn::make('validator.name')
                    ->label('Diverifikasi Oleh')
                    ->placeholder('-'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Tanggal Masuk'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->label('Status'),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('Setujui & Timbang')
                    ->color('success')
                    ->icon(Heroicon::OutlinedCheck)
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->form(function ($record) {
                        return [
                            Repeater::make('items')
                                ->label('Detail Item Sampah (Masukkan Berat Riil)')
                                ->schema([
                                    Hidden::make('id'),
                                    Select::make('trash_price_id')
                                        ->options(TrashPrice::all()->pluck('name', 'id'))
                                        ->disabled()
                                        ->label('Kategori Sampah'),
                                    TextInput::make('weight')
                                        ->numeric()
                                        ->required()
                                        ->label('Berat Riil (kg/L)'),
                                ])
                                ->default(function () use ($record) {
                                    return $record->items->map(fn ($item) => [
                                        'id' => $item->id,
                                        'trash_price_id' => $item->trash_price_id,
                                        'weight' => $item->weight,
                                    ])->toArray();
                                })
                                ->disableItemCreation()
                                ->disableItemDeletion()
                        ];
                    })
                    ->action(function ($record, array $data) {
                        try {
                            app(\App\Services\TransactionService::class)->approveDeposit($record, $data['items'], auth()->id());

                            \Filament\Notifications\Notification::make()
                                ->title('Setoran Disetujui')
                                ->body('Setoran berhasil ditimbang dan saldo ditambahkan.')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            \Filament\Notifications\Notification::make()
                                ->title('Gagal Memproses Setoran')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                Action::make('reject')
                    ->label('Tolak')
                    ->color('danger')
                    ->icon(Heroicon::OutlinedXMark)
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        try {
                            app(\App\Services\TransactionService::class)->rejectDeposit($record, auth()->id());

                            \Filament\Notifications\Notification::make()
                                ->title('Setoran Ditolak')
                                ->body('Permohonan setoran sampah telah ditolak.')
                                ->info()
                                ->send();
                        } catch (\Exception $e) {
                            \Filament\Notifications\Notification::make()
                                ->title('Gagal Menolak Setoran')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                EditAction::make(),
                Action::make('print')
                    ->label('Cetak')
                    ->color('info')
                    ->icon('heroicon-o-printer')
                    ->url(fn ($record) => route('admin.deposit.print', $record))
                    ->openUrlInNewTab(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
