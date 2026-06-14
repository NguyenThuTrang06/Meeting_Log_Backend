<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$link = "https://drive.google.com/file/d/1AEx8XdxK96hgqE0sAVoP1p0szZp-h3Xf/view";
$m = App\Models\Meeting::where('video_link', $link)->first();
echo "Found by exact match: " . ($m ? "YES (ID: {$m->id})" : "NO") . "\n";

$all = App\Models\Meeting::all();
$matchCount = 0;
foreach ($all as $m) {
    if (trim($m->video_link) === trim($link)) {
        echo "Found by trim: YES (ID: {$m->id})\n";
        echo "Actual string in DB: '" . $m->video_link . "'\n";
        $matchCount++;
    }
}
if ($matchCount === 0) echo "Not found by trim either.\n";
