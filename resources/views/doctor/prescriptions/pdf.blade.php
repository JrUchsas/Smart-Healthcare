<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Prescription – {{ $prescription->prescription_number }}</title>
    <style>
        /* DomPDF requires inline CSS — external stylesheets are not supported reliably. */
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #1a1a2e;
            padding: 30px 40px;
        }

        /* ── Header ──────────────────────────────── */
        .header {
            border-bottom: 3px solid #0d6efd;
            padding-bottom: 12px;
            margin-bottom: 20px;
            display: table;
            width: 100%;
        }
        .header-left  { display: table-cell; vertical-align: middle; }
        .header-right { display: table-cell; vertical-align: middle; text-align: right; width: 50%; }
        .clinic-name  { font-size: 20px; font-weight: bold; color: #0d6efd; }
        .clinic-sub   { font-size: 11px; color: #6c757d; }
        .rx-badge     { font-size: 36px; font-weight: 900; color: #0d6efd; line-height: 1; }

        /* ── Meta row ────────────────────────────── */
        .meta-row {
            display: table;
            width: 100%;
            background: #f0f4ff;
            border-radius: 4px;
            padding: 10px 14px;
            margin-bottom: 18px;
        }
        .meta-cell { display: table-cell; width: 25%; }
        .meta-label { font-size: 9px; color: #6c757d; text-transform: uppercase; letter-spacing: .5px; }
        .meta-value { font-size: 12px; font-weight: bold; color: #1a1a2e; }

        /* ── Section blocks ──────────────────────── */
        .section { margin-bottom: 16px; }
        .section-title {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #0d6efd;
            font-weight: bold;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 4px;
            margin-bottom: 8px;
        }
        .section-text { font-size: 12px; line-height: 1.6; }

        /* ── Medicines table ─────────────────────── */
        table.medicines {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }
        table.medicines thead th {
            background: #0d6efd;
            color: #fff;
            font-size: 10px;
            text-transform: uppercase;
            padding: 8px 10px;
            text-align: left;
        }
        table.medicines tbody td {
            padding: 8px 10px;
            border-bottom: 1px solid #e9ecef;
            font-size: 12px;
        }
        table.medicines tbody tr:nth-child(even) td { background: #f8f9ff; }

        /* ── Footer / signature ──────────────────── */
        .footer {
            margin-top: 30px;
            display: table;
            width: 100%;
        }
        .footer-left  { display: table-cell; vertical-align: bottom; }
        .footer-right { display: table-cell; text-align: right; width: 40%; vertical-align: bottom; }
        .sig-line { border-top: 1px dashed #6c757d; padding-top: 5px; margin-top: 40px; }
        .sig-name { font-weight: bold; font-size: 13px; }
        .sig-label { font-size: 10px; color: #6c757d; }
        .stamp { font-size: 9px; color: #aaa; margin-top: 10px; }
    </style>
</head>
<body>

    {{-- ── Clinic / Hospital Header ── --}}
    <div class="header">
        <div class="header-left">
            <div class="clinic-name">Smart Healthcare</div>
            <div class="clinic-sub">Excellence in Patient Care &bull; Digital Health Platform</div>
        </div>
        <div class="header-right">
            <div class="rx-badge">℞</div>
        </div>
    </div>

    {{-- ── Prescription Meta ── --}}
    <div class="meta-row">
        <div class="meta-cell">
            <div class="meta-label">Ref Number</div>
            <div class="meta-value">{{ $prescription->prescription_number }}</div>
        </div>
        <div class="meta-cell">
            <div class="meta-label">Patient</div>
            <div class="meta-value">{{ $prescription->patient?->user?->name ?? 'N/A' }}</div>
        </div>
        <div class="meta-cell">
            <div class="meta-label">Doctor</div>
            <div class="meta-value">Dr. {{ $prescription->doctor?->user?->name ?? 'N/A' }}</div>
        </div>
        <div class="meta-cell">
            <div class="meta-label">Date Issued</div>
            <div class="meta-value">{{ $prescription->created_at->format('d M Y') }}</div>
        </div>
    </div>

    {{-- ── Clinical Details ── --}}
    @if($prescription->symptoms)
    <div class="section">
        <div class="section-title">Symptoms</div>
        <div class="section-text">{{ $prescription->symptoms }}</div>
    </div>
    @endif

    @if($prescription->diagnosis)
    <div class="section">
        <div class="section-title">Diagnosis</div>
        <div class="section-text">{{ $prescription->diagnosis }}</div>
    </div>
    @endif

    {{-- ── Medicines Table ── --}}
    <div class="section">
        <div class="section-title">Medicines</div>
        <table class="medicines">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Medicine</th>
                    <th>Dosage</th>
                    <th>Duration</th>
                    <th>Instructions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($prescription->items as $i => $item)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td><strong>{{ $item->medicine_name }}</strong></td>
                    <td>{{ $item->dosage }}</td>
                    <td>{{ $item->duration }}</td>
                    <td>{{ $item->instructions ?? '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- ── Advice & Follow-Up ── --}}
    @if($prescription->advice)
    <div class="section">
        <div class="section-title">Advice</div>
        <div class="section-text">{{ $prescription->advice }}</div>
    </div>
    @endif

    @if($prescription->follow_up_date)
    <div class="section">
        <div class="section-title">Follow-Up</div>
        <div class="section-text">Please revisit on <strong>{{ $prescription->follow_up_date->format('d M Y') }}</strong>.</div>
    </div>
    @endif

    {{-- ── Signature Block ── --}}
    <div class="footer">
        <div class="footer-left">
            <div class="stamp">
                This prescription is valid for 30 days from the date of issue.<br>
                Generated by Smart Healthcare Digital Platform.
            </div>
        </div>
        <div class="footer-right">
            <div class="sig-line">
                <div class="sig-name">{{ $prescription->doctor_signature ?? ('Dr. ' . ($prescription->doctor?->user?->name ?? '')) }}</div>
                <div class="sig-label">Licensed Medical Practitioner</div>
            </div>
        </div>
    </div>

</body>
</html>
