<?php

namespace App\Filament\Resources\Withdrawals\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Support\Icons\Heroicon;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class WithdrawalsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->searchable()
                    ->sortable()
                    ->label('Nasabah'),
                TextColumn::make('amount')
                    ->money('IDR', locale: 'id')
                    ->sortable()
                    ->label('Jumlah Penarikan'),
                TextColumn::make('bank_name')
                    ->searchable()
                    ->label('Bank/E-Wallet'),
                TextColumn::make('account_number')
                    ->searchable()
                    ->label('No. Rekening/HP'),
                TextColumn::make('account_name')
                    ->searchable()
                    ->label('Penerima'),
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
                    ->label('Tanggal Pengajuan'),
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
                    ->label('Setujui & Transfer')
                    ->color('success')
                    ->icon(Heroicon::OutlinedCheck)
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        try {
                            app(\App\Services\TransactionService::class)->approveWithdrawal($record, auth()->id());

                            Notification::make()
                                ->title('Penarikan Disetujui')
                                ->body('Penarikan saldo sebesar Rp ' . number_format($record->amount, 0, ',', '.') . ' telah disetujui.')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Gagal Memproses Penarikan')
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
                            app(\App\Services\TransactionService::class)->rejectWithdrawal($record, auth()->id());

                            Notification::make()
                                ->title('Penarikan Ditolak')
                                ->body('Permohonan penarikan saldo telah ditolak.')
                                ->info()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Gagal Menolak Penarikan')
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
                    ->url(fn ($record) => route('admin.withdrawal.print', $record))
                    ->openUrlInNewTab(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
