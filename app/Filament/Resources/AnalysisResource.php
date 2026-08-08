<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AnalysisResource\Pages;
use App\Models\Analysis;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AnalysisResource extends Resource
{
    protected static ?string $model = Analysis::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-document-chart-bar';

    protected static ?string $navigationLabel = 'Riwayat Analisa';

    protected static ?string $modelLabel = 'Analisa';

    protected static ?string $pluralModelLabel = 'Semua Analisa';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')->label('Judul')->disabled(),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Sesi Analisa')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('title')
                            ->label('Judul Sesi'),

                        TextEntry::make('user.name')
                            ->label('Pengguna'),

                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn (string $state) => match ($state) {
                                'completed'       => 'success',
                                'processing'      => 'warning',
                                'failed'          => 'danger',
                                'partial_failure' => 'danger',
                                default           => 'gray',
                            }),

                        TextEntry::make('locale')
                            ->label('Bahasa'),

                        TextEntry::make('model_used')
                            ->label('Model AI'),

                        TextEntry::make('total_chunks')
                            ->label('Total Potongan Audio'),

                        TextEntry::make('processed_chunks')
                            ->label('Potongan Selesai'),

                        TextEntry::make('duration_seconds')
                            ->label('Durasi Audio (detik)'),

                        TextEntry::make('created_at')
                            ->label('Dibuat')
                            ->dateTime('d M Y, H:i'),
                    ]),

                Section::make('Penilaian Kesesuaian')
                    ->schema([
                        TextEntry::make('feedback.is_accurate')
                            ->label('Dinilai Akurat?')
                            ->formatStateUsing(fn ($state) => match ((string) $state) {
                                '1'  => '✅ Ya, Akurat',
                                '0'  => '❌ Tidak Akurat',
                                default => '— Belum dinilai',
                            }),

                        TextEntry::make('feedback.comments')
                            ->label('Komentar')
                            ->default('—'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('title')
                    ->label('Judul Sesi')
                    ->searchable()
                    ->limit(40),

                TextColumn::make('user.name')
                    ->label('Pengguna')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'completed'       => 'success',
                        'processing'      => 'warning',
                        'synthesizing'    => 'warning',
                        'failed'          => 'danger',
                        'partial_failure' => 'danger',
                        default           => 'gray',
                    })
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'completed'       => 'Selesai',
                        'processing'      => 'Diproses',
                        'synthesizing'    => 'Sintesis',
                        'failed'          => 'Gagal',
                        'partial_failure' => 'Gagal Sebagian',
                        default           => ucfirst($state),
                    }),

                TextColumn::make('locale')
                    ->label('Bahasa')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'id' => '🇮🇩 Indonesia',
                        'en' => '🇬🇧 English',
                        'zh' => '🇨🇳 中文',
                        default => $state,
                    }),

                TextColumn::make('total_chunks')
                    ->label('Potongan')
                    ->sortable(),

                IconColumn::make('feedback.is_accurate')
                    ->label('Akurat?')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'completed'       => 'Selesai',
                        'processing'      => 'Diproses',
                        'failed'          => 'Gagal',
                        'partial_failure' => 'Gagal Sebagian',
                    ]),

                SelectFilter::make('locale')
                    ->label('Bahasa')
                    ->options([
                        'id' => 'Indonesia',
                        'en' => 'English',
                        'zh' => 'Chinese',
                    ]),

                SelectFilter::make('user_id')
                    ->label('Pengguna')
                    ->relationship('user', 'name'),
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('viewResult')
                    ->label('Lihat Hasil')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('primary')
                    ->url(fn (Analysis $record) => route('analysis.result', $record->slug))
                    ->openUrlInNewTab()
                    ->visible(fn (Analysis $record) => $record->status === 'completed'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAnalyses::route('/'),
            'view'  => Pages\ViewAnalysis::route('/{record}'),
        ];
    }
}
