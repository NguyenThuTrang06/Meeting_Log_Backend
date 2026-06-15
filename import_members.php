<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Member;

$csvFile = __DIR__ . '/members.csv';

if (!file_exists($csvFile)) {
    echo "LỖI: Không tìm thấy file members.csv!\n";
    echo "Vui lòng tải tab 'Nhân viên' từ Google Sheet dưới dạng file CSV, đổi tên thành 'members.csv' và để vào thư mục backend-meeting-logs.\n";
    exit(1);
}

$file = fopen($csvFile, 'r');
// Đọc từng dòng, không bỏ qua dòng đầu vì file của sếp không có tiêu đề (Header)
$count = 0;
while (($row = fgetcsv($file)) !== false) {
    if (empty(trim($row[0]))) continue;
    
    $name = trim($row[0]);
    $team = isset($row[1]) ? trim($row[1]) : '';

    // Cập nhật nếu trùng tên, hoặc tạo mới
    Member::updateOrCreate(
        ['name' => $name],
        ['team' => $team]
    );
    $count++;
}

fclose($file);
echo "Thành công! Đã đồng bộ $count nhân viên vào Database.\n";
