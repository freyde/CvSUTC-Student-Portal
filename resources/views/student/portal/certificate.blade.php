<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate of Grade - {{ $user->name }}</title>
    <style>
        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            .no-print {
                display: none;
            }
            @page {
                margin: 1cm;
            }
        }
        body {
            font-family: 'Times New Roman', serif;
            margin: 0;
            padding: 20px;
            background: white;
        }
        .certificate-container {
            max-width: 8.5in;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border: 2px solid #000;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            font-size: 24px;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .header h2 {
            font-size: 18px;
            font-weight: normal;
            margin: 10px 0;
            text-transform: uppercase;
        }
        .header p {
            font-size: 14px;
            margin: 5px 0;
        }
        .student-info {
            margin: 30px 0;
            line-height: 1.8;
        }
        .student-info p {
            margin: 5px 0;
            font-size: 14px;
        }
        .student-info strong {
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 12px;
        }
        table th {
            background-color: #f0f0f0;
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
            font-weight: bold;
        }
        table td {
            border: 1px solid #000;
            padding: 8px;
        }
        .semester-section {
            margin-bottom: 30px;
        }
        .semester-title {
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 10px;
            text-transform: uppercase;
        }
        .summary {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #000;
        }
        .summary p {
            margin: 5px 0;
            font-size: 14px;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
        }
        .signature-line {
            border-top: 1px solid #000;
            width: 200px;
            margin: 40px auto 5px;
        }
        .print-button {
            text-align: center;
            margin-bottom: 20px;
        }
        .print-button button {
            padding: 10px 20px;
            background-color: #000;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 16px;
            border-radius: 4px;
        }
        .print-button button:hover {
            background-color: #333;
        }
    </style>
</head>
<body>
    <div class="no-print print-button">
        <button onclick="window.print()">Print Certificate</button>
    </div>

    <div class="certificate-container">
        <div class="header">
            <h1>Cavite State University</h1>
            <h2>Tanza Campus</h2>
            <p>Tanza, Cavite</p>
            <h2 style="margin-top: 30px;">Certificate of Grade</h2>
        </div>

        <div class="student-info">
            <p><strong>Name:</strong> {{ strtoupper($user->name) }}</p>
            <p><strong>Student Number:</strong> {{ $user->student_number ?? 'N/A' }}</p>
            @if($user->program)
                <p><strong>Program:</strong> {{ strtoupper($user->program->name) }} ({{ $user->program->code }})</p>
            @endif
            <p><strong>Date Generated:</strong> {{ now()->format('F d, Y') }}</p>
        </div>

        @if($groupedEnrollments->isEmpty())
            <p style="text-align: center; margin: 40px 0;">No grades available yet.</p>
        @else
            @foreach($groupedEnrollments as $semesterKey => $enrollments)
                <div class="semester-section">
                    <div class="semester-title">{{ $semesterKey }}</div>
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 15%;">Course Code</th>
                                <th style="width: 45%;">Course Title</th>
                                <th style="width: 10%; text-align: center;">Lec Unit</th>
                                <th style="width: 10%; text-align: center;">Lab Unit</th>
                                <th style="width: 10%; text-align: center;">Total Units</th>
                                <th style="width: 10%; text-align: center;">Grade</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($enrollments as $enrollment)
                                @php
                                    $finalGrade = $enrollment->grades->firstWhere('item', 'Final') 
                                        ?? $enrollment->grades->firstWhere('item', 'final')
                                        ?? $enrollment->grades->first();
                                    $course = $enrollment->schedule->course ?? $enrollment->course;
                                    $lecUnit = $course->lec_unit ?? 0;
                                    $labUnit = $course->lab_unit ?? 0;
                                    $totalUnits = $lecUnit + $labUnit;
                                @endphp
                                <tr>
                                    <td>{{ $course->code ?? 'N/A' }}</td>
                                    <td>{{ $course->title ?? 'N/A' }}</td>
                                    <td style="text-align: center;">{{ $lecUnit }}</td>
                                    <td style="text-align: center;">{{ $labUnit }}</td>
                                    <td style="text-align: center;">{{ $totalUnits }}</td>
                                    <td style="text-align: center; font-weight: bold;">{{ $finalGrade->score ?? 'N/A' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endforeach

            @php
                // Calculate summary statistics
                $totalUnits = 0;
                $totalEnrollments = 0;
                foreach($groupedEnrollments as $enrollments) {
                    foreach($enrollments as $enrollment) {
                        $course = $enrollment->schedule->course ?? $enrollment->course;
                        $lecUnit = $course->lec_unit ?? 0;
                        $labUnit = $course->lab_unit ?? 0;
                        $totalUnits += ($lecUnit + $labUnit);
                        $totalEnrollments++;
                    }
                }
            @endphp

            <div class="summary">
                <p><strong>Total Number of Courses:</strong> {{ $totalEnrollments }}</p>
                <p><strong>Total Units:</strong> {{ $totalUnits }}</p>
            </div>
        @endif

        <div class="footer">
            <p style="margin-top: 50px;">This is a computer-generated document.</p>
            <p style="margin-top: 10px;">Generated on {{ now()->format('F d, Y \a\t h:i A') }}</p>
        </div>
    </div>
</body>
</html>

