<?php
function getDirContents($dir, &$results = array()) {
    $files = scandir($dir);
    foreach ($files as $key => $value) {
        $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
        if (!is_dir($path)) {
            if (pathinfo($path, PATHINFO_EXTENSION) === 'php') {
                $results[] = $path;
            }
        } else if ($value != "." && $value != "..") {
            getDirContents($path, $results);
            $results[] = $path;
        }
    }
    return $results;
}

$files = getDirContents(__DIR__ . '/resources/views/owner');
$count = 0;
foreach($files as $file) {
    if(is_dir($file)) continue;
    $content = file_get_contents($file);
    $pattern = '/@include\(\'owner\.components\.topbar-profile\'\)\s*<div class="profile-dropdown" id="profileDropdown">.*?<\/form>\s*<\/div>\s*<\/div>\s*<\/div>\s*@endsection/s';
    
    if (preg_match($pattern, $content)) {
        $replacement = "@include('owner.components.topbar-profile')\n    </div>\n@endsection";
        $newContent = preg_replace($pattern, $replacement, $content);
        if ($content !== $newContent) {
            file_put_contents($file, $newContent);
            echo "Fixed $file\n";
            $count++;
        }
    }
}
echo "Total fixed: $count\n";
