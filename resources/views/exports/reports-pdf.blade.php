<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Reports — {{ $company?->name ?? 'Company' }}</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            color: #111827;
            margin: 24px;
            font-size: 12px;
        }
        header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            border-bottom: 2px solid #059669;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        header h1 { margin: 0; font-size: 20px; }
        header .meta { font-size: 11px; color: #6b7280; text-align: right; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 8px 10px; text-align: left; border-bottom: 1px solid #e5e7eb; vertical-align: top; }
        th {
            background: #f3f4f6;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .03em;
            color: #374151;
        }
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .02em;
        }
        .badge-draft     { background: #f1f5f9; color: #475569; }
        .badge-submitted { background: #dbeafe; color: #1d4ed8; }
        .badge-reviewed  { background: #fef3c7; color: #92400e; }
        .badge-approved  { background: #d1fae5; color: #065f46; }
        .badge-rejected  { background: #fee2e2; color: #991b1b; }
        footer {
            margin-top: 24px;
            padding-top: 8px;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 10px;
            display: flex;
            justify-content: space-between;
        }
        .actions {
            margin-bottom: 16px;
            display: flex;
            gap: 8px;
        }
        .actions button {
            background: #059669;
            color: #fff;
            border: 0;
            padding: 8px 14px;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
        }
        .actions button.secondary {
            background: #fff;
            color: #111827;
            border: 1px solid #d1d5db;
        }
        @media print {
            .actions { display: none; }
            body { margin: 12mm; }
        }
    </style>
</head>
<body>
    <div class="actions">
        <button type="button" onclick="window.print()">Print / Save as PDF</button>
        <button type="button" class="secondary" onclick="window.close()">Close</button>
    </div>

    <header>
        <div>
            <h1>Reports</h1>
            <div style="color:#6b7280; margin-top:4px;">
                {{ $company?->name ?? '—' }}
                @if ($department)
                    — {{ $department->name }}
                @endif
            </div>
        </div>
        <div class="meta">
            <div><strong>Generated:</strong> {{ $generatedAt }}</div>
            <div><strong>Total reports:</strong> {{ $reports->count() }}</div>
        </div>
    </header>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Title</th>
                <th>Department</th>
                <th>Status</th>
                <th>Report Date</th>
                <th>Created By</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($reports as $report)
                <tr>
                    <td>#{{ $report->id }}</td>
                    <td>{{ $report->title }}</td>
                    <td>{{ $report->department?->name ?? '—' }}</td>
                    <td>
                        <span class="badge badge-{{ $report->status }}">{{ ucfirst($report->status) }}</span>
                    </td>
                    <td>{{ optional($report->report_date)->format('d-M-Y') ?? '—' }}</td>
                    <td>{{ $report->creator?->name ?? '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="6" style="text-align:center; padding:24px; color:#9ca3af;">No reports yet</td></tr>
            @endforelse
        </tbody>
    </table>

    <footer>
        <span>QSM — Reports export</span>
        <span>{{ $generatedAt }}</span>
    </footer>
</body>
</html>
