<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContestResource\Pages;
use App\Filament\Resources\ContestResource\RelationManagers\RegistrationsRelationManager;
use App\Models\Contest;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class ContestResource extends Resource
{
    protected static ?string $model = Contest::class;

    protected static ?string $slug = 'contests';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required(),
//                    ->afterStateUpdated(fn($state, callable $set) => $set('slug', Str::slug($state))),

                TextInput::make('slug')
                    ->required()
                    ->unique(Contest::class, 'slug', fn($record) => $record),
                Toggle::make('public'),

                SpatieMediaLibraryFileUpload::make('Banner Image')
                    ->hint("use https://imagecompressor.com/ compressor if larger file size.")
                    ->disk('contest-banner-images')
                    ->collection('contest-banner-images')
                    ->preserveFilenames()
                    ->responsiveImages()
                    ->maxSize(1024 * 3)
                    ->image()
                    ->imageEditor()
                    ->visibility('public')
                    ->required(),
                SpatieMediaLibraryFileUpload::make('Tshirt Sized')
                    ->disk('tshirt-sizes')
                    ->collection('tshirt-sizes')
                    ->preserveFilenames()
                    ->responsiveImages()
                    ->maxSize(1024 * 3)
                    ->image()
                    ->imageEditor()
                    ->visibility('public')
                    ->required(),
                
                SpatieMediaLibraryFileUpload::make('Credentials CSV')
                    ->disk('local')
                    ->collection('credentials-csv')
                    ->acceptedFileTypes(['text/csv', 'text/plain', 'application/csv'])
                    ->maxSize(5120)
                    ->downloadable()
                    ->helperText('Upload a CSV file with student credentials (Name, Clan_1, Clan_2, Clan_3, Room No, PC No, Username, Password)')
                    ->visibility('private'),
                
                TextInput::make('semester')
                    ->required(),

                Textarea::make('description'),

                TextInput::make('registration_fee')
                    ->required()
                    ->integer(),

                DateTimePicker::make('registration_deadline')
                    ->timezone('Asia/Dhaka')
                    ->required()
                    ->seconds(false),
                DateTimePicker::make('registration_start_time')
                    ->timezone('Asia/Dhaka')
                    ->seconds(false),

                Textarea::make('countdown_text'),

                DateTimePicker::make('countdown_time')
                    ->timezone('Asia/Dhaka')
                    ->required()
                    ->seconds(false),

                TextInput::make('registration_limit')
                    ->integer(),


                Section::make('Contest Dates')
                    ->description("Option value for registration forms")
                    ->schema([
                        Repeater::make('dates')
                            ->grid(2)
                            ->columns(2)
                            ->schema([
                                TextInput::make('round_name')
                                    ->placeholder('Preliminary A Slot')
                                    ->required(),
                                DateTimePicker::make('round_date')
                                    ->seconds(false)
                                    ->required(),
                            ]),
                    ]),

                Section::make('Form Options')
                    ->columns(2)
                    ->description("Option value for registration forms")
                    ->schema([
                        TextInput::make('student_id_rules')
                            ->placeholder('regex:/^[0-9-]+$/')
                            ->columnSpan(2)
                            ->startsWith('regex:'),
                        TextInput::make('student_id_rules_guide')
                            ->columnSpan(2)
                            ->placeholder('Student Id Must...'),

                        Repeater::make('pickup_points')
                            ->columnSpan(2)
                            ->grid(3)
                            ->schema([
                                TextInput::make('name')->required(),
                            ]),
                        Repeater::make('departments')
                            ->columnSpan(2)
                            ->grid(3)
                            ->schema([
                                TextInput::make('name')->required(),
                            ]),
                        Repeater::make('manual_payment_methods')
                            ->required()
                            ->columnSpan(2)
                            ->grid(2)
                            ->schema([
                                TextInput::make('name')->required(),
                                RichEditor::make('info')->required(),
                            ]),
                        Repeater::make('sections')
                            ->columnSpan(2)
                            ->grid(3)
                            ->schema([
                                TextInput::make('name')->required(),
                            ]),

                        Repeater::make('lab_teacher_names')
                            ->columnSpan(2)
                            ->grid(2)
                            ->columns(2)
                            ->schema([
                                TextInput::make('initial')->required(),
                                TextInput::make('full_name')
                                    ->required(),
                            ]),

                    ])
                ,
                Placeholder::make('created_at')
                    ->label('Created Date')
                    ->content(fn(?Contest $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                Placeholder::make('updated_at')
                    ->label('Last Modified Date')
                    ->content(fn(?Contest $record): string => $record?->updated_at?->diffForHumans() ?? '-'),


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('slug')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('semester')
                    ->searchable()
                    ->sortable(),


                TextColumn::make('registration_fee'),

                TextColumn::make('registration_deadline')
                    ->date(),


            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
                RestoreAction::make(),
                ForceDeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContests::route('/'),
            'create' => Pages\CreateContest::route('/create'),
            'edit' => Pages\EditContest::route('/{record}/edit'),
            'registrations' => Pages\ManageContestRegistrations::route('/{record}/registrations'),

        ];
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\EditContest::class,
            Pages\ManageContestRegistrations::class,
        ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

//    public static function getRelations(): array
//    {
//        return [
//            RegistrationsRelationManager::class,
//        ];
//    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'slug'];
    }
}
