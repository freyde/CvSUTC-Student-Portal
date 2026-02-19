<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CERTIFICATE OF GRADES - {{ $user->name }}</title>
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

            header, footer {
                display: none;
            }
        }
        body {
            font-family: 'Times New Roman', serif;
            margin: 0;
            padding: 20px;
            background: white;
            font-size: 12px;
        }
        .certificate-container {
            max-width: 8.5in;
            margin: 0 auto;
            background: white;
            padding: 30px 40px;
        }
        .header {
            display: flex;
            align-items: flex-start;
            margin-bottom: 20px;
        }
        .logo-container {
            width: 120px;
            margin-right: 20px;
            margin-right: -120px;
        }
        .logo-container img {
            width: 100%;
            height: auto;
        }
        .header-text {
            flex: 1;
            text-align: center;
        }
        .header-text .republic {
            font-size: 11px;
            margin-bottom: 5px;
        }
        .header-text .university {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 3px;
            letter-spacing: 0.5px;
        }
        .header-text .campus {
            font-size: 14px;
            margin-bottom: 3px;
        }
        .header-text .address {
            font-size: 11px;
        }
        .certificate-title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin: 20px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .date-section {
            text-align: right;
            margin-top: -50px;
            margin-bottom: 20px;
        }
        .date-section .date {
            font-size: 12px;
            text-decoration: underline;
        }
        .date-section .date-label {
            font-size: 11px;
            margin-top: 3px;
            margin-right: 35px;
        }
        .to-whom {
            margin: 20px 0 15px 0;
            font-size: 12px;
        }
        .certification-text {
            margin: 15px 0;
            line-height: 1.8;
            font-size: 12px;
            text-align: justify;
        }
        .certification-text .underline {
            text-decoration: underline;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 11px;
        }
        table th {
            background-color: #f0f0f0;
            border: 1px solid #000;
            padding: 6px 4px;
            text-align: center;
            font-weight: bold;
        }
        table td {
            border: 1px solid #000;
            padding: 6px 4px;
            text-align: center;
        }
        table td.text-left {
            text-align: left;
        }
        .summary {
            margin-top: 20px;
            line-height: 2;
            font-size: 12px;
        }
        .summary-item {
            margin: 3px 0;
        }
        .summary-item .label {
            display: inline-block;
            width: 150px;
        }
        .summary-item .value {
            text-decoration: underline;
            font-weight: bold;
        }
        .certification-purpose {
            margin-top: 20px;
            font-size: 12px;
        }
        .certification-purpose .underline {
            text-decoration: underline;
        }
        .registrar-section {
            margin-top: 40px;
            text-align: right;
        }
        .registrar-name {
            font-weight: bold;
            text-decoration: underline;
            font-size: 12px;
            margin-bottom: 5px;
        }
        .registrar-title {
            font-size: 11px;
        }
        .grading-scale {
            margin-top: 30px;
            font-size: 10px;
            color: #d10707;
            line-height: 1.6;
        }
        .grading-scale-title {
            font-weight: bold;
            margin-bottom: 5px;
            color: #000;
        }
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 2px solid #000;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .footer-left {
            flex: 1;
        }
        .footer-right {
            flex: 1;
            text-align: right;
        }
        .contact-info {
            font-size: 10px;
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .contact-info span {
            display: flex;
            align-items: center;
            gap: 5px;
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
        <!-- Header Section -->
        <div class="header">
            <div class="logo-container">
                <!-- Logo will be inserted here by user -->
                <div style="width: 100px; height: 100px; display: flex; align-items: center; justify-content: center; font-size: 10px; color: #999;">
                    <img src="{{ asset('img/CvSULogo.png') }}" alt="Application Logo">
                </div>
            </div>
            <div class="header-text">
                <div class="republic">Republic of the Philippines</div>
                <div class="university">CAVITE STATE UNIVERSITY</div>
                <div class="campus">Tanza Campus</div>
                <div class="address">Bagtas, Tanza, Cavite</div>
            </div>
        </div>

        <div class="certificate-title">CERTIFICATE OF GRADES</div>
        <br>
        <br>
        <br>
        <br>
        <div class="date-section">
            <div class="date">&nbsp&nbsp&nbsp{{ now()->format('F d, Y') }}&nbsp&nbsp&nbsp</div>
            <div class="date-label">Date</div>
        </div>

        <!-- Certification Text -->
        <div class="to-whom">To Whom It May Concern:</div>
        
        <div class="certification-text">
            This is to certify that Mr./Ms. <span class="underline" style="padding: 5px;">&nbsp&nbsp&nbsp&nbsp{{ strtoupper($user->name) }}&nbsp&nbsp&nbsp&nbsp</span> 
            with student number <span class="underline" style="padding: 5px;">&nbsp&nbsp&nbsp&nbsp{{ $user->student_number ?? 'N/A' }}&nbsp&nbsp&nbsp&nbsp</span> 
            taking up <span class="underline" style="padding: 5px;">
                @if($user->program)
                    &nbsp&nbsp&nbsp&nbsp{{ strtoupper($user->program->name) }}&nbsp&nbsp&nbsp&nbsp
                @else
                    N/A
                @endif
            </span> 
            obtained the following grades during the 
            @if($groupedEnrollments->isNotEmpty())
                @php
                    $firstSemester = $groupedEnrollments->keys()->first();
                    // Extract semester and academic year from the key (format: "2025-2026 - Second Semester")
                    $parts = explode(' - ', $firstSemester);
                    $academicYear = $parts[0] ?? '';
                    $semester = $parts[1] ?? '';
                @endphp
                <span class="underline">&nbsp&nbsp&nbsp&nbsp{{ strtoupper($semester) }}&nbsp&nbsp&nbsp&nbsp</span> semester of AY <span class="underline" style="padding: 5px;">&nbsp&nbsp&nbsp&nbsp{{ $academicYear }}&nbsp&nbsp&nbsp&nbsp</span>
            @else
                <span class="underline">&nbsp&nbsp&nbsp&nbsp&nbsp</span> semester of AY <span class="underline">&nbsp&nbsp&nbsp&nbsp&nbsp</span>
            @endif
            :
        </div>

        @if($groupedEnrollments->isEmpty())
            <p style="text-align: center; margin: 40px 0;">No grades available yet.</p>
        @else
            <!-- Grades Table -->
            <table>
                <thead>
                    <tr>
                        <th style="width: 12%;">Course Code</th>
                        <th style="width: 38%;">Title</th>
                        <th style="width: 10%;">Grade</th>
                        <th style="width: 10%;">Comp</th>
                        <th style="width: 10%;">Units</th>
                        <th style="width: 10%;">Credit Units</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalUnits = 0;
                        $totalCreditUnits = 0;
                        $totalPassed = 0;
                        $gradeSum = 0;
                        $gradeCount = 0;
                        $gradeUnitSum = 0;
                    @endphp
                    @foreach($groupedEnrollments->first() as $enrollment)
                        @php
                            $finalGrade = $enrollment->grades->firstWhere('item', 'Final') 
                                ?? $enrollment->grades->firstWhere('item', 'final')
                                ?? $enrollment->grades->first();
                            $course = $enrollment->schedule->course ?? $enrollment->course;
                            $lecUnit = $course->lec_unit ?? 0;
                            $labUnit = $course->lab_unit ?? 0;
                            $units = $lecUnit + $labUnit;
                            $gradeValue = $finalGrade->score ?? null;
                            
                            // Determine if completed (not 5.00, INC, DRP)
                            $isCompleted = false;
                            $creditUnits = 0;
                            if ($gradeValue && !in_array(strtoupper($gradeValue), ['5.00', '6.00', '7.00'])) {
                                $isCompleted = true;
                                $creditUnits = $units;
                                $totalPassed++;
                                
                                // Calculate numeric grade for average (exclude INC, DRP)
                                if (is_numeric($gradeValue) && $gradeValue != 6.00 && $gradeValue != 7.00) {
                                    $gradeSum += (float)$gradeValue * $units;
                                    $gradeUnitSum += $units;
                                    $gradeCount++;
                                }
                            }
                            
                            $totalUnits += $units;
                            $totalCreditUnits += $creditUnits;
                        @endphp
                        <tr>
                            <td>{{ $course->code ?? 'N/A' }}</td>
                            <td class="text-left">{{ $course->title ?? 'N/A' }}</td>
                            <td style="font-weight: bold;">
                                 @if($gradeValue === null)
                                    --
                                @elseif($gradeValue == 6.00)
                                    INC
                                @elseif($gradeValue == 7.00)
                                    DRP
                                @else
                                    {{ number_format($gradeValue, 2) }}
                                @endif
                            </td>
                            <td>{{ $isCompleted ? '' : '' }}</td>
                            <td>{{ $units }}</td>
                            <td>{{ $creditUnits }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Summary Section -->
            <div class="summary">
                <div class="summary-item">
                    <span class="label">Total Units:</span>
                    <span class="value">&nbsp&nbsp&nbsp&nbsp&nbsp{{ $totalUnits }}&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</span>
                </div>
                <div class="summary-item">
                    <span class="label">Total Credit Units:</span>
                    <span class="value">&nbsp&nbsp&nbsp&nbsp&nbsp{{ $totalCreditUnits }}&nbsp&nbsp&nbsp&nbsp&nbsp</span>
                </div>
                <div class="summary-item">
                    <span class="label">Passing Percentage:</span>
                    <span class="value">
                        @if($groupedEnrollments->first()->count() > 0)
                            &nbsp&nbsp{{ number_format(($totalPassed / $groupedEnrollments->first()->count()) * 100, 3) }}%&nbsp&nbsp
                        @else
                            ___
                        @endif
                    </span>
                </div>
                <div class="summary-item">
                    <span class="label">Average:</span>
                    <span class="value">
                        @if($gradeCount > 0)
                            &nbsp&nbsp&nbsp{{ number_format($gradeSum / $gradeUnitSum, 3) }}&nbsp&nbsp&nbsp
                        @else
                            &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                        @endif
                    </span>
                </div>

            <!-- Certification Purpose -->
            <div class="certification-purpose">
                This certification is for ENROLMENT purposes only.
            </div>
        @endif

        <!-- Grading Scale -->
        <div class="grading-scale">
            <div class="grading-scale-title">Grading Scale:</div>
            <b>1.00</b> - 100 - 96.7; &nbsp&nbsp&nbsp<b>1.25</b> - 96.6 - 93.4; &nbsp&nbsp&nbsp<b>1.50</b> - 93.3 - 90.1; &nbsp&nbsp<b>1.75</b> - 90.0 - 86.7<br>
            <b>2.00</b> - 86.6 - 83.4; &nbsp&nbsp<b>2.25</b> - 83.3 - 80.1; &nbsp&nbsp&nbsp<b>2.50</b> - 80.0 - 76.7; &nbsp&nbsp<b>2.75</b> - 76.6 - 73.4<br>
            <b>3.00</b> - 73.3 - 70.0; &nbsp&nbsp<b>4.00</b> - Conditional; &nbsp&nbsp<b>5.00</b> - Failed; &nbsp&nbsp&nbsp<b>Inc.&nbsp&nbsp</b> - Incomplete; &nbsp&nbsp<b>Drp.</b> - Dropped
        </div>

        <!-- Footer with Contact Info -->
        <div class="footer">
            <div class="footer-left">
                <!-- Decorative elements can be added here -->
            </div>
            <div class="footer-right">
                <!-- <div class="contact-info">
                    <span>📧 tanza.registrar@cvsu.edu.ph</span>
                    <span>📞 (046) 414-3979</span>
                    <span>🌐 www.cvsu.edu.ph</span>
                </div> -->
            </div>
        </div>
    </div>
</body>
</html>
