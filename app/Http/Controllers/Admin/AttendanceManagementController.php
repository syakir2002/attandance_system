<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Elibyy\TCPDF\Facades\TCPDF;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class AttendanceManagementController extends Controller
{
    private const PDF_COLUMN_WIDTHS = [10, 52, 26, 26, 21, 34, 21];

    private const PDF_PAGE_CONTENT_BOTTOM = 285;

    /**
     * Display a listing of attendance records with filters.
     */
    public function index(Request $request): View
    {

        $staffId = $request->input('staff_id', '');
        $date = $request->input('date', '');
        $month = $request->input('month', '');
        $year = $request->input('year', '');
        $query = Attendance::with('user');

        if ($staffId !== '') {
            $query->where('staff_id', $staffId);
        }

        if ($date !== null && $date !== '') {
            $query->where('date', $date);
        } else {
            if ($year !== '' && $year !== null) {
                $query->whereYear('date', $year);
            }
            if ($month !== '' && $month !== null) {
                $query->whereMonth('date', $month);
            }
        }

        $records = $query->orderBy('date', 'desc')
            ->orderBy('check_in', 'desc')
            ->paginate(15)
            ->withQueryString();

        $staffList = User::where('role', 'staff')
            ->orderBy('name', 'asc')
            ->get();

        return view('admin.attendance.index', compact(
            'records',
            'staffList',
            'staffId',
            'date',
            'month',
            'year'
        ));
    }

    /**
     * Store manual attendance record for staff who forgot to clock in/out.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'staff_id' => ['required', 'exists:user,staff_id'],
            'date' => ['required', 'date_format:Y-m-d'],
            'check_in' => ['required', 'date_format:H:i:s'],
            'check_out' => ['nullable', 'date_format:H:i:s'],
            'keterangan_in' => ['nullable', 'string', 'max:255'],
            'keterangan_out' => ['nullable', 'string', 'max:255'],
            'keterangan_staff' => ['nullable', 'string', 'max:255'],
            'keterangan_admin' => ['nullable', 'string', 'max:255'],
        ], [
            'staff_id.required' => 'Sila pilih staf.',
            'date.required' => 'Sila pilih tarikh.',
            'check_in.required' => 'Sila masukkan masa check in.',
        ]);
       

        $staffId = $validated['staff_id'];
        $date = $validated['date'];
        $checkIn = $validated['check_in'];
        $checkOut = ! empty($validated['check_out']) ? $validated['check_out'] : null;

        // Generate Custom ID: {staff_id}-{YYYYMMDD}-{session_number}
        $customId = Attendance::generateCustomId($staffId, $date);

        // Calculate period
        $period = Attendance::calculatePeriod($checkIn, $checkOut);
        
        $keteranganIn = $validated['keterangan_in'] ?? $validated['keterangan_staff'] ?? null;
        $keteranganOut = $validated['keterangan_out'] ?? null;

        $isOverlapping = Attendance::where('staff_id',$staffId)
                                    ->where('date',$date)
                                    ->where(function($query) use ($checkIn, $checkOut){
                                        $query->where('check_in', '<', $checkIn)
                                              ->where('check_out', '>', $checkOut);
                                    })
                                    ->exists();
        
        $hasOpenSession = Attendance::where('staff_id',$staffId)
                                    ->where('date',$date)
                                    ->whereNull('check_out')
                                    ->exists();

        if($hasOpenSession){
            return redirect()->route('admin.attendance.index')
                ->with('error', "Kemasukkan gagal.");
        }
    
        if($isOverlapping)
        {
            return redirect()->route('admin.attendance.index')
                ->with('error', "Kemasukkan gagal.");
        }

        Attendance::create([
            'id' => $customId,
            'staff_id' => $staffId,
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'date' => $date,
            'keterangan_in' => $keteranganIn,
            'keterangan_out' => $keteranganOut,
            'keterangan_admin' => $validated['keterangan_admin'] ?? 'Kemasukan manual oleh admin',
            'period' => $period,
        ]);

        return redirect()->route('admin.attendance.index')
            ->with('success', "Rekod kehadiran manual berjaya dicipta untuk {$staffId} pada {$date} (ID: {$customId}).");
    }

    /**
     * Update an attendance record (Correction).
     * Recalculates period automatically.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $attendance = Attendance::findOrFail($id);

        $validated = $request->validate([
            'date' => ['required', 'date_format:Y-m-d'],
            'check_in' => ['required'],
            'check_out' => ['nullable'],
            'keterangan_in' => ['nullable', 'string', 'max:255'],
            'keterangan_out' => ['nullable', 'string', 'max:255'],
            'keterangan_staff' => ['nullable', 'string', 'max:255'],
            'keterangan_admin' => ['nullable', 'string', 'max:255'],
        ]);

        // Normalize time format to H:i:s
        $checkIn = strlen($validated['check_in']) === 5 ? $validated['check_in'].':00' : $validated['check_in'];
        $checkOut = null;
        if (! empty($validated['check_out'])) {
            $checkOut = strlen($validated['check_out']) === 5 ? $validated['check_out'].':00' : $validated['check_out'];
        }

        // Automatic re-calculation of period
        $period = Attendance::calculatePeriod($checkIn, $checkOut);

        $updateData = [
            'date' => $validated['date'],
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'keterangan_admin' => $validated['keterangan_admin'] ?? null,
            'period' => $period,
        ];

        if (array_key_exists('keterangan_in', $validated)) {
            $updateData['keterangan_in'] = $validated['keterangan_in'];
        } elseif (array_key_exists('keterangan_staff', $validated)) {
            $updateData['keterangan_in'] = $validated['keterangan_staff'];
        }

        if (array_key_exists('keterangan_out', $validated)) {
            $updateData['keterangan_out'] = $validated['keterangan_out'];
        }

        $attendance->update($updateData);

        return redirect()->route('admin.attendance.index')
            ->with('success', "Rekod kehadiran {$attendance->id} berjaya dikemas kini. Tempoh masa dikira semula: ".($period ?? 'Sesi Aktif'));
    }

    /**
     * Remove an attendance record.
     */
    public function destroy(string $id): RedirectResponse
    {
        $attendance = Attendance::findOrFail($id);
        $attendance->delete();

        return redirect()->route('admin.attendance.index')
            ->with('success', "Rekod kehadiran {$id} telah berjaya dipadam.");
    }

    /**
     * Display Monthly Summary Report for all staff.
     */
    public function summary(Request $request): View|Response
    {
        $selectedMonth = (int) $request->input('month', now()->month);
        $selectedYear = (int) $request->input('year', now()->year);
        $selectedStaffId = trim((string) $request->input('staff_id', ''));
        $staffQuery = User::where('role', 'staff')->orderBy('name', 'asc');
        if ($selectedStaffId !== '') {
            $staffQuery->where('staff_id', $selectedStaffId);
        }
        $staffMembers = $staffQuery->get();

        // Get all attendance records for this month and year
        $records = Attendance::whereYear('date', $selectedYear)
            ->whereMonth('date', $selectedMonth)
            ->get();

        $firstDateOfMonth = Carbon::create($selectedYear, $selectedMonth, 1);
        $totalHB = 0;

        for ($day = 1; $day <= $firstDateOfMonth->daysInMonth; $day++) {
            $date = $firstDateOfMonth->copy()->day($day);

            if (! $date->isFriday() && ! $date->isSaturday()) {
                $totalHB++;
            }
        }

        // Aggregate statistics per staff
        $summaryData = [];
        $grandTotalSeconds = 0;
        $grandTotalDays = 0;
        $grandTotalSessions = 0;

        foreach ($staffMembers as $staff) {
            $staffRecords = $records->where('staff_id', $staff->staff_id);

            $staffSeconds = 0;
            foreach ($staffRecords as $rec) {
                if ($rec->period) {
                    [$h, $m, $s] = explode(':', $rec->period);
                    $staffSeconds += ($h * 3600) + ($m * 60) + (int) $s;
                }
            }

            $distinctDays = $staffRecords->pluck('date')->unique()->count();
            $sessionsCount = $staffRecords->count();

            $hours = floor($staffSeconds / 3600);
            $minutes = floor(($staffSeconds % 3600) / 60);
            $formattedTotalTime = sprintf('%02d Jam %02d Minit', $hours, $minutes);
            $digitalTotalTime = sprintf('%02d:%02d:00', $hours, $minutes);

            $totalScore = $this->calculateTotalScore($staffRecords);
            $totalScorePerMonth = $totalHB;
            $summaryData[] = [
                'staff' => $staff,
                'total_seconds' => $staffSeconds,
                'formatted_time' => $formattedTotalTime,
                'digital_time' => $digitalTotalTime,
                'days_present' => $distinctDays,
                'total_sessions' => $sessionsCount,
                'total_score' => $totalScore,
                'total_hb' => $totalHB,
                'total_score_per_month' => $totalScorePerMonth,
                'records' => $staffRecords->sortBy('date'),
            ];

            $grandTotalSeconds += $staffSeconds;
            $grandTotalDays += $distinctDays;
            $grandTotalSessions += $sessionsCount;
        }

        $allStaffList = User::where('role', 'staff')->orderBy('name', 'asc')->get();

        $grandHours = floor($grandTotalSeconds / 3600);
        $grandMinutes = floor(($grandTotalSeconds % 3600) / 60);

        if ($request->input('download') === 'pdf') {
            $filename = sprintf('attendance-summary-%04d-%02d.pdf', $selectedYear, $selectedMonth);

            TCPDF::SetTitle('Laporan Ringkasan Kehadiran');
            TCPDF::SetMargins(10, 10, 10);
            TCPDF::SetAutoPageBreak(true, 12);
            TCPDF::SetFont('dejavusans', '', 8);

            if ($summaryData === []) {
                TCPDF::AddPage('P', 'A4');
                TCPDF::SetFont('dejavusans', 'B', 14);
                TCPDF::Cell(0, 10, 'Rekod Kehadiran Kakitangan', 0, 1, 'L');
                TCPDF::SetFont('dejavusans', '', 10);
                TCPDF::Cell(0, 8, 'Tiada rekod staf ditemui.', 0, 1, 'L');
            }

            foreach ($summaryData as $row) {
                TCPDF::AddPage('P', 'A4');
                $this->writeAttendancePdfHeading($row, $selectedMonth, $selectedYear);
                $this->writeAttendancePdfTableHeader();

                $recordsByDate = $row['records']->groupBy(fn ($record) => $record->date->format('Y-m-d'));
                $monthStart = Carbon::create($selectedYear, $selectedMonth, 1);

                for ($day = 1; $day <= $monthStart->daysInMonth; $day++) {
                    $reportDate = $monthStart->copy()->day($day);
                    $dayFillColor = $reportDate->day % 2 === 1 ? [229, 231, 235] : [255, 255, 255];
                    $dateKey = $reportDate->format('Y-m-d');
                    $dayRecords = $recordsByDate->get($dateKey, collect())->values();
                    $dayTotalSeconds = 0;

                    foreach ($dayRecords as $dayRecord) {
                        if ($dayRecord->period) {
                            [$hours, $minutes, $seconds] = array_map('intval', explode(':', $dayRecord->period));
                            $dayTotalSeconds += ($hours * 3600) + ($minutes * 60) + $seconds;
                        }
                    }

                    $dayHours = intdiv($dayTotalSeconds, 3600);
                    $dayScore = $dayHours < 3 ? 0 : ($dayHours < 5 ? 0.5 : 1);
                    $dayCode = match ($reportDate->dayOfWeek) {
                        0 => 'A',
                        1 => 'I',
                        2 => 'S',
                        3 => 'R',
                        4 => 'K',
                        5, 6 => 'W',
                        default => '',
                    };
                    $dayLabel = $dayCode !== '' ? $day : (string) $day;
                    $rowsForDay = $dayRecords->isEmpty() ? [null] : $dayRecords;

                    foreach ($rowsForDay as $index => $record) {
                        $isFirstRecord = $index === 0;
                        $note = $record?->keterangan_staff ?: '';
                        TCPDF::SetFont('dejavusans', '', 8);
                        $rowHeight = max(5.7, TCPDF::getStringHeight(self::PDF_COLUMN_WIDTHS[1] - 2, $note, false, true, null, 1));

                        if (TCPDF::GetY() + $rowHeight > self::PDF_PAGE_CONTENT_BOTTOM) {
                            TCPDF::AddPage('P', 'A4');
                            $this->writeAttendancePdfHeading($row, $selectedMonth, $selectedYear, true);
                            $this->writeAttendancePdfTableHeader();
                        }

                        if ($record === null) {
                            $cells = [$dayLabel, '', '', '', '00:00', '00:00', '0.0'];
                        } else {
                            $cells = [
                                $isFirstRecord ? $dayLabel : '',
                                $note,
                                Carbon::parse($record->check_in)->format('h:i A'),
                                $record->check_out ? Carbon::parse($record->check_out)->format('h:i A') : '',
                                $record->period ? substr($record->period, 0, 5) : '',
                                $isFirstRecord ? sprintf('%02d:%02d', $dayHours, intdiv($dayTotalSeconds % 3600, 60)) : '',
                                $isFirstRecord ? number_format($dayScore, 1) : '',
                            ];
                        }

                        $this->writeAttendancePdfRow($cells, $rowHeight, false, true, $dayFillColor);
                    }
                }

                $monthlyHours = intdiv((int) $row['total_seconds'], 3600);
                $monthlyMinutes = intdiv((int) $row['total_seconds'] % 3600, 60);
                $this->writeAttendancePdfTotalsRow(
                    sprintf('%02d:%02d', $monthlyHours, $monthlyMinutes),
                    number_format((float) $row['total_score'], 1)
                );

                if (TCPDF::GetY() + 36 > self::PDF_PAGE_CONTENT_BOTTOM) {
                    TCPDF::AddPage('P', 'A4');
                    $this->writeAttendancePdfHeading($row, $selectedMonth, $selectedYear, true);
                }
            }

            return response(TCPDF::Output($filename, 'I'), Response::HTTP_OK, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            ]);
        }

        return view('admin.attendance.summary', compact(
            'summaryData',
            'staffMembers',
            'allStaffList',
            'selectedMonth',
            'selectedYear',
            'selectedStaffId',
            'grandHours',
            'grandMinutes',
            'grandTotalSessions'
        ));
    }

    private function writeAttendancePdfHeading(array $row, int $month, int $year, bool $continued = false): void
    {
        TCPDF::SetFont('dejavusans', 'B', 13);
        TCPDF::Cell(0, 8, 'Rekod Kehadiran Kakitangan'.($continued ? ' (Sambungan)' : ''), 0, 1, 'L');
        TCPDF::SetFont('dejavusans', '', 13);
        TCPDF::SetCellPaddings(12, 0, 0, 0); // kiri, atas, kanan, bawah: mm
        TCPDF::Cell(0, 7, $row['staff']->name, 0, 1, 'L');
        TCPDF::SetCellPaddings(0, 0, 0, 0); // reset supaya tak menjejaskan sel seterusnya
        TCPDF::Cell(0, 7, ' Tahun: '.$year.'              Bulan: '.$month, 0, 1, 'C');

        TCPDF::Ln(2);
    }

    private function writeAttendancePdfTableHeader(): void
    {
        $this->writeAttendancePdfRow(['HB', 'Keterangan', 'Masuk', 'Keluar', 'Tempoh', 'Jumlah', ''], 8, true, true, [255, 255, 255]);
    }

    private function writeAttendancePdfRow(array $cells, float $height, bool $bold = false, bool $fill = false, array $fillColor = [229, 231, 235]): void
    {
        $widths = self::PDF_COLUMN_WIDTHS;
        $alignments = ['C', 'L', 'C', 'C', 'C', 'C', 'C'];
        $startX = TCPDF::GetX();
        $startY = TCPDF::GetY();
        TCPDF::SetFont('dejavusans', $bold ? 'B' : '', 8);

        if ($fill) {
            TCPDF::SetFillColor($fillColor[0], $fillColor[1], $fillColor[2]);
        }

        foreach ($cells as $index => $cell) {
            $isScoreColumn = $index === array_key_last($cells);
            TCPDF::MultiCell(
                $widths[$index],
                $height,
                (string) $cell,
                $isScoreColumn ? 0 : 1,
                $alignments[$index],
                $fill && ! $isScoreColumn,
                0,
                $startX + array_sum(array_slice($widths, 0, $index)),
                $startY,
                true,
                0,
                false,
                true,
                0,
                'M'
            );
        }

        TCPDF::SetXY($startX, $startY + $height);
    }

    private function writeAttendancePdfTotalsRow(string $monthlyTime, string $monthlyScore): void
    {
        $totalLabelWidth = array_sum(array_slice(self::PDF_COLUMN_WIDTHS, 0, 4));
        TCPDF::SetFont('dejavusans', 'B', 8);
        TCPDF::SetFillColor(229, 231, 235);
        TCPDF::Cell($totalLabelWidth, 8, '', 0, 0, 'L', false);
        TCPDF::Cell(self::PDF_COLUMN_WIDTHS[4], 8, $monthlyTime, 0, 0, 'C', false);
        TCPDF::Cell(self::PDF_COLUMN_WIDTHS[5], 8, $monthlyTime, 0, 0, 'C', false);
        TCPDF::Cell(self::PDF_COLUMN_WIDTHS[6], 8, $monthlyScore, 0, 0, 'C', false);
    }

    private function calculateTotalScore($records)
    {
        $totalScore = 0;
        $records->sortBy('date')->groupBy('date')->each(function ($dailyRecords) use (&$totalScore) {

            $dailyScore = 0;

            foreach ($dailyRecords as $record) {
                if ($record->period) {
                    [$hours, $minutes, $seconds] = array_map('intval', explode(':', $record->period));
                    $dailyScore += ($hours * 3600) + ($minutes * 60) + $seconds;
                }
            }

            // Convert daily score to hours and minutes
            $dailyHours = floor($dailyScore / 3600);

            // Calculate score based on daily hours and minutes
            if ($dailyHours < 3) {
                $totalScore += 0; // no score
            } elseif ($dailyHours < 5) {
                $totalScore += 0.5; // Half day
            } elseif ($dailyHours >= 5) {
                $totalScore += 1; //  Full day
            }
        });

        return round($totalScore, 2); // Round to 2 decimal places
    }
}
