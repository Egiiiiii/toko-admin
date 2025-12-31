<?php

namespace App\Filament\Resources;

use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Models\Product;
use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
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
    protected static ?string $navigationGroup = 'Transactions';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Bagian Kiri: Data Order Utama
                Section::make('Order Details')->schema([
                    Select::make('customer_id')
                        ->relationship('customer', 'name')
                        ->searchable()
                        ->required(),
                    
                    TextInput::make('number')
                        ->default('ORD-' . random_int(100000, 999999)) // Generate nomor otomatis
                        ->disabled() // Tidak bisa diedit manual
                        ->dehydrated() // Tetap dikirim ke database
                        ->required(),

                    Select::make('status')
                        ->options([
                            'pending' => 'Pending',
                            'processing' => 'Processing',
                            'completed' => 'Completed',
                            'cancelled' => 'Cancelled',
                        ])
                        ->default('pending')
                        ->required(),
                        
                    TextInput::make('total_price')
                        ->numeric()
                        ->readOnly() // Hanya baca, dihitung otomatis nanti
                        ->prefix('Rp'),
                ])->columns(2),

                // Bagian Bawah: Daftar Barang (Repeater)
                Section::make('Order Items')->schema([
                    Repeater::make('items') // Relasi ke hasMany OrderItems
                        ->relationship()
                        ->schema([
                            Select::make('product_id')
                                ->relationship('product', 'name')
                                ->searchable()
                                ->required()
                                ->reactive() // Agar bisa memicu perubahan harga
                                ->afterStateUpdated(function ($state, Set $set) {
                                    // Script Otomatis Ambil Harga Produk
                                    $product = Product::find($state);
                                    if ($product) {
                                        $set('unit_price', $product->price);
                                    }
                                })
                                ->columnSpan(4), // Lebar kolom

                            TextInput::make('quantity')
                                ->numeric()
                                ->default(1)
                                ->minValue(1)
                                ->required()
                                ->columnSpan(2),

                            TextInput::make('unit_price')
                                ->numeric()
                                ->disabled() // Harga diambil dari produk, jangan edit manual
                                ->dehydrated()
                                ->columnSpan(3),
                        ])
                        ->columns(9) // Total grid kolom
                ]),
            ]);

    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('customer_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_price')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'pending' => 'warning',   // Kuning
                    'processing' => 'info',   // Biru
                    'completed' => 'success', // Hijau
                    'cancelled' => 'danger',  // Merah
                })
                ->icon(fn (string $state): string => match ($state) {
                    'pending' => 'heroicon-m-clock',
                    'completed' => 'heroicon-m-check-circle',
                    'cancelled' => 'heroicon-m-x-circle',
                    default => 'heroicon-m-arrow-path',
                })
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
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
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
