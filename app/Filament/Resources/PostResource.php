<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Content Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required()
                    ->searchable(),
                Forms\Components\Textarea::make('content')
                    ->required()
                    ->maxLength(5000)
                    ->columnSpanFull(),
                Forms\Components\TagsInput::make('platforms')
                    ->suggestions(['facebook', 'instagram', 'twitter', 'tiktok', 'youtube', 'pinterest'])
                    ->required(),
                Forms\Components\Select::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'scheduled' => 'Scheduled',
                        'published' => 'Published',
                    ])
                    ->required()
                    ->default('draft'),
                Forms\Components\DateTimePicker::make('scheduled_at')
                    ->label('Schedule For')
                    ->nullable(),
                Forms\Components\Toggle::make('is_ai_generated')
                    ->label('AI Generated')
                    ->default(false),
                Forms\Components\TextInput::make('likes_count')
                    ->numeric()
                    ->default(0)
                    ->disabled(),
                Forms\Components\TextInput::make('comments_count')
                    ->numeric()
                    ->default(0)
                    ->disabled(),
                Forms\Components\TextInput::make('reposts_count')
                    ->numeric()
                    ->default(0)
                    ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('content')
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'secondary' => 'draft',
                        'warning' => 'scheduled',
                        'success' => 'published',
                    ]),
                Tables\Columns\IconColumn::make('is_ai_generated')
                    ->boolean()
                    ->label('AI'),
                Tables\Columns\TextColumn::make('likes_count')
                    ->sortable()
                    ->label('Likes'),
                Tables\Columns\TextColumn::make('comments_count')
                    ->sortable()
                    ->label('Comments'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'scheduled' => 'Scheduled',
                        'published' => 'Published',
                    ]),
                Tables\Filters\TernaryFilter::make('is_ai_generated')
                    ->label('AI Generated'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
