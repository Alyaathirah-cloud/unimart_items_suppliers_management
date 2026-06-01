<?php

$dir = new RecursiveDirectoryIterator(__DIR__ . '/resources/views/owner');
$iterator = new RecursiveIteratorIterator($dir);
$files = new RegexIterator($iterator, '/^.+\.blade\.php$/i', RecursiveRegexIterator::GET_MATCH);

$addedCount = 0;

foreach ($files as $file) {
    $path = $file[0];
    $content = file_get_contents($path);

    // Skip if it already has the Invoices link
    if (strpos($content, 'owner.invoices.index') !== false) {
        continue;
    }

    // Look for the Return Requests link
    $search = '<a href="{{ route(\'owner.return-requests.index\') }}" class="nav-item {{ request()->routeIs(\'owner.return-requests.*\') ? \'active\' : \'\' }}"><span class="nav-icon">↩</span> Return Requests</a>';
    $replace = $search . "\n        <a href=\"{{ route('owner.invoices.index') }}\" class=\"nav-item {{ request()->routeIs('owner.invoices.*') ? 'active' : '' }}\"><span class=\"nav-icon\">📄</span> Invoices</a>";

    if (strpos($content, $search) !== false) {
        $content = str_replace($search, $replace, $content);
        file_put_contents($path, $content);
        echo "Updated: $path\n";
        $addedCount++;
    } else {
        // Try slightly different formatting
        $search2 = '<a href="{{ route(\'owner.return-requests.index\') }}" class="nav-item"><span class="nav-icon">↩</span> Return Requests</a>';
        $replace2 = $search2 . "\n        <a href=\"{{ route('owner.invoices.index') }}\" class=\"nav-item\"><span class=\"nav-icon\">📄</span> Invoices</a>";
        if (strpos($content, $search2) !== false) {
            $content = str_replace($search2, $replace2, $content);
            file_put_contents($path, $content);
            echo "Updated (alt): $path\n";
            $addedCount++;
        }
    }
}

echo "Total files updated: $addedCount\n";
