<?php

namespace App\Jobs;

use App\Models\Enrollment;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ImportEnrollmentsFromCsv implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 1200; // 20 minutes per chunk

    public function __construct(public string $path)
    {
    }

    public function handle(): void
    {
        $fullPath = Storage::path($this->path);

        if (!is_readable($fullPath)) {
            Log::error('Enrollment import: file not readable', ['path' => $fullPath, 'rel' => $this->path]);
            return;
        }

        $file = new \SplFileObject($fullPath);
        $file->setFlags(\SplFileObject::READ_CSV | \SplFileObject::SKIP_EMPTY);

        $lineIndex = 0;
        $headerSkipped = false;

        // Simple caches to reduce queries
        $studentNumberToId = [];
        $scheduleCodeTo = [];

        $buffer = [];
        $bufferSize = 500; // smaller batches to keep each DB op quick

        $flushBuffer = function () use (&$buffer) {
            if (empty($buffer)) return;
            Enrollment::upsert($buffer, ['user_id','schedule_id'], []);
            $buffer = [];
        };

        foreach ($file as $row) {
            if ($row === [null] || $row === false) { continue; }
            $lineIndex++;

            // Detect and skip header
            if (!$headerSkipped) {
                $maybeHeader = array_map(fn($v) => strtolower(trim((string)$v)), $row);
                if (in_array('student', $maybeHeader, true) || in_array('student_number', $maybeHeader, true) || in_array('schedule', $maybeHeader, true) || in_array('schedule_code', $maybeHeader, true)) {
                    $headerSkipped = true;
                    continue;
                }
                $headerSkipped = true; // treat first row as data if not header
            }

            if (count(array_filter($row, fn($v) => $v !== null && $v !== '')) === 0) {
                continue;
            }

            $studentNumber = trim((string)($row[0] ?? ''));
            $scheduleCode = trim((string)($row[1] ?? ''));
            if ($studentNumber === '' || $scheduleCode === '') {
                continue;
            }

            // Resolve student id
            if (!isset($studentNumberToId[$studentNumber])) {
                $studentId = User::where('student_number', $studentNumber)->value('id');
                if (!$studentId) { continue; }
                $studentNumberToId[$studentNumber] = $studentId;
            }

            // Resolve schedule id and course id
            if (!isset($scheduleCodeTo[$scheduleCode])) {
                $schedule = Schedule::where('schedule_code', $scheduleCode)->first(['id','course_id']);
                if (!$schedule) { continue; }
                $scheduleCodeTo[$scheduleCode] = ['schedule_id' => $schedule->id, 'course_id' => $schedule->course_id];
            }

            $buffer[] = [
                'user_id' => $studentNumberToId[$studentNumber],
                'course_id' => $scheduleCodeTo[$scheduleCode]['course_id'],
                'schedule_id' => $scheduleCodeTo[$scheduleCode]['schedule_id'],
            ];

            if (count($buffer) >= $bufferSize) {
                $flushBuffer();
            }
        }

        $flushBuffer();

        // Optional: remove file after processing
        try { Storage::delete($this->path); } catch (\Throwable $e) {}
    }
}


