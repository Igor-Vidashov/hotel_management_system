<?php
// Проверка синтаксиса functions.php
echo "<h1>Checking functions.php syntax</h1>";

$file = 'includes/functions.php';
if (file_exists($file)) {
    // Проверяем синтаксис
    $output = shell_exec("php -l " . $file);
    echo "<p>Syntax check: " . $output . "</p>";
    
    // Покажем содержимое файла
    echo "<h3>File content (first 20 lines):</h3>";
    $lines = file($file);
    echo "<pre>";
    for ($i = 0; $i < min(20, count($lines)); $i++) {
        echo htmlspecialchars(($i+1) . ": " . $lines[$i]);
    }
    echo "</pre>";
} else {
    echo "<p>File not found: $file</p>";
}
?>