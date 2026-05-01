<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Prescription #{{ $prescription->id }}</title>
    <style>
        @page { margin: 30mm 18mm; }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10pt;
            color: #1f2937;
            line-height: 1.45;
        }
        h1, h2, h3 { margin: 0; padding: 0; }

        .header {
            border-bottom: 2px solid #4f46e5;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .header-table { width: 100%; }
        .clinic-name {
            font-size: 16pt;
            font-weight: 700;
            color: #4f46e5;
            margin-bottom: 2px;
        }
        .clinic-tagline {
            font-size: 9pt;
            color: #64748b;
        }
        .doc-meta {
            text-align: right;
            font-size: 9pt;
        }
        .doc-meta .label {
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 7pt;
        }

        .info-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 10px 14px;
            margin-bottom: 18px;
        }
        .info-table { width: 100%; }
        .info-table td { padding: 4px 0; font-size: 9pt; }
        .info-table .label {
            color: #64748b;
            text-transform: uppercase;
            font-size: 7pt;
            letter-spacing: 0.5px;
            width: 25%;
        }
        .info-table .value { font-weight: 500; }

        .section-title {
            font-size: 11pt;
            color: #4f46e5;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 6px;
            margin-top: 18px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 4px;
        }

        .instructions {
            background: #fef3c7;
            border-left: 3px solid #f59e0b;
            padding: 10px 14px;
            margin-bottom: 16px;
            font-size: 9.5pt;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }
        .items-table thead th {
            background: #4f46e5;
            color: white;
            text-align: left;
            padding: 8px 10px;
            font-size: 9pt;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }
        .items-table tbody td {
            padding: 9px 10px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 10pt;
        }
        .items-table tbody tr:nth-child(even) td {
            background: #f8fafc;
        }
        .items-table .num {
            color: #94a3b8;
            text-align: center;
            width: 28px;
            font-size: 9pt;
        }
        .items-table .medication { font-weight: 600; }

        .signature-block {
            margin-top: 50px;
            text-align: right;
        }
        .signature-line {
            display: inline-block;
            width: 200px;
            border-top: 1px solid #475569;
            padding-top: 6px;
            font-size: 8.5pt;
            color: #475569;
        }
        .signature-name {
            font-weight: 600;
            font-size: 10pt;
            color: #1f2937;
        }

        .footer {
            position: fixed;
            bottom: -18mm;
            left: 0; right: 0;
            text-align: center;
            font-size: 7.5pt;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 6px;
        }

        .legal-notice {
            margin-top: 30px;
            padding: 8px 12px;
            background: #f1f5f9;
            border-radius: 4px;
            font-size: 8pt;
            color: #64748b;
            font-style: italic;
        }
    </style>
</head>
<body>

<div class="header">
    <table class="header-table">
        <tr>
            <td>
                <div class="clinic-name">{{ config('app.name') }}</div>
                <div class="clinic-tagline">Specialist medical care</div>
            </td>
            <td class="doc-meta">
                <div class="label">Prescription #</div>
                <div style="font-weight: 600; font-size: 11pt;">{{ str_pad($prescription->id, 6, '0', STR_PAD_LEFT) }}</div>
                <div class="label" style="margin-top: 6px;">Issued</div>
                <div>{{ $prescription->created_at->format('F j, Y') }}</div>
            </td>
        </tr>
    </table>
</div>

<div class="info-box">
    <table class="info-table">
        <tr>
            <td class="label">Patient</td>
            <td class="value">{{ $prescription->appointment->patient->user->name }}</td>
            <td class="label">DOB</td>
            <td class="value">{{ $prescription->appointment->patient->date_of_birth->format('M j, Y') }}</td>
        </tr>
        <tr>
            <td class="label">Gender</td>
            <td class="value">{{ ucfirst($prescription->appointment->patient->gender) }}</td>
            <td class="label">Blood Group</td>
            <td class="value">{{ $prescription->appointment->patient->blood_group }}</td>
        </tr>
        <tr>
            <td class="label">Visit Date</td>
            <td class="value">{{ $prescription->appointment->appointment_date->format('M j, Y') }}</td>
            <td class="label">Visit Time</td>
            <td class="value">{{ \Carbon\Carbon::parse($prescription->appointment->appointment_time)->format('g:i A') }}</td>
        </tr>
    </table>
</div>

<div class="section-title">Prescribing Doctor</div>
<table class="info-table" style="margin-bottom: 4px;">
    <tr>
        <td class="label">Doctor</td>
        <td class="value">Dr. {{ $prescription->appointment->doctor->user->name }}</td>
        <td class="label">License</td>
        <td class="value">{{ $prescription->appointment->doctor->license_number }}</td>
    </tr>
    <tr>
        <td class="label">Department</td>
        <td class="value">{{ $prescription->appointment->doctor->department->name }}</td>
        <td class="label">Specialization</td>
        <td class="value">{{ $prescription->appointment->doctor->specialization }}</td>
    </tr>
</table>

@if($prescription->general_instructions)
    <div class="section-title">General Instructions</div>
    <div class="instructions">
        {{ $prescription->general_instructions }}
    </div>
@endif

<div class="section-title">Medications</div>
<table class="items-table">
    <thead>
        <tr>
            <th class="num">#</th>
            <th>Medication</th>
            <th>Dosage</th>
            <th>Frequency</th>
            <th>Duration</th>
        </tr>
    </thead>
    <tbody>
        @foreach($prescription->items as $idx => $item)
            <tr>
                <td class="num">{{ $idx + 1 }}</td>
                <td class="medication">{{ $item->medication_name }}</td>
                <td>{{ $item->dosage }}</td>
                <td>{{ $item->frequency }}</td>
                <td>{{ $item->duration }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<div class="signature-block">
    <div class="signature-name">Dr. {{ $prescription->appointment->doctor->user->name }}</div>
    <div style="font-size: 8.5pt; color: #64748b;">{{ $prescription->appointment->doctor->specialization }}</div>
    <div class="signature-line">Doctor's signature</div>
</div>

<div class="legal-notice">
    This is a system-generated prescription. Validity: 30 days from the issue date unless otherwise specified.
    Possession of this document does not authorise refills beyond the duration listed for each medication.
</div>

<div class="footer">
    {{ config('app.name') }} &middot; Generated {{ now()->format('M j, Y \a\t g:i A') }} &middot; Prescription #{{ str_pad($prescription->id, 6, '0', STR_PAD_LEFT) }}
</div>

</body>
</html>
