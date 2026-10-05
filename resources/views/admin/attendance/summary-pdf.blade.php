<h1 style="font-size: 16pt; font-weight: bold;">Rekod Kehadiran Kakitangan</h1>
<table cellpadding="4" style="width: 100%; font-size: 12pt;">
    <tr>
        <td width="33%" align="center">{{ mb_strtoupper($row['staff']->name, 'UTF-8') }}</td>
        <td width="33%" align="center">Tahun: {{ $selectedYear }}</td>
        <td width="34%" align="center">Bulan: {{ $selectedMonth }}</td>
    </tr>
</table>
<br><br>
<table border="1" cellpadding="3" cellspacing="0" style="width: 100%; font-size: 9pt;">
    <thead>
        <tr style="font-weight: bold; text-align: center;">
            <th width="5.4%" align="center">HB</th>
            <th width="27.9%">Keterangan</th>
            <th width="13.5%" align="center">Masuk</th>
            <th width="13.5%" align="center">Keluar</th>
            <th width="10.8%" align="center">Tempoh</th>
            <th width="18.9%" align="center">Jumlah</th>
            <th width="10%" border="0"></th>
        </tr>
    </thead>
    <tbody>
        @foreach ($days as $day)
            @php
                $date = \Carbon\Carbon::create($selectedYear, $selectedMonth, $day);
                $dayRecords = $row['records']->filter(
                    fn ($record) => $record->date->format('Y-m-d') === $date->format('Y-m-d')
                );
                $daySeconds = $dayRecords->sum(function ($record) {
                    if (! $record->period) {
                        return 0;
                    }

                    [$hours, $minutes, $seconds] = array_map('intval', explode(':', $record->period));

                    return ($hours * 3600) + ($minutes * 60) + $seconds;
                });
                $dayHours = intdiv($daySeconds, 3600);
                $dayScore = $dayHours < 3 ? 0 : ($dayHours < 5 ? 0.5 : 1);
                $dayLabel = $date->format('j');
                $rowBackground = $date->day % 2 === 1 ? '#e5e7eb' : '#ffffff';
            @endphp
            @forelse ($dayRecords as $record)
                <tr bgcolor="{{ $rowBackground }}">
                    @if ($loop->first)
                        <td width="5.4%" rowspan="{{ $dayRecords->count() }}" align="center">{{ $dayLabel }}</td>
                    @endif
                    <td width="27.9%">{{ $record->keterangan_staff ?: '' }}</td>
                    <td width="13.5%" align="center">{{ \Carbon\Carbon::parse($record->check_in)->format('h:i A') }}</td>
                    <td width="13.5%" align="center">{{ $record->check_out ? \Carbon\Carbon::parse($record->check_out)->format('h:i A') : '' }}</td>
                    <td width="10.8%" align="center">{{ $record->period ? substr($record->period, 0, 5) : '00:00' }}</td>
                    @if ($loop->first)
                        <td width="18.9%" rowspan="{{ $dayRecords->count() }}" align="center">{{ sprintf('%02d:%02d', $dayHours, intdiv($daySeconds % 3600, 60)) }}</td>
                        <td width="10%" border="0" rowspan="{{ $dayRecords->count() }}" align="center">{{ number_format($dayScore, 1) }}</td>
                    @endif
                </tr>
            @empty
                <tr bgcolor="{{ $rowBackground }}">
                    <td width="5.4%" align="center">{{ $dayLabel }}</td>
                    <td width="27.9%"></td>
                    <td width="13.5%" align="center"></td>
                    <td width="13.5%" align="center"></td>
                    <td width="10.8%" align="center">00:00</td>
                    <td width="18.9%" align="center">00:00</td>
                    <td width="10%" border="0" align="center">0.0</td>
                </tr>
            @endforelse
        @endforeach
        @if ($isLastPage)
            @php
                $totalHours = intdiv($row['total_seconds'], 3600);
                $totalMinutes = intdiv($row['total_seconds'] % 3600, 60);
            @endphp
            <tr style="font-weight: bold;">
                <td colspan="5">Jumlah Bulanan</td>
                <td align="center">{{ sprintf('%02d:%02d', $totalHours, $totalMinutes) }}</td>
                <td border="0" align="center">{{ number_format((float) $row['total_score'], 1) }}</td>
            </tr>
        @endif
    </tbody>
</table>
@if ($isLastPage)
    <br><br>
    <table cellpadding="8" style="width: 100%; font-size: 8pt; text-align: center;">
        <tr>
            <td width="50%">_________________________<br><strong>Disediakan Oleh</strong><br>Tarikh: ____________________</td>
            <td width="50%">_________________________<br><strong>Disahkan Oleh</strong><br>Tarikh: ____________________</td>
        </tr>
    </table>
@endif
