<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\RelationManagers;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\KeyValue;
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
                TextInput::make('title')
                    ->default('Test article')
                    ->helperText(new HtmlString('Helper text for <strong>title</strong>'))
                    ->hint('Hint for title')
                    ->hintIcon('heroicon-m-language', 'Tooltip for title')
                    ->hintColor('primary')
//                    ->disabled()
                    ->disabledOn('edit')
                    ->hiddenOn('edit')
                    ->autofocus()
                    ->required()
                    ->columnSpan(2)
                    ->label('Наименование'),
                TextInput::make('slug'),
                Forms\Components\Select::make('status')->options([
                    1 => 'Draft', 'Published', 'Reviewing'
                ])
//                    ->native(false)
                    ->multiple()
                    ->searchable(),

                Forms\Components\FileUpload::make('image')
                    //->disk('public_uploads')
                    ->directory("preview/" . date('Y') . '/' . date('m') . '/' . date('d'))
                    ->imageEditor()
                    ->reorderable()
                    ->acceptedFileTypes(['image/png', 'image/jpeg'])
                    ->imageEditorAspectRatios([
                        null,
                        '16:9',
                        '4:3',
                        '1:1',
                    ])
                    ->multiple()
                    ->columnSpan(2),

                Forms\Components\RichEditor::make('content')->columnSpan(2),

                Repeater::make('users')
                    ->schema([
                        TextInput::make('name')->required()->live(true),
                        Select::make('role')
                            ->options([
                                'user' => 'User',
                                'manager' => 'Manager',
                                'admin' => 'Admin',
                            ])
                            ->required(),
                    ])
                    ->addActionLabel('Add user')
                    ->cloneable()
                    ->collapsible()
                    ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
//                    ->defaultItems(3)
                    ->columns(2)->columnSpan(2),

                KeyValue::make('meta')->columnSpan(2),

                Forms\Components\Builder::make('page_content')
                    ->blocks([
                        Block::make('heading')
                            ->schema([
                                TextInput::make('content')
                                    ->label('Heading')
                                    ->required(),
                                Select::make('level')
                                    ->options([
                                        'h1' => 'Heading 1',
                                        'h2' => 'Heading 2',
                                        'h3' => 'Heading 3',
                                        'h4' => 'Heading 4',
                                        'h5' => 'Heading 5',
                                        'h6' => 'Heading 6',
                                    ])
                                    ->required(),
                            ])
                            ->columns(2),
                        Block::make('paragraph')
                            ->schema([
                                Forms\Components\Textarea::make('content')
                                    ->label('Paragraph')
                                    ->required(),
                            ]),
                        Block::make('image')
                            ->schema([
                                Forms\Components\FileUpload::make('url')
                                    ->label('Image')
                                    ->image()
                                    ->required(),
                                TextInput::make('alt')
                                    ->label('Alt text')
                                    ->required(),
                            ]),
                    ])->columnSpan(2),

                Forms\Components\DatePicker::make('published_at')
                    ->native(false)
                    ->locale('ru')
                    ->format('Y-m-d')
                    ->minDate(now()->subDays(7))
                    ->maxDate(now()->addDays(7))
                    ->closeOnDateSelection()
                    ->displayFormat('d M Y'),
                TextInput::make('email')->email(),
                TextInput::make('password')->password()->revealable(),
                TextInput::make('phone')->tel()->placeholder('xxx xxx-xx-xx')->mask('999 999-99-99'),
                TextInput::make('domain')->prefix('https://')->suffix('.com')->suffixIcon('heroicon-m-globe-alt'),
            ])->columns(2);
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
