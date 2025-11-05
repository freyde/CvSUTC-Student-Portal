<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class SplitEnrollmentsImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 900; // 15 minutes

    public function __construct(public string $path, public int $rowsPerChunk = 2000)
    {
    }

    public function handle(): void
    {
        $fullPath = Storage::path($this->path);
        if (!is_readable($fullPath)) {
            return;
        }

        // Ensure chunk directory exists
        Storage::makeDirectory('imports/enrollments');

        $in = new \SplFileObject($fullPath);
        $in->setFlags(\SplFileObject::READ_CSV | \SplFileObject::SKIP_EMPTY);

        $chunkIndex = 0;
        $rowCountInChunk = 0;
        $headerChecked = false;
        $out = null;

        $openChunk = function () use (&$out, &$chunkIndex) {
            $chunkIndex++;
            $chunkRel = 'imports/enrollments/chunk_'.$chunkIndex.'_'.basename($this->path);
            $out = new \SplFileObject(Storage::path($chunkRel), 'w');
            return $chunkRel;
        };

        $currentChunkRelPath = $openChunk();

        foreach ($in as $row) {
            if ($row === [null] || $row === false) { continue; }

            // Header detection for first non-empty row
            if (!$headerChecked) {
                $maybeHeader = array_map(fn($v) => strtolower(trim((string)$v)), $row);
                if (in_array('student', $maybeHeader, true) || in_array('student_number', $maybeHeader, true)) {
                    $headerChecked = true;
                    continue; // skip header
                }
            }
            $headerChecked = true;

            // Write row to current chunk
            $out->fputcsv($row);
            $rowCountInChunk++;

            if ($rowCountInChunk >= $this->rowsPerChunk) {
                // dispatch job for this chunk
                ImportEnrollmentsFromCsv::dispatch($currentChunkRelPath)->onQueue('imports');
                // reset and open next chunk
                $rowCountInChunk = 0;
                $currentChunkRelPath = $openChunk();
            }
        }

        // Dispatch last chunk if it has rows
        if ($rowCountInChunk > 0) {
            ImportEnrollmentsFromCsv::dispatch($currentChunkRelPath)->onQueue('imports');
        } else {
            // No rows written to last chunk, delete empty file
            try { Storage::delete($currentChunkRelPath); } catch (\Throwable $e) {}
        }

        // Optionally remove original file
        try { Storage::delete($this->path); } catch (\Throwable $e) {}
    }
}


