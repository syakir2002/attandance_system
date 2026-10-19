<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class MalaysiaHolidayService
{
    /**
     * Base API URL.
     */
    protected string $baseUrl;

    /**
     * Default state code (e.g., TRG for Terengganu).
     */
    protected string $defaultState;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) config('services.malaysia_holiday.url', 'https://malaysia-holiday.dydxsoft.my/api/v1'), '/');
        $this->defaultState = (string) config('services.malaysia_holiday.default_state', 'TRG');
    }

    /**
     * Fetch holidays for a given year and state from cache or upstream API.
     *
     * @return array<int, array{name: string, date: string, day_name?: string, is_subject_to_change?: bool}>
     */
    public function getHolidays(int $year, ?string $state = null): array
    {
        $stateCode = strtoupper($state ?: $this->defaultState);
        $cacheKey = "malaysia_holidays_{$year}_{$stateCode}";

        return Cache::remember($cacheKey, now()->addDays(30), function () use ($year, $stateCode): array {
            try {
                $response = Http::timeout(8)
                    ->acceptJson()
                    ->get("{$this->baseUrl}/holidays", [
                        'year' => $year,
                        'state' => $stateCode,
                    ]);

                if ($response->successful()) {
                    $json = $response->json();
                    if (isset($json['data']) && is_array($json['data'])) {
                        return $json['data'];
                    }
                }

                Log::warning("Malaysia Holiday API response status: {$response->status()} for year {$year} state {$stateCode}");
            } catch (Throwable $e) {
                Log::error('Malaysia Holiday API error: '.$e->getMessage());
            }

            return [];
        });
    }

    /**
     * Fetch holidays within a given date range.
     *
     * @return array<int, array{name: string, date: string, day_name?: string, is_subject_to_change?: bool}>
     */
    public function getHolidaysBetween(Carbon $startDate, Carbon $endDate, ?string $state = null): array
    {
        $startYear = (int) $startDate->year;
        $endYear = (int) $endDate->year;
        $stateCode = $state ?: $this->defaultState;

        $allHolidays = [];

        for ($year = $startYear; $year <= $endYear; $year++) {
            $yearHolidays = $this->getHolidays($year, $stateCode);
            foreach ($yearHolidays as $holiday) {
                $allHolidays[] = $holiday;
            }
        }

        $startStr = $startDate->toDateString();
        $endStr = $endDate->toDateString();

        return array_values(array_filter($allHolidays, function (array $holiday) use ($startStr, $endStr): bool {
            $date = $holiday['date'] ?? '';

            return $date >= $startStr && $date <= $endStr;
        }));
    }

    /**
     * Transform holidays into FullCalendar event objects.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getAsCalendarEvents(Carbon $startDate, Carbon $endDate, ?string $state = null): array
    {
        $stateCode = strtoupper($state ?: $this->defaultState);
        $holidays = $this->getHolidaysBetween($startDate, $endDate, $stateCode);
        $stateName = $this->getStateName($stateCode);

        $events = [];

        foreach ($holidays as $holiday) {
            $dateStr = (string) $holiday['date'];
            $carbonDate = Carbon::parse($dateStr);
            $isSubjectToChange = (bool) ($holiday['is_subject_to_change'] ?? false);
            $holidayName = (string) $holiday['name'];

            $events[] = [
                'id' => 'holiday_'.$dateStr.'_'.substr(md5($holidayName), 0, 8),
                'title' => $holidayName,
                'start' => $dateStr,
                'end' => null,
                'allDay' => true,
                'className' => 'fc-custom-block-event fc-holiday-block',
                'extendedProps' => [
                    'source' => 'holiday',
                    'raw_title' => $holidayName,
                    'description' => "Cuti Kelepasan Am Rasmi bagi {$stateName}.\n".($isSubjectToChange ? '⚠️ Catatan: Tarikh tertakluk kepada perubahan rasmi.' : 'Pelepasan am yang digazetkan.'),
                    'status' => 1,
                    'is_public' => true,
                    'status_label' => "Cuti Umum ({$stateCode})",
                    'display_style' => 'block',
                    'is_block' => true,
                    'is_holiday' => true,
                    'state_code' => $stateCode,
                    'state_name' => $stateName,
                    'is_subject_to_change' => $isSubjectToChange,
                    'start_formatted' => $carbonDate->translatedFormat('d F Y (l)'),
                    'end_formatted' => '-',
                ],
            ];
        }

        return $events;
    }

    /**
     * Map state code to full state name.
     */
    public function getStateName(string $stateCode): string
    {
        $names = [
            'TRG' => 'Negeri Terengganu',
            'JHR' => 'Negeri Johor',
            'KDH' => 'Negeri Kedah',
            'KTN' => 'Negeri Kelantan',
            'MLK' => 'Negeri Melaka',
            'NSN' => 'Negeri Sembilan',
            'PHG' => 'Negeri Pahang',
            'PRK' => 'Negeri Perak',
            'PLS' => 'Negeri Perlis',
            'PNG' => 'Pulau Pinang',
            'SBH' => 'Negeri Sabah',
            'SWK' => 'Negeri Sarawak',
            'SGR' => 'Negeri Selangor',
            'KUL' => 'Wilayah Persekutuan Kuala Lumpur',
            'LBN' => 'Wilayah Persekutuan Labuan',
            'PJY' => 'Wilayah Persekutuan Putrajaya',
        ];

        return $names[$stateCode] ?? $stateCode;
    }
}
