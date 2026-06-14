<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Meeting;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportMeetings extends Command
{
    protected $signature = 'import:meetings';
    protected $description = 'Import meetings from Excel file';

    public function handle()
    {
        $this->info('Starting import...');
        $filePath = base_path('202604 MKT - Meeting Logs.xlsx');
        
        if (!file_exists($filePath)) {
            $this->error('Excel file not found at: ' . $filePath);
            return;
        }

        $spreadsheet = IOFactory::load($filePath);
        $masterSheet = $spreadsheet->getSheetByName('Meeting Log');
        
        if (!$masterSheet) {
            // Try first sheet if named differently
            $masterSheet = $spreadsheet->getSheet(0);
        }

        $rows = $masterSheet->toArray();
        $this->info('Found ' . count($rows) . ' rows in master sheet.');

        // Truncate existing
        Meeting::truncate();

        for ($i = 1; $i < count($rows); $i++) {
            $row = $rows[$i];
            if (empty($row[0])) continue;

            $dateStr = $row[1];
            if (is_numeric($dateStr)) {
                $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dateStr);
                $dateStr = $date->format('Y-m-d H:i');
            }

            $meeting = Meeting::create([
                'week' => $row[0] ?? null,
                'meeting_date' => $dateStr ?? null,
                'customer_id' => $row[2] ?? null,
                'project_id' => $row[3] ?? null,
                'team' => $row[4] ?? null,
                'leader' => $row[5] ?? null,
                'name' => $row[6] ?? 'Không có tên',
                'duration_minutes' => isset($row[7]) ? (int)preg_replace('/\D/', '', $row[7]) : 0,
                'video_link' => $row[8] ?? null,
                'short_summary' => $row[9] ?? null,
                'overview' => '',
                'action_items' => '',
                'decisions' => '',
                'issues' => '',
                'next_steps' => ''
            ]);
        }

        $this->info('Master rows imported. Processing details...');

        $sheetNames = $spreadsheet->getSheetNames();
        $detailCount = 0;

        foreach ($sheetNames as $sheetName) {
            $sheet = $spreadsheet->getSheetByName($sheetName);
            $rows = $sheet->toArray();
            
            if (empty($rows[0][0]) || $rows[0][0] !== 'BIÊN BẢN CUỘC HỌP') continue;

            $videoLink = null;
            $overview = '';
            $actionItems = [];
            $decisions = [];
            $issues = [];
            $nextSteps = [];
            
            $currentSection = '';

            foreach ($rows as $row) {
                if (empty($row[0])) continue;

                if ($row[0] === 'Link video') {
                    $videoLink = $row[1];
                    continue;
                }

                if ($row[0] === 'TỔNG QUAN') { $currentSection = 'overview'; continue; }
                if ($row[0] === 'ACTION ITEMS') { $currentSection = 'action_items'; continue; }
                if ($row[0] === 'QUYẾT ĐỊNH ĐÃ THỐNG NHẤT') { $currentSection = 'decisions'; continue; }
                if ($row[0] === 'VẤN ĐỀ & RỦI RO') { $currentSection = 'issues'; continue; }
                if ($row[0] === 'BƯỚC TIẾP THEO') { $currentSection = 'next_steps'; continue; }

                if ($row[0] === 'STT' || $row[0] === 'Mục') continue;

                if ($currentSection === 'overview' && !empty($row[0])) {
                    $overview .= $row[0] . "\n";
                } else if ($currentSection === 'action_items' && !empty($row[0]) && !empty($row[1])) {
                    $dl = (!empty($row[2]) && $row[2] !== 'Không đề cập') ? " | Deadline: " . $row[2] : "";
                    $actionItems[] = $row[0] . ". " . $row[1] . $dl;
                } else if ($currentSection === 'decisions' && !empty($row[0]) && !empty($row[1])) {
                    $decisions[] = $row[0] . ". " . $row[1];
                } else if ($currentSection === 'issues' && !empty($row[0]) && !empty($row[1])) {
                    $issues[] = $row[0] . ". " . $row[1];
                } else if ($currentSection === 'next_steps' && !empty($row[0]) && !empty($row[1])) {
                    $nextSteps[] = $row[0] . ". " . $row[1];
                }
            }

            if (!$videoLink) continue;

            $m = Meeting::where('video_link', $videoLink)->first();
            if ($m) {
                $m->update([
                    'overview' => trim($overview),
                    'action_items' => implode("\n", $actionItems),
                    'decisions' => implode("\n", $decisions),
                    'issues' => implode("\n", $issues),
                    'next_steps' => implode("\n", $nextSteps)
                ]);
                $detailCount++;
            }
        }

        $this->info("Import finished! Updated details for {$detailCount} meetings.");
    }
}
