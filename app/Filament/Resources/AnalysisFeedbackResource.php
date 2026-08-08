<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AnalysisFeedbackResource\Pages;
use App\Models\AnalysisFeedback;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class AnalysisFeedbackResource extends Resource
{
    protected static ?string $model = AnalysisFeedback::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-star';

    protected static ?string $navigationLabel = 'Penilaian Kesesuaian';

    protected static ?string $modelLabel = 'Penilaian';

    protected static ?string $pluralModelLabel = 'Penilaian Kesesuaian';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('analysis_id')
                    ->label('Analisa')
                    ->relationship('analysis', 'title')
                    ->searchable()
                    ->disabled(),

                Select::make('user_id')
                    ->label('Pengguna')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->disabled(),

                Toggle::make('is_accurate')
                    ->label('Dinilai Akurat')
                    ->disabled(),

                Textarea::make('comments')
                    ->label('Komentar')
                    ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('analysis.title')
                    ->label('Judul Analisa')
                    ->limit(45)
                    ->searchable(),

                TextColumn::make('user.name')
                    ->label('Pengguna')
                    ->searchable()
                    ->sortable(),

                IconColumn::make('is_accurate')
                    ->label('Akurat?')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                TextColumn::make('comments')
                    ->label('Komentar')
                    ->limit(60)
                    ->placeholder('— Tidak ada komentar'),

                TextColumn::make('created_at')
                    ->label('Dinilai pada')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_accurate')
                    ->label('Kesesuaian')
                    ->trueLabel('Akurat')
                    ->falseLabel('Tidak Akurat'),
            ])
            ->recordActions([
                ViewAction::make(),
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
            'index' => Pages\ListAnalysisFeedbacks::route('/'),
        ];
    }
}
