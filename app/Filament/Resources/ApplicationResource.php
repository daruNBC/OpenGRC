<?php

namespace App\Filament\Resources;

use App\Enums\ApplicationStatus;
use App\Enums\ApplicationType;
use App\Filament\Resources\ApplicationResource\Pages;
use App\Models\Application;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class ApplicationResource extends Resource
{
    protected static ?string $model = Application::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

    public static function getNavigationLabel(): string
    {
        return __('Assets (CIIs)');
    }

    public static function getNavigationGroup(): string
    {
        return __('Entities');
    }

    public static function getModelLabel(): string
    {
        return __('Asset (CII)');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Assets (CIIs)');
    }

    protected static function mutateFormDataBeforeCreate(array $data): array
    {
        $user = Auth::user();

        // Check if a user is authenticated and has a 'code' property
        if ($user && property_exists($user, 'code')) {
            // Use the correct database column 'user_code'
            // Use the user's 'code'
            $data['user_code'] = $user->code;
        }

        return $data;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('code')
                    ->label(__('Code'))
                    ->disabled() 
                    ->dehydrated(false) 
                    ->hiddenOn('create'), 
                Forms\Components\TextInput::make('name')
                    ->label(__('Name'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('owner_name')
                    ->label(__('Owner'))
                    ->required(),
                Forms\Components\Select::make('type')
                    ->label(__('Type'))
                    ->enum(ApplicationType::class)
                    ->options(collect(ApplicationType::cases())->mapWithKeys(fn ($case) => [$case->value => $case->getLabel()]))
                    ->searchable()
                    ->required(),
                Forms\Components\Textarea::make('description')
                    ->label(__('Description'))
                    ->maxLength(65535),

                Forms\Components\Section::make('Details')
                    ->schema([
                        Forms\Components\Textarea::make('location')
                            ->label(__('Location'))
                            ->helperText('Physical address including facility name, region, city.'),
                        Forms\Components\Textarea::make('dependencies')
                            ->label(__('Dependencies (Upstream/Downstream)'))
                            ->helperText('List key systems, technologies, networks and services this asset depends on.'),
                    ])->columns(1),

                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'Active' => 'Active (In use)',
                        'Candidate' => 'Candidate (Under review)',
                        'Retired' => 'Retired (No longer in use)',
                    ])
                    ->placeholder('Select a status')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\TextInput::make('url')
                    ->label(__('URL'))
                    ->maxLength(512),
                Forms\Components\Textarea::make('notes')
                    ->label(__('Notes'))
                    ->maxLength(65535),
                Forms\Components\TextInput::make('custodian_name')
                    ->label(__('Custodian'))
                    ->helperText('Organization responsible for ownership, operation, or maintenance.')
                    ->required(),
                Forms\Components\FileUpload::make('logo')
                    ->label(__('Logo'))
                    ->disk(config('filesystems.default'))
                    ->directory('application-logos')
                    ->storeFileNamesIn('logo')
                    ->visibility('private')
                    ->maxSize(1024) // 1MB
                    ->deletable()
                    ->deleteUploadedFileUsing(function ($state) {
                        if ($state) {
                            \Illuminate\Support\Facades\Storage::disk(config('filesystems.default'))->delete($state);
                        }
                    }),
                Forms\Components\Section::make('Geolocation')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('latitude')
                        ->numeric()
                        ->rules(['regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'])
                        ->helperText('Example: 40.7128'),
                    Forms\Components\TextInput::make('longitude')
                        ->numeric()
                        ->rules(['regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/'])
                        ->helperText('Example: -74.0060'),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')->label(__('Code'))->searchable()->sortable(),
                Tables\Columns\TextColumn::make('name')->label(__('Name'))->searchable(),
                Tables\Columns\TextColumn::make('owner_name')->label(__('Owner'))->searchable(),
                Tables\Columns\TextColumn::make('type')->label(__('Type'))->badge()->color(fn ($record) => $record->type->getColor()),
                Tables\Columns\TextColumn::make('custodian_name')->label(__('Custodian')),
                Tables\Columns\TextColumn::make('status')->label(__('Status'))->badge(),
                // Tables\Columns\TextColumn::make('status')->label(__('Status'))->badge()->color(fn ($record) => $record->status->getColor()),
                Tables\Columns\TextColumn::make('url')->label(__('URL'))->url(fn ($record) => $record->url, true),
                Tables\Columns\TextColumn::make('created_at')->label(__('Created'))->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('updated_at')->label(__('Updated'))->dateTime()->sortable(),
            ])
            ->filters([

            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
            Tables\Actions\Action::make('view_map')
                ->label('See assets on a map')
                ->url(fn (): string => static::getUrl('map'))
                ->icon('heroicon-o-map'),
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
            'index' => Pages\ListApplications::route('/'),
            'create' => Pages\CreateApplication::route('/create'),
            'edit' => Pages\EditApplication::route('/{record}/edit'),
            'map' => Pages\AssetsMap::route('/map'),
        ];
    }
}
