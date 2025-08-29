<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\RelationManagers;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use function Livewire\before;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
                            ->hint('Generated automatically based on title'),

                        Forms\Components\RichEditor::make('description')
                            ->fileAttachmentsDirectory("images/" . date('Y') . '/' . date('m') . '/' . date('d')),

                    ])
                ])->columnSpan(2),

                Forms\Components\Group::make()->schema([
                    Forms\Components\Section::make()->schema([

                        Forms\Components\Select::make('parent_id')
                            ->options(function () {
                                return Category::getCategoriesTree(Category::all());
                            })
                            ->disableOptionWhen(function (Forms\Get $get, string $value) {
                                return $value == $get('id');
                            })
                            ->placeholder('Root category'),

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
            ->defaultSort('id', 'desc')
            ->striped()
            ->defaultPaginationPageOption(3)
            ->paginated([3, 5, 10, 20, 'all'])
            ->extremePaginationLinks()
            ->columns([

                Tables\Columns\TextColumn::make('my_id')
                    ->label('#')
                    ->state(function (Tables\Contracts\HasTable $livewire, \stdClass $rowLoop) {
                        if ($livewire->getTableRecordsPerPage() == 'all') {
                            return $rowLoop->iteration;
                        }
                        return $rowLoop->iteration + ($livewire->getTableRecordsPerPage() * ($livewire->getTablePage() - 1));
                    }),

                Tables\Columns\TextColumn::make('id')->label('ID')->sortable(),

                Tables\Columns\ImageColumn::make('photo'),

                Tables\Columns\TextColumn::make('title')->searchable()->sortable(),

                Tables\Columns\TextColumn::make('parent.title')->sortable(),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()
                        ->disabled(function ($record) {
                            return $record->children()->exists() || $record->products()->exists();
                        })
                        ->before(function ($record, $action) {
                            if ($record->children()->exists() || $record->products()->exists()) {
                                Notification::make()
                                    ->body('Forbidden!')
                                    ->danger()
                                    ->send();
                                $action->cancel();
                            }
                        }),
//                Tables\Actions\ViewAction::make(),
                ])

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
