<?php
$files = glob(__DIR__ . '/resources/views/owner/*/*.blade.php');
$count = 0;
foreach($files as $file) {
    $content = file_get_contents($file);
    // Find the block starting with <div class="topbar-profile">...</div></div>
    $pattern = '/<div class="topbar-profile".*?<\/div>\s*<\/div>/s';
    if (preg_match($pattern, $content)) {
        $newContent = preg_replace($pattern, "@include('owner.components.topbar-profile')", $content);
        if ($content !== $newContent) {
            file_put_contents($file, $newContent);
            echo "Updated $file\n";
            $count++;
        }
    }
}
echo "Total updated: $count\n";
