<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-table-cells';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\Group::make([

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
                            ->hint('Generated automatically based on title'),

                        Forms\Components\Select::make('category_id')
                            ->options(function () {
                                return Category::getCategoriesTree(Category::all());
                            })
                            ->required()
                            ->exists(table: Category::class, column: 'id')
                            ->placeholder('Select category'),

                        Forms\Components\Select::make('brand_id')
                            ->placeholder('Select brand')
                            ->options(Brand::all()->pluck('title', 'id')),

                        Forms\Components\Textarea::make('excerpt')
                            ->maxLength(255)
                            ->default(null)
                            ->columnSpanFull(),

                        Forms\Components\RichEditor::make('description')
                            ->fileAttachmentsDirectory("images/" . date('Y') . '/' . date('m') . '/' . date('d'))
                            ->columnSpanFull(),

                    ])->columns()

                ])->columnSpan(2),

                Forms\Components\Group::make([

                    Forms\Components\Section::make()->schema([

                        Forms\Components\TextInput::make('sku')
                            ->label('SKU')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        Forms\Components\TextInput::make('quantity')
                            ->required()
                            ->numeric()
                            ->default(1),

                        Forms\Components\TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->prefix('$')
                            ->default(0),

                        Forms\Components\TextInput::make('old_price')
                            ->required()
                            ->numeric()
                            ->default(0),

                        Forms\Components\Toggle::make('is_visible')
                            ->default(true)
                            ->required(),

                        Forms\Components\Toggle::make('is_featured')
                            ->required(),

                        Forms\Components\Toggle::make('is_hit')
                            ->required(),

                        Forms\Components\Toggle::make('is_sale')
                            ->required(),

                    ])->columns(),

                    Forms\Components\Section::make('Photos')->schema([

                        Forms\Components\FileUpload::make('photo')
                            ->image()
                            ->directory("preview/" . date('Y') . '/' . date('m') . '/' . date('d')),

                        Forms\Components\FileUpload::make('photos')
                            ->image()
                            ->multiple()
                            ->directory("preview/" . date('Y') . '/' . date('m') . '/' . date('d')),

                    ])->collapsible(),

                ]),

            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('category_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('brand_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable(),
                Tables\Columns\TextColumn::make('excerpt')
                    ->searchable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->money()
                    ->sortable(),
                Tables\Columns\TextColumn::make('old_price')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_visible')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_featured')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_hit')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_sale')
                    ->boolean(),
                Tables\Columns\TextColumn::make('photo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
