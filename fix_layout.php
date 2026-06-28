<?php
$files = glob(__DIR__ . '/resources/views/owner/**/*.blade.php');
$count = 0;
foreach($files as $file) {
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
