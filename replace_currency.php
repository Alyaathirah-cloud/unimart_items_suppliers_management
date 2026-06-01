<?php
$dir = new RecursiveDirectoryIterator(__DIR__ . '/resources/views');
$ite = new RecursiveIteratorIterator($dir);
$files = new RegexIterator($ite, '/.*\.blade\.php$/', RegexIterator::GET_MATCH);

$count = 0;
foreach($files as $file) {
    $path = $file[0];
    $content = file_get_contents($path);
    $orig = $content;
    
    // Replace $+ with RM
    $content = str_replace('+$', '+RM ', $content);
    // Replace ${{ with RM {{
    $content = str_replace('${{', 'RM {{', $content);
    // Replace $0.00 with RM 0.00
    $content = str_replace('$0.00', 'RM 0.00', $content);
    // Any remaining literal $> or >$ 
    $content = str_replace('>$', '>RM ', $content);

    if($content !== $orig) {
        file_put_contents($path, $content);
        $count++;
    }
}
echo "Updated $count blade files.\n";
