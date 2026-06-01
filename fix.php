<?php

$dir = 'c:\laragon\www\unimart_items_suppliers_management\resources\views\supplier';
$files = [
    $dir . '\dashboard.blade.php' => 'dashboard',
    $dir . '\purchase_orders\index.blade.php' => 'purchase_orders',
    $dir . '\return_requests\index.blade.php' => 'returns',
    $dir . '\credit_notes\index.blade.php' => 'credit_notes',
    $dir . '\credit_notes\show.blade.php' => 'credit_notes',
];

foreach ($files as $file => $active) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        // We will replace <aside class="sidebar"> ... </aside>
        // It might or might not have <!-- Sidebar --> before it.
        $content = preg_replace('/(?:<!-- Sidebar -->\s*)?<aside class="sidebar">.*?<\/aside>/s', "<!-- Sidebar -->\n@include('supplier.partials.sidebar', ['active' => '$active'])", $content);
        file_put_contents($file, $content);
        echo "Updated $file\n";
    }
}
