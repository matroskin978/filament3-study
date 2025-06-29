<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\RelationManagers;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

//    protected static ?string $modelLabel = 'Категория';
//    protected static ?string $pluralModelLabel = 'Категории';

    protected static ?string $label = 'Категория';
    protected static ?string $pluralLabel = 'Категории';
    protected static ?string $navigationLabel = 'Список категорий';
    protected static ?string $navigationGroup = 'Блог';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\Group::make()->schema([
                    Forms\Components\Section::make()->schema([

                        TextInput::make('title')
                            ->required()
                            ->minLength(5)
                            ->live(true)
                            ->afterStateUpdated(function (Forms\Set $set, ?string $state, string $operation) {
                                if ($operation === 'edit') {
                                    return;
                                }
                                $set('slug', Str::slug($state));
                            }),

                        TextInput::make('slug')
                            /*->hidden(function (Forms\Get $get) {
                                return !$get('title');
                            })*/
//                            ->hidden(fn (Forms\Get $get): bool => !$get('title'))
                            ->disabledOn('edit')
                            ->required()
                            ->unique()
                            ->helperText('Генерируется автоматически на основе наименования'),

                        Forms\Components\RichEditor::make('content')->columnSpan(2)->required()

                    ])->columns(2)
                ])->columnSpan(2),

                Forms\Components\Group::make()->schema([

                    Forms\Components\Section::make()->schema([

                        FileUpload::make('image')
                            ->image()
                            ->directory("preview/" . date('Y') . '/' . date('m') . '/' . date('d')),

                    ])

                ]),

            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
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
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
