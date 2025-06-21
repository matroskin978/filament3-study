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

                    Forms\Components\Section::make('Основное')->description('Основная информация о пользователе')->icon('heroicon-o-user')->schema([
                        TextInput::make('first_name'),
                        TextInput::make('middle_name'),
                        TextInput::make('last_name'),
                        TextInput::make('email')->email(),
                        TextInput::make('password')->password()->revealable()->columnSpan('full'),
                    ])->columns(2)->collapsible(),

                    Forms\Components\Section::make('Контакты')->description('Контактная информация пользователя')->icon('heroicon-o-map')->schema([
                        Select::make('country')->options(['Country 1', 'Country 2', 'Country 3']),
                        Select::make('city')->options(['City 1', 'City 2', 'City 3']),
                        Select::make('street')->options(['Street 1', 'Street 2', 'Street 3']),
                        TextInput::make('zip'),
                        TextInput::make('phone')->tel()->mask('+99 999 999-99-99'),
                    ])->columns(2)->collapsible(),

                ])->columnSpan(2),

                Forms\Components\Group::make()->schema([

                    Forms\Components\Section::make('Дополнительно')->description('Дополнительная информация о пользователе')->icon('heroicon-o-user')->schema([
                        Select::make('dob')->options(
                            array_combine(
                                range(date('Y'), 1900),
                                range(date('Y'), 1900),
                            )
                        ),
                        Radio::make('gender')->options(['Male', 'Female'])->inline(),
                    ])->collapsible(),

                    Forms\Components\Section::make('Аватар')->description('И  еще немного')->icon('heroicon-o-user')->schema([
                        FileUpload::make('avatar')->image(),
                    ])->collapsible()->collapsed(),

                    Forms\Components\Section::make('Примечаение')->description('И  еще чуть-чуть')->icon('heroicon-o-user')->schema([
                        Forms\Components\Textarea::make('notes')->rows(5)
                    ])->collapsible()->collapsed(),

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
