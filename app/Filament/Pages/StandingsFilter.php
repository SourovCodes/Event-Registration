<?php

namespace App\Filament\Pages;

use App\Filament\Exports\RegistrationExporter;
use App\Filament\Resources\RegistrationResource;
use App\Models\Contest;
use App\Models\Registration;
use Filament\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Rmsramos\Activitylog\Actions\ActivityLogTimelineTableAction;
use Symfony\Component\DomCrawler\Crawler;

class StandingsFilter extends Page implements HasForms, HasTable
{
    use InteractsWithTable, InteractsWithForms;

    public ?array $data = [];
    public ?array $standingsData = [];
    public array $filteredIDs = [];
    /**
     * Map of student_id => rank for O(1) lookup in table column.
     */
    protected array $standingsRankMap = [];

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'filament.pages.standings-filter';


    public static function canAccess(): bool
    {
        return auth()->user()?->hasPermissionTo('page_StandingsFilter') ?? false;
    }

    public function mount(): void
    {
        $this->form->fill();
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('process')
                ->label('Process')
                ->submit('process'),
        ];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->columns(['sm' => 2])
                    ->schema([
                        TextInput::make('standings_url')
                            ->default('https://toph.co/c/diu-take-off-fall-24-preliminary-b-slot/standings')
                            ->placeholder('Standings URL')
                            ->url()
                            ->required(),
                        Select::make('contest_id')
                            ->label('Contest')
                            ->options(fn () => Contest::pluck('name', 'id'))
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn () => $this->resetContestContext()),
                        TextInput::make('rank_from')
                            ->numeric()
                            ->minValue(1),
                        TextInput::make('rank_to')
                            ->numeric()
                            ->minValue(1),
                        Select::make('genders')
                            ->label('Genders')
                            ->options(function (callable $get) {
                                $contestId = $get('contest_id');
                                if ($contestId) {
                                    return $this->getGenderOptions($contestId);
                                }
                                return [];
                            })
                            ->multiple()
                            ->hidden(fn (callable $get) => !$get('contest_id')),
                        // Toggle::make('female_only')->label('Female Only'),
                    ]),
            ])
            ->statePath('data');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getFilteredRegistrations())
            //            ->paginated([10, 25, 50])
            ->recordUrl(
                fn(Model $record): string => route('filament.admin.resources.registrations.edit', $record),
            )
            ->columns([
                TextColumn::make('rank')
                    ->label('Rank')
                    ->getStateUsing(fn ($record) => $this->standingsRankMap[$record->student_id] ?? 'N/A'),
                TextColumn::make('name')->toggleable()->searchable(),
                TextColumn::make('student_id')->label('Student ID')->toggleable()->searchable(),
                TextColumn::make('section')->toggleable(),
                TextColumn::make('department')->toggleable(),
            ])
            ->headerActions([
                ExportAction::make()
                    ->fileDisk('export-file')
                    ->exporter(RegistrationExporter::class),
            ])
            ->actions([
                ActivityLogTimelineTableAction::make('Activities'),
            ]) // Add if needed
            ->bulkActions([
                BulkActionGroup::make([
                    ExportBulkAction::make()
                        ->label("export Data")
                        ->fileDisk('export-file')
                        ->exporter(RegistrationExporter::class),
                ]),
            ]); // Add if needed
    }

    private function getFilteredRegistrations()
    {
        return Registration::query()
            ->where('contest_id', $this->data['contest_id'] ?? 0)
            ->when(!empty($this->filteredIDs), function ($query) {
                return $query
                    ->whereIn('student_id', $this->filteredIDs)
                    ->orderByRaw('FIELD(student_id, ' . implode(',', array_map(fn ($id) => "'" . addslashes($id) . "'", $this->filteredIDs)) . ')');
            })
            ->when(empty($this->filteredIDs), fn ($query) => $query->whereIn('student_id', []));
    }


    public function process(): void
    {
        try {
            $this->form->getState();

            $contestId = $this->data['contest_id'] ?? null;
            $standingsUrl = $this->data['standings_url'] ?? null;
            if (!$contestId || !$standingsUrl) {
                Notification::make()->title('Missing data')->body('Contest & URL are required.')->danger()->send();
                return;
            }

            $registrations = Registration::where('contest_id', $contestId)->get();
            $registrationsById = $registrations->keyBy('student_id');

            $rankList = $this->getCachedRankList($standingsUrl);

            $gendersFilter = $this->data['genders'] ?? [];
            $from = isset($this->data['rank_from']) && $this->data['rank_from'] !== '' ? (int)$this->data['rank_from'] : null;
            $to = isset($this->data['rank_to']) && $this->data['rank_to'] !== '' ? (int)$this->data['rank_to'] : null;

            $this->standingsData = [];
            foreach ($rankList as $rank) {
                if ($this->shouldSkipRank($rank, $from, $to)) {
                    continue;
                }

                $registration = $registrationsById->get($rank['id']);
                if (!$registration) {
                    continue; // Not registered
                }

                if (!empty($gendersFilter) && !in_array(($registration->gender ?? 'N/A'), $gendersFilter, true)) {
                    continue;
                }

                $this->standingsData[] = [
                    'rank' => (int)$rank['rank'],
                    'name' => $rank['name'],
                    'student_id' => $registration->student_id,
                    'gender' => $registration->gender,
                ];
            }

            // Sort by rank once.
            usort($this->standingsData, fn ($a, $b) => $a['rank'] <=> $b['rank']);

            $this->filteredIDs = array_column($this->standingsData, 'student_id');
            $this->standingsRankMap = array_column($this->standingsData, 'rank', 'student_id');

            $this->resetTable();

            Notification::make()
                ->title('Processing Success')
                ->body('Found ' . count($this->filteredIDs) . ' IDs.')
                ->success()
                ->send();
        } catch (\Throwable $exception) {
            Log::error('Standings processing failed', [
                'message' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);
            Notification::make()
                ->title('Processing Failed')
                ->body('Could not process standings. Check logs.')
                ->danger()
                ->send();
        }
    }

    private function shouldSkipRank(array $rank, ?int $from = null, ?int $to = null): bool
    {
        $r = (int)($rank['rank'] ?? 0);
        if ($from !== null && $r < $from) return true;
        if ($to !== null && $r > $to) return true;
        return false;
    }

    /**
     * Fetch and cache rank list.
     */
    private function getCachedRankList(string $url): array
    {
        $cacheKey = 'standings_raw_' . md5($url);
        return cache()->remember($cacheKey, 3600, fn () => $this->fetchRankList($url));
    }

    private function fetchRankList(string $url): array
    {
        $client = new Client([
            'headers' => [
                'User-Agent' => 'StandingsFetcher/1.0 (+laravel)'
            ],
            'timeout' => 10,
        ]);
        $rankList = [];
        $start = 0;

        while (true) {
            try {
                $pageUrl = $url . '?start=' . $start;
                $htmlContent = cache()->remember($pageUrl, 3600, fn () => $client->get($pageUrl)->getBody()->getContents());
            } catch (\Throwable $e) {
                Log::warning('Failed fetching standings page', [
                    'url' => $url,
                    'start' => $start,
                    'error' => $e->getMessage(),
                ]);
                break; // Stop paging on failure
            }

            $crawler = new Crawler($htmlContent);
            $rows = $crawler->filter('table tbody tr');

            if ($rows->count() === 0) {
                break;
            }

            $start += $rows->count();

            $rows->each(function (Crawler $row) use (&$rankList) {
                try {
                    $rank = trim($row->filter('td:nth-child(1)')->text());
                    $secondTd = $row->filter('td:nth-child(2)');
                    $nameNode = $secondTd->getNode(0)->childNodes[0] ?? null;
                    $name = $nameNode ? trim($nameNode->nodeValue) : '';
                    $idText = $secondTd->filter('.adjunct')->count() ? $secondTd->filter('.adjunct')->text() : '';
                    $details = array_map('trim', array_filter(explode(',', $idText)));

                    $rankList[] = [
                        'rank' => $rank,
                        'name' => $name,
                        'id' => $details[0] ?? '',
                        'section' => $details[1] ?? '',
                        'department' => $details[2] ?? '',
                    ];
                } catch (\Throwable $e) {
                    Log::notice('Skipping malformed standings row', ['error' => $e->getMessage()]);
                }
            });
        }

        return $rankList;
    }

    /**
     * Build gender options for a contest, cached.
     */
    private function getGenderOptions(int $contestId): array
    {
        return cache()->remember(
            'genders_of_contest_' . $contestId,
            7200,
            fn () => Registration::where('contest_id', $contestId)
                ->pluck('gender')
                ->unique()
                ->filter()
                ->values()
                ->mapWithKeys(fn ($gender) => [$gender => ucfirst($gender)])
                ->toArray()
        );
    }

    /**
     * Reset context when contest changes.
     */
    private function resetContestContext(): void
    {
        $this->standingsData = [];
        $this->filteredIDs = [];
        $this->standingsRankMap = [];
        $this->resetTable();
    }
}
