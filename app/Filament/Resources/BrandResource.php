<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BrandResource\Pages;
use App\Filament\Resources\BrandResource\RelationManagers;
use App\Models\Brand;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class BrandResource extends Resource
{
    protected static ?string $model = Brand::class;

    protected static ?string $navigationIcon = 'heroicon-s-globe-alt';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\Group::make()->schema([
                    Forms\Components\Section::make()->schema([

                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->live(true)
                            ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get, ?string $state, string $operation) {
                                if ($operation === 'edit' && $get('slug')) {
                                    return;
                                }
                                $set('slug', Str::slug($state));
                            }),

                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->helperText('Generated automatically based on title'),

                        Forms\Components\RichEditor::make('description')
                            ->fileAttachmentsDirectory("images/" . date('Y') . '/' . date('m') . '/' . date('d')),

                    ])
                ])->columnSpan(2),

                Forms\Components\Group::make()->schema([
                    Forms\Components\Section::make()->schema([

                        Forms\Components\FileUpload::make('photo')
                            ->image()
                            ->directory("preview/" . date('Y') . '/' . date('m') . '/' . date('d')),

                    ])
                ]),


            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('title', 'asc')
            ->striped()
            ->columns([

                Tables\Columns\TextColumn::make('id')->label('ID')->sortable(),

                Tables\Columns\ImageColumn::make('photo'),

                Tables\Columns\TextColumn::make('title')->searchable()->sortable(),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBrands::route('/'),
            'create' => Pages\CreateBrand::route('/create'),
            'edit' => Pages\EditBrand::route('/{record}/edit'),
        ];
    }
}
