<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $report->title }} — {{ $company?->name ?? 'Company' }}</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: DejaVu Sans, sans-serif;
            color: #111827;
            margin: 0;
            padding: 24px 32px;
            font-size: 11px;
            line-height: 1.5;
        }
        .header {
            border-bottom: 3px solid #059669;
            padding-bottom: 14px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0 0 2px;
            font-size: 20px;
            color: #111827;
        }
        .header .subtitle {
            font-size: 12px;
            color: #6b7280;
            margin: 0;
        }
        .meta-grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .meta-grid td {
            padding: 6px 10px;
            vertical-align: top;
            width: 50%;
            border: 1px solid #e5e7eb;
        }
        .meta-grid .label {
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: #6b7280;
            display: block;
            margin-bottom: 2px;
        }
        .meta-grid .value {
            font-weight: 700;
            color: #111827;
        }
        .badge {
            display: inline-block;
            padding: 2px 10px;
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

        .section-title {
            font-size: 14px;
            font-weight: 700;
            color: #059669;
            border-bottom: 2px solid #d1fae5;
            padding-bottom: 6px;
            margin: 24px 0 12px;
        }

        .form-card {
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            margin-bottom: 16px;
            page-break-inside: avoid;
        }
        .form-card-header {
            background: #f9fafb;
            padding: 8px 12px;
            border-bottom: 1px solid #e5e7eb;
            font-weight: 700;
            font-size: 12px;
        }
        .form-card-body {
            padding: 10px 12px;
        }
        .field-row {
            padding: 6px 0;
            border-bottom: 1px solid #f3f4f6;
        }
        .field-row:last-child {
            border-bottom: 0;
        }
        .field-label {
            font-size: 10px;
            font-weight: 700;
            color: #374151;
            text-transform: uppercase;
            letter-spacing: .03em;
            margin-bottom: 3px;
        }
        .field-type {
            font-size: 9px;
            color: #9ca3af;
            font-weight: 400;
            text-transform: none;
            letter-spacing: 0;
        }
        .field-value {
            color: #111827;
            font-size: 11px;
            min-height: 14px;
        }
        .field-empty {
            color: #9ca3af;
            font-style: italic;
        }
        .field-options {
            color: #6b7280;
            font-size: 10px;
        }
        .answer-line {
            border-bottom: 1px dotted #d1d5db;
            min-height: 18px;
            margin-top: 2px;
        }

        .description-block {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 10px 14px;
            margin-bottom: 16px;
            font-size: 11px;
            color: #374151;
        }

        .footer {
            margin-top: 28px;
            padding-top: 8px;
            border-top: 1px solid #e5e7eb;
            color: #9ca3af;
            font-size: 9px;
        }
        .footer table { width: 100%; }
        .footer td { padding: 0; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $report->title }}</h1>
        <p class="subtitle">{{ $company?->name ?? '' }} — {{ $department?->name ?? '' }}</p>
    </div>

    <table class="meta-grid">
        <tr>
            <td>
                <span class="label">Report Date</span>
                <span class="value">{{ optional($report->report_date)->format('d-M-Y') ?? '—' }}</span>
            </td>
            <td>
                <span class="label">Status</span>
                <span class="badge badge-{{ $report->status }}">{{ ucfirst($report->status) }}</span>
            </td>
        </tr>
        <tr>
            <td>
                <span class="label">Created By</span>
                <span class="value">{{ $creator ?? '—' }}</span>
            </td>
            <td>
                <span class="label">Department</span>
                <span class="value">{{ $department?->name ?? '—' }}</span>
            </td>
        </tr>
        @if($report->description)
        <tr>
            <td colspan="2">
                <span class="label">Description</span>
                <span class="value" style="font-weight:400;">{{ $report->description }}</span>
            </td>
        </tr>
        @endif
    </table>

    @if($forms->isEmpty())
        <p style="text-align:center; color:#9ca3af; padding:24px;">No forms attached to this report.</p>
    @endif

    @foreach($forms as $form)
        <div class="section-title">{{ $form['name'] }}</div>

        <div class="form-card">
            <div class="form-card-header">
                {{ $form['name'] }}
                @if($mode === 'full' && !empty($form['submitted_at']))
                    <span style="font-weight:400; color:#6b7280; font-size:10px;">
                        &mdash; Submitted {{ $form['submitted_at'] }}
                        @if(!empty($form['submitter']))
                            by {{ $form['submitter'] }}
                        @endif
                    </span>
                @endif
            </div>
            <div class="form-card-body">
                @forelse($form['fields'] as $field)
                    <div class="field-row">
                        <div class="field-label">
                            {{ $field['label'] }}
                            @if($field['required']) <span style="color:#ef4444;">*</span> @endif
                        </div>

                        @if($mode === 'full')
                            <div class="field-value">
                                @if($field['type'] === 'signature' && !empty($field['answer']) && str_starts_with($field['answer'], 'data:image'))
                                    <img src="{{ $field['answer'] }}" style="max-width:200px; max-height:60px; border:1px solid #e5e7eb;" alt="Signature" />
                                @elseif(!empty($field['answer']))
                                    {{ is_array($field['answer']) ? implode(', ', $field['answer']) : $field['answer'] }}
                                @else
                                    <span class="field-empty">No response provided</span>
                                @endif
                            </div>
                        @else
                            {{-- Content-only: show field info and blank answer line --}}
                            @if(!empty($field['options']))
                                <div class="field-options">Options: {{ implode(', ', $field['options']) }}</div>
                            @endif
                            <div class="answer-line"></div>
                        @endif
                    </div>
                @empty
                    <p class="field-empty">No fields in this form.</p>
                @endforelse
            </div>
        </div>
    @endforeach

    <div class="footer">
        <table>
            <tr>
                <td>QSMCore — Report #{{ $report->id }}</td>
                <td style="text-align:right;">Generated {{ $generatedAt }}</td>
            </tr>
        </table>
    </div>
</body>
</html>
