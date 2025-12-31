<?php

namespace App\Filament\Resources;

use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationGroup = 'User Management';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->label('Nama Lengkap'),
                
                TextInput::make('email')
                    ->email()
                    ->required(),
                
                TextInput::make('password')
                    ->password()
                    // Password hanya wajib saat buat baru (create), tidak saat edit
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->dehydrated(fn (?string $state) => filled($state)) // Jangan update jika kosong
                    ->label('Kata Sandi'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
                ->columns([
                    TextColumn::make('name')
                        ->sortable() // Bisa diurutkan A-Z
                        ->searchable(), // Bisa dicari
                    
                    TextColumn::make('email')
                        ->icon('heroicon-m-envelope') // Tambah ikon (opsional)
                        ->searchable(),
                        
                    TextColumn::make('created_at')
                        ->dateTime('d M Y') // Format tanggal: 30 Dec 2025
                        ->label('Tanggal Daftar')
                        ->sortable(),
                ])
                ->filters([
                    // Kita kosongkan dulu filternya
                ])
                ->actions([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(), // Tombol hapus
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
