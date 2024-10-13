<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Filament\Resources\ProductResource\RelationManagers\PromotionRelationManager;
use App\Filament\Resources\ProductResource\RelationManagers\ReviewsRelationManager;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\Section::make()->description('Product Information')->schema([
                        Forms\Components\Select::make('brand_id')
                            ->label('Brand')
                            ->relationship('brand', 'name')
                            ->searchable()
                            ->default(null),
                        Forms\Components\Select::make('category_id')
                            ->label('Category')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->default(null),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->unique(Product::class, 'name', ignoreRecord: true)
                            ->afterStateUpdated(function (string $operation, $state, Forms\Set $set) {
                                /*if ($operation !== 'create') {
                                return;
                            }*/

                                $set('slug', \Illuminate\Support\Str::slug($state));
                            }),

                        Forms\Components\TextInput::make('slug')
                            ->disabled()
                            ->dehydrated()
                            ->required()
                            ->maxLength(255)
                            ->unique(Product::class, 'slug', ignoreRecord: true),
                        Forms\Components\TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->prefix('₱'),
                        Forms\Components\TextInput::make('stock_quantity')
                            ->required()
                            ->numeric(),
                        Forms\Components\Textarea::make('description')
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('images')
                            ->label('Images')
                            ->multiple()
                            ->columnSpanFull(),
                    ])->columns(2),
                ])
                    ->columnSpan([
                        'sm' => 3,
                        'md' => 3,
                        'lg' => 2
                    ]),

                Forms\Components\Grid::make(1)->schema([

                    Forms\Components\Section::make()->description('Status')->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->onIcon('heroicon-m-bolt')
                            ->offIcon('heroicon-m-bolt-slash')
                            ->required(),
                    ]),

                    Forms\Components\Section::make()->schema([
                        Forms\Components\TextInput::make('sku')
                            ->label('SKU')
                            ->required()
                            ->maxLength(255),
                    ]),
                    Forms\Components\Section::make()->schema([
                        Forms\Components\Placeholder::make('created_at')
                            ->label('Created at')
                            ->hiddenOn('create')
                            ->content(function (\Illuminate\Database\Eloquent\Model $record): String {
                                $category = Product::find($record->id);
                                $now = \Carbon\Carbon::now();

                                $diff = $category->created_at->diff($now);
                                if ($diff->y > 0) {
                                    return $diff->y . ' years ago';
                                } elseif ($diff->m > 0) {
                                    if ($diff->m == 1) {
                                        return '1 month ago';
                                    } else {
                                        return $diff->m . ' months ago';
                                    }
                                } elseif ($diff->d >= 7) {
                                    $weeks = floor($diff->d / 7);
                                    if ($weeks == 1) {
                                        return 'a week ago';
                                    } else {
                                        return $weeks . ' weeks ago';
                                    }
                                } elseif ($diff->d > 0) {
                                    if ($diff->d == 1) {
                                        return 'yesterday';
                                    } else {
                                        return $diff->d . ' days ago';
                                    }
                                } elseif ($diff->h > 0) {
                                    if ($diff->h == 1) {
                                        return '1 hour ago';
                                    } else {
                                        return $diff->h . ' hours ago';
                                    }
                                } elseif ($diff->i > 0) {
                                    if ($diff->i == 1) {
                                        return '1 minute ago';
                                    } else {
                                        return $diff->i . ' minutes ago';
                                    }
                                } elseif ($diff->s > 0) {
                                    if ($diff->s == 1) {
                                        return '1 second ago';
                                    } else {
                                        return $diff->s . ' seconds ago';
                                    }
                                } else {
                                    return 'just now';
                                }
                            }),
                        Forms\Components\Placeholder::make('updated_at')
                            ->label('Last modified at')
                            ->content(function (\Illuminate\Database\Eloquent\Model $record): String {
                                $category = Product::find($record->id);
                                $now = \Carbon\Carbon::now();

                                $diff = $category->updated_at->diff($now);
                                if ($diff->y > 0) {
                                    return $diff->y . ' years ago';
                                } elseif ($diff->m > 0) {
                                    if ($diff->m == 1) {
                                        return '1 month ago';
                                    } else {
                                        return $diff->m . ' months ago';
                                    }
                                } elseif ($diff->d >= 7) {
                                    $weeks = floor($diff->d / 7);
                                    if ($weeks == 1) {
                                        return 'a week ago';
                                    } else {
                                        return $weeks . ' weeks ago';
                                    }
                                } elseif ($diff->d > 0) {
                                    if ($diff->d == 1) {
                                        return 'yesterday';
                                    } else {
                                        return $diff->d . ' days ago';
                                    }
                                } elseif ($diff->h > 0) {
                                    if ($diff->h == 1) {
                                        return '1 hour ago';
                                    } else {
                                        return $diff->h . ' hours ago';
                                    }
                                } elseif ($diff->i > 0) {
                                    if ($diff->i == 1) {
                                        return '1 minute ago';
                                    } else {
                                        return $diff->i . ' minutes ago';
                                    }
                                } elseif ($diff->s > 0) {
                                    if ($diff->s == 1) {
                                        return '1 second ago';
                                    } else {
                                        return $diff->s . ' seconds ago';
                                    }
                                } else {
                                    return 'just now';
                                }
                            }),
                    ])->hiddenOn('create'),
                ])->columnSpan([
                    'sm' => 3,
                    'md' => 3,
                    'lg' => 1
                ]),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('brand.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->description(fn($record) => $record->slug),
                Tables\Columns\TextColumn::make('price')
                    ->money('PHP')
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock_quantity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable(),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Active')
                    ->onIcon('heroicon-m-bolt')
                    ->offIcon('heroicon-m-bolt-slash')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
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
                Tables\Filters\TrashedFilter::make(),
                Tables\Filters\SelectFilter::make('is_active')
                    ->label('Status')
                    ->options([
                        '1' => 'Active',
                        '0' => 'Inactive',
                    ]),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()->color('info'),
                    Tables\Actions\EditAction::make()->color('primary'),
                    Tables\Actions\DeleteAction::make()->label('Archive'),
                    Tables\Actions\RestoreAction::make(),
                    Tables\Actions\ForceDeleteAction::make()->label('Permanent Delete'),
                ])
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->tooltip('Actions')

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
            PromotionRelationManager::class,
            ReviewsRelationManager::class,
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
