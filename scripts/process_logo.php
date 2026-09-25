<?php

$srcPath = __DIR__ . '/../public/images/logo1.jpg';
if (!file_exists($srcPath)) {
    die("File not found: $srcPath\n");
}

$img = imagecreatefromjpeg($srcPath);
$w = imagesx($img);
$h = imagesy($img);
echo "New logo1.jpg dimensions: {$w}x{$h}\n";

// Scan horizontal density to find:
// 1. Top of mark
// 2. Bottom of mark (gap before wordmark)
// 3. Top of wordmark
// 4. Bottom of wordmark

$rowCounts = [];
for ($y = 0; $y < $h; $y++) {
    $nonWhite = 0;
    for ($x = 0; $x < $w; $x += 2) {
        $rgb = imagecolorat($img, $x, $y);
        $r = ($rgb >> 16) & 0xFF;
        $g = ($rgb >> 8) & 0xFF;
        $b = $rgb & 0xFF;
        if ($r < 240 || $g < 240 || $b < 240) {
            $nonWhite++;
        }
    }
    $rowCounts[$y] = $nonWhite;
}

// Find top and bottom of entire graphic
$firstRow = 0; $lastRow = $h - 1;
for ($y = 0; $y < $h; $y++) {
    if ($rowCounts[$y] > 5) { $firstRow = $y; break; }
}
for ($y = $h - 1; $y >= 0; $y--) {
    if ($rowCounts[$y] > 5) { $lastRow = $y; break; }
}
echo "Overall graphic vertical range: Y=[{$firstRow}..{$lastRow}]\n";

// Find the gap between mark and wordmark (in the middle ~50% to 75% region)
$searchStart = (int)($firstRow + ($lastRow - $firstRow) * 0.55);
$searchEnd = (int)($firstRow + ($lastRow - $firstRow) * 0.85);

$minCount = 999999;
$gapY = (int)(($searchStart + $searchEnd) / 2);
for ($y = $searchStart; $y <= $searchEnd; $y++) {
    if ($rowCounts[$y] < $minCount) {
        $minCount = $rowCounts[$y];
        $gapY = $y;
    }
}
echo "Detected gap row between symbol and wordmark at Y = $gapY (nonWhite count: $minCount)\n";

// Bounding box of mark symbol (above gapY)
$markMinX = $w; $markMaxX = 0;
$markMinY = $gapY; $markMaxY = 0;
for ($y = $firstRow; $y < $gapY; $y++) {
    for ($x = 0; $x < $w; $x++) {
        $rgb = imagecolorat($img, $x, $y);
        $r = ($rgb >> 16) & 0xFF;
        $g = ($rgb >> 8) & 0xFF;
        $b = $rgb & 0xFF;
        if ($r < 240 || $g < 240 || $b < 240) {
            if ($x < $markMinX) $markMinX = $x;
            if ($x > $markMaxX) $markMaxX = $x;
            if ($y < $markMinY) $markMinY = $y;
            if ($y > $markMaxY) $markMaxY = $y;
        }
    }
}

echo "Mark Symbol Bounding Box: X=[{$markMinX}..{$markMaxX}], Y=[{$markMinY}..{$markMaxY}]\n";
$markW = $markMaxX - $markMinX;
$markH = $markMaxY - $markMinY;
echo "Mark Size: {$markW}x{$markH}\n";

// Create square cropped mark with 5% breathing padding
$markDim = max($markW, $markH);
$markPad = (int)($markDim * 0.05);
$boxSize = $markDim + ($markPad * 2);

$markCenterX = (int)($markMinX + ($markW / 2));
$markCenterY = (int)($markMinY + ($markH / 2));

$cropX = (int)($markCenterX - ($boxSize / 2));
$cropY = (int)($markCenterY - ($boxSize / 2));

// Ensure cropY doesn't cut into gapY
if ($cropY + $boxSize > $gapY) {
    $cropY = $gapY - $boxSize - 5;
}
if ($cropY < 0) $cropY = 0;

$cropMark = imagecreatetruecolor($boxSize, $boxSize);
$white = imagecolorallocate($cropMark, 255, 255, 255);
imagefill($cropMark, 0, 0, $white);

$srcX = max(0, $cropX);
$srcY = max(0, $cropY);
$dstX = $cropX < 0 ? abs($cropX) : 0;
$dstY = $cropY < 0 ? abs($cropY) : 0;
$copyW = min($boxSize - $dstX, $w - $srcX);
$copyH = min($boxSize - $dstY, $h - $srcY);

imagecopy($cropMark, $img, $dstX, $dstY, $srcX, $srcY, $copyW, $copyH);

// Generate all target mark icons
$sizes = [
    ['file' => __DIR__ . '/../public/images/logo-mark-512.png', 'size' => 512],
    ['file' => __DIR__ . '/../public/images/logo-mark-192.png', 'size' => 192],
    ['file' => __DIR__ . '/../public/images/logo-icon-192.png', 'size' => 192],
    ['file' => __DIR__ . '/../public/images/logo-icon-64.png', 'size' => 64],
    ['file' => __DIR__ . '/../public/images/logo-icon-32.png', 'size' => 32],
    ['file' => __DIR__ . '/../public/favicon.png', 'size' => 32],
];

foreach ($sizes as $s) {
    $target = imagecreatetruecolor($s['size'], $s['size']);
    imagecopyresampled($target, $cropMark, 0, 0, 0, 0, $s['size'], $s['size'], $boxSize, $boxSize);
    imagepng($target, $s['file'], 9);
    imagedestroy($target);
    echo "Wrote: " . $s['file'] . " ({$s['size']}x{$s['size']})\n";
}

copy(__DIR__ . '/../public/favicon.png', __DIR__ . '/../public/favicon.ico');
echo "Updated public/favicon.ico\n";

// Also create a cropped high-res FULL logo (mark + wordmark)
$fullMinX = $w; $fullMaxX = 0;
$fullMinY = $h; $fullMaxY = 0;
for ($y = $firstRow; $y <= $lastRow; $y++) {
    for ($x = 0; $x < $w; $x++) {
        $rgb = imagecolorat($img, $x, $y);
        $r = ($rgb >> 16) & 0xFF;
        $g = ($rgb >> 8) & 0xFF;
        $b = $rgb & 0xFF;
        if ($r < 240 || $g < 240 || $b < 240) {
            if ($x < $fullMinX) $fullMinX = $x;
            if ($x > $fullMaxX) $fullMaxX = $x;
            if ($y < $fullMinY) $fullMinY = $y;
            if ($y > $fullMaxY) $fullMaxY = $y;
        }
    }
}
$fullW = $fullMaxX - $fullMinX;
$fullH = $fullMaxY - $fullMinY;
$fullPad = (int)(max($fullW, $fullH) * 0.05);

$cropFull = imagecreatetruecolor($fullW + $fullPad*2, $fullH + $fullPad*2);
$white2 = imagecolorallocate($cropFull, 255, 255, 255);
imagefill($cropFull, 0, 0, $white2);
imagecopy($cropFull, $img, $fullPad, $fullPad, $fullMinX, $fullMinY, $fullW, $fullH);

imagepng($cropFull, __DIR__ . '/../public/images/logo-full.png', 9);
echo "Wrote: public/images/logo-full.png\n";

imagedestroy($cropMark);
imagedestroy($cropFull);
imagedestroy($img);
echo "SUCCESS! All logo assets generated from new logo1.jpg.\n";
