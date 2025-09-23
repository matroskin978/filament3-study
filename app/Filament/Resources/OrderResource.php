<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\Wizard::make()->schema([

                    Forms\Components\Wizard\Step::make('Customer')->schema([

                        Forms\Components\Select::make('user_id')
                            ->label('Exists user')
                            ->searchable()
                            ->live()
                            ->getSearchResultsUsing(function (string $search) {
                                return User::query()
                                    ->whereLike('name', "%{$search}%")
                                    ->limit(10)
                                    ->pluck('name', 'id')
                                    ->toArray();
                            })
                            ->getOptionLabelUsing(fn($value): ?string => User::find($value)?->name)
                            ->afterStateUpdated(function ($state, $set) {
                                $user = User::query()->find($state);

                                $set('name', $user?->name);
                                $set('email', $user?->email);
                            }),
                        //->getSearchResultsUsing(fn (string $search): array => User::where('name', 'like', "%{$search}%")->limit(50)->pluck('name', 'id')->toArray())
                        //->getOptionLabelUsing(fn ($value): ?string => User::find($value)?->name),

                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('address')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'processing' => 'Processing',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
                            ])
                            ->required()
                            ->default('pending'),

                        Forms\Components\Textarea::make('note')
                            ->columnSpanFull(),

                    ])->columns(),

                    Forms\Components\Wizard\Step::make('Products')->schema([

                        Forms\Components\Repeater::make('products')
                            ->relationship('orderProducts')
                            ->schema([

                                Forms\Components\Select::make('product_id')
                                    ->label('Search product')
                                    ->searchable()
                                    ->live()
                                    ->getSearchResultsUsing(function (string $search) {
                                        return Product::query()
                                            ->whereLike('title', "%{$search}%")
                                            ->limit(10)
                                            ->pluck('title', 'id')
                                            ->toArray();
                                    })
                                    ->getOptionLabelUsing(fn($value): ?string => Product::find($value)?->title)
                                    ->afterStateUpdated(function ($state, $set) {
                                        $product = Product::query()->find($state);

                                        $set('title', $product?->title);
                                        $set('slug', $product?->slug);
                                        $set('price', $product?->price);
                                        $set('photo', $product?->photo);
                                    }),

                                Forms\Components\TextInput::make('quantity')
                                    ->required()
                                    ->numeric()
                                    ->default(1),

                                Forms\Components\TextInput::make('title')
                                    ->required()
                                    ->maxLength(255)
                                    ->disabled()
                                    ->dehydrated(),

                                Forms\Components\TextInput::make('slug')
                                    ->required()
                                    ->maxLength(255)
                                    ->disabled()
                                    ->dehydrated(),

                                Forms\Components\TextInput::make('price')
                                    ->required()
                                    ->disabled()
                                    ->dehydrated(),

                                Forms\Components\TextInput::make('photo')
                                    ->maxLength(255)
                                    ->disabled()
                                    ->dehydrated(),

                            ])->columnSpanFull()->columns(),

                        Forms\Components\TextInput::make('shipping')
                            ->required()
                            ->numeric()
                            ->default(0),

                        Forms\Components\TextInput::make('discount')
                            ->required()
                            ->numeric()
                            ->default(0),

                        Forms\Components\TextInput::make('total')
                            ->required()
                            ->numeric(),

                    ])->columns(),

                ])->columnSpanFull(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('address')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\TextColumn::make('total')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('shipping')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('discount')
                    ->numeric()
                    ->sortable(),
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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
