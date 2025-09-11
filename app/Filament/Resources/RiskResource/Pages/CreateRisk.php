<?php

namespace App\Filament\Resources\RiskResource\Pages;

use App\Filament\Resources\RiskResource;
use App\Models\Risk;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\Wizard\Step;
use Filament\Resources\Pages\CreateRecord;

class CreateRisk extends CreateRecord
{
    use CreateRecord\Concerns\HasWizard;

    protected static string $resource = RiskResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {

        $data['inherent_risk'] = $data['inherent_likelihood'] * $data['inherent_impact'];
        $data['residual_risk'] = $data['residual_likelihood'] * $data['residual_impact'];

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Created new Risk';
    }

    public function getSteps(): array
    {
        return [
            Step::make('Risk Identification')
                ->columns(4)
                ->schema([
                    TextInput::make('s_n')
                        ->label('S/N')
                        ->prefix('S/N')
                        ->numeric()
                        // ->disabled()
                        ->dehydrated(true)
                        ->minValue(0)
                        ->integer()
                        ->default(Risk::next())
                        ->helperText('Unique code for this risk')
                        ->unique('risks', 'code')
                        ->required(),
                    Select::make('asset_category')
                        ->label('Asset Category')
                        ->options([
                            'Platform' => 'Platform',
                            'Ordinateur' => 'Ordinateur',
                            'Server' => 'Server',
                        ])
                        ->placeholder('Select a category')
                        ->searchable()
                        ->preload()
                        ->helperText('Select the category/type for this risk')
                        ->nullable()
                        ->columnSpan(3),
                    TextInput::make('asset')
                        ->columnSpan(4)
                        ->maxLength(255)
                        ->label('Asset')
                        ->helperText('Give the risk a short but descriptive name')
                        ->required(),
                    TextInput::make('threat')
                        ->columnSpan(2)
                        ->maxLength(255)
                        ->label('Threat')
                        ->helperText('Give a short name to the threat')
                        ->required(),
                    TextInput::make('vulnerability')
                        ->columnSpan(2)
                        ->maxLength(255)
                        ->label('Vulnerability')
                        ->helperText('Give a short name to the vulnerability')
                        ->required(),
                    Textarea::make('risk')
                        ->label('Risk (Description)')
                        ->columnSpan(4)
                        ->maxLength(4096)
                        ->helperText('Provide a description of the risk that will help others understand it'),
                    // RiskResource::taxonomySelect('Department')
                    //     ->nullable()
                    //     ->columnSpan(2)
                    //     ->helperText('Select the department responsible for this risk'),
                    // RiskResource::taxonomySelect('Scope')
                    //     ->nullable()
                    //     ->columnSpan(2)
                    //     ->helperText('Select the scope this risk applies to'),
                    TextInput::make('existing_controls')
                        ->columnSpan(2)
                        ->maxLength(255)
                        ->label('Existing Controls')
                        ->helperText('Give a short name to the existing controls')
                        ->required(),
                    TextInput::make('status_of_existing_controls')
                        ->columnSpan(2)
                        ->maxLength(255)
                        ->label('Status of Existing Controls')
                        ->helperText('Give a short name to the status of the existing controls')
                        ->required(),
                ]),

            Step::make('Risk Analysis')
                ->columns(2)
                ->schema([

                    // Placeholder::make('InherentRisk')
                    //     ->hiddenLabel(true)
                    //     ->columnSpan(4)
                    //     ->content('Inherent risk is the risk that exists before you apply any controls. 
                    //     Use your best judgement to answer the following questions.'),

                    Section::make('LIKELIHOOD')
                        ->columnSpan(1)
                        ->columns(1)
                        ->extraAttributes(['class' => 'flex flex-col items-center text-center'])
                        ->schema([
                            Placeholder::make('InherentLikelihood')
                                ->hiddenLabel(true)
                                ->columnSpan(4)
                                ->content(fn () => new \Illuminate\Support\HtmlString('<strong>Likelihood</strong> is the probability or chance that a particular risk event will occur within a specified time frame or under certain conditions. It reflects how often or how likely it is that the event might happen, based on evidence, trends, and expert judgment.')),
                            Placeholder::make('InherentImpactTable')
                                ->columnSpan(4)
                                ->view('components.misc.inherent_likelihood'),
                            ToggleButtons::make('inherent_likelihood')
                                ->columnSpan(4)
                                ->label('Likelihood Score')
                                ->options([
                                    1 => new \Illuminate\Support\HtmlString('Very Low<br><span class="text-xs font-semibold block mt-3">1</span>'),
                                    2 => new \Illuminate\Support\HtmlString('Low<br><span class="text-xs font-semibold block mt-3">2</span>'),
                                    3 => new \Illuminate\Support\HtmlString('Moderate<br><span class="text-xs font-semibold block mt-3">3</span>'),
                                    4 => new \Illuminate\Support\HtmlString('High<br><span class="text-xs font-semibold block mt-3">4</span>'),
                                    5 => new \Illuminate\Support\HtmlString('Very High<br><span class="text-xs font-semibold block mt-3">5</span>'),
                                ])
                                ->default('3')
                                ->colors(
                                    [
                                        1 => 'success',  // Very Low
                                        2 => 'info',  // Low
                                        3 => 'primary',  // Moderate
                                        4 => 'warning',  // High
                                        5 => 'danger',  // Very High
                                    ]
                                )
                                ->grouped()
                                ->required(),
                        ]),

                    Section::make('IMPACT')
                        ->columns(1)
                        ->columnSpan(1)
                        ->extraAttributes(['class' => 'flex flex-col items-center text-center'])
                        ->schema([
                            Placeholder::make('InherentImpact')
                                ->hiddenLabel(true)
                                ->columnSpan(4)
                                ->content(fn () => new \Illuminate\Support\HtmlString('<strong>Impact</strong> refers to the potential severity or magnitude of the consequences if a risk event occurs. It measures the extent of damage or disruption that could result, considering factors such as financial loss, operational downtime, reputational harm, safety issues, or legal implications.')),
                            Placeholder::make('InherentImpactTable')
                                ->columnSpan(4)
                                ->view('components.misc.inherent_impact'),
                            ToggleButtons::make('inherent_impact')
                                ->columnSpan(4)
                                ->label('Impact Score')
                                ->options([
                                    1 => new \Illuminate\Support\HtmlString('Very Low<br><span class="text-xs font-semibold block mt-3">1</span>'),
                                    2 => new \Illuminate\Support\HtmlString('Low<br><span class="text-xs font-semibold block mt-3">2</span>'),
                                    3 => new \Illuminate\Support\HtmlString('Moderate<br><span class="text-xs font-semibold block mt-3">3</span>'),
                                    4 => new \Illuminate\Support\HtmlString('High<br><span class="text-xs font-semibold block mt-3">4</span>'),
                                    5 => new \Illuminate\Support\HtmlString('Very High<br><span class="text-xs font-semibold block mt-3">5</span>'),
                                ])
                                ->default('3')
                                ->colors(
                                    [
                                        1 => 'success',  // Very Low
                                        2 => 'info',  // Low
                                        3 => 'primary',  // Moderate
                                        4 => 'warning',  // High
                                        5 => 'danger',  // Very High
                                    ]
                                )
                                ->grouped()
                                ->required(),
                        ]),

                ]),

            Step::make('Risk Evaluation')
                ->columns(4)
                ->schema([
                    Section::make('Inherent Risk')
                        ->columnSpan(4)
                        ->schema([
                            Placeholder::make('RelatedRisksList')
    ->columnSpan(4)
    ->content(fn () => new \Illuminate\Support\HtmlString(
        (function () {
            try {
                return app(\App\Filament\Resources\RiskResource\Widgets\RiskMatrix::class)->render();
            } catch (\Throwable $e) {
                // show message + stack trace for debugging (remove this block when fixed)
                $msg = '<div class="p-4 bg-red-100 text-red-800"><strong>Exception:</strong> '
                    .htmlspecialchars($e->getMessage()).'</div>';
                $trace = '<pre style="max-height:300px;overflow:auto;background:#111;color:#fff;padding:8px;">'
                    .htmlspecialchars($e->getTraceAsString()).'</pre>';
                return $msg . $trace;
            }
        })()
    )),
                    ]),
                ]),
                
            Step::make('Residual Risk')
                ->columns(2)
                ->schema([

                    Section::make('How likely is this risk to occur after your current safeguards?')
                        ->columns(1)
                        ->columnSpan(1)
                        ->schema([

                            Placeholder::make('ResidualRisk')
                                ->hiddenLabel(true)
                                ->columnSpan(4)
                                ->content('Residual likelihood is the likelihood of the risk occurring if no 
                                action is taken. Use your best judgement to determine the likelihood of this risk 
                                occurring AFTER you applied controls.'),
                            Placeholder::make('ResidualTable')
                                ->columnSpan(4)
                                ->view('components.misc.inherent_likelihood'),
                            ToggleButtons::make('residual_likelihood')
                                ->label('Residual Likelihood Score')
                                ->helperText('How likely is it that this risk will impact us if we do nothing?')
                                ->options([
                                    1 => 'Very Low',
                                    2 => 'Low',
                                    3 => 'Moderate',
                                    4 => 'High',
                                    5 => 'Very High',
                                ])
                                ->default('3')
                                ->colors(
                                    [
                                        1 => 'success',  // Very Low
                                        2 => 'info',  // Low
                                        3 => 'primary',  // Moderate
                                        4 => 'warning',  // High
                                        5 => 'danger',  // Very High
                                    ]
                                )
                                ->grouped()
                                ->required(),
                        ]),

                    Section::make('If this risk does occur, how severe will the impact be with your current safeguards?')
                        ->columnSpan(1)
                        ->columns(1)
                        ->schema([

                            Placeholder::make('ResidualImpact')
                                ->hiddenLabel(true)
                                ->columnSpan(4)
                                ->content('Residual impact is the damage that will occur if the risk does occur.
                                Use your best judgement to determine the impact of this risk occurring AFTER you applied 
                                controls.'),
                            Placeholder::make('ResidualImpactTable')
                                ->columnSpan(4)
                                ->view('components.misc.inherent_impact'),
                            ToggleButtons::make('residual_impact')
                                ->label('Residual Impact Score')
                                ->helperText('If this risk does occur, how severe will the impact be?')
                                ->options([
                                    1 => 'Very Low',
                                    2 => 'Low',
                                    3 => 'Moderate',
                                    4 => 'High',
                                    5 => 'Very High',
                                ])
                                ->default('3')
                                ->colors(
                                    [
                                        1 => 'success',  // Very Low
                                        2 => 'info',  // Low
                                        3 => 'primary',  // Moderate
                                        4 => 'warning',  // High
                                        5 => 'danger',  // Very High
                                    ]
                                )
                                ->grouped()
                                ->required(),
                        ]),

                    Section::make('Related Implementations')
                        ->columnSpan(2)
                        ->schema([
                            Placeholder::make('implementations')
                                ->hiddenLabel(true)
                                ->columnSpan(4)
                                ->content('If you already have implementatons in OpenGRC
                                that you use to control this risk, you can link them here. You
                                can relate these later if you need to.'),
                            Select::make('implementations')
                                ->label('Related Implementations')
                                ->helperText('What are we doing to mitigate this risk?')
                                ->relationship('implementations', 'title')
                                ->searchable(['title', 'code'])
                                ->multiple(),

                        ]),

                ]),

        ];
    }
}
