<?php

/**
 * Debug Script for Book Upload API
 * Access this file directly to test the API endpoint
 */

echo "<!DOCTYPE html>";
echo "<html><head><title>Book Upload Debug</title>";
echo "<style>
    body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
    .debug-section { background: white; padding: 20px; margin: 10px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    .success { color: green; }
    .error { color: red; }
    .warning { color: orange; }
    pre { background: #f8f8f8; padding: 10px; border-radius: 4px; overflow-x: auto; }
    h2 { border-bottom: 2px solid #007bff; padding-bottom: 10px; }
</style>";
echo "</head><body>";

echo "<h1>🐛 Book Upload API Debug Tool</h1>";

// Check 1: Database Connection
echo "<div class='debug-section'>";
echo "<h2>1. Database Connection</h2>";
try {
    require_once '../../../config/database.php';
    $pdo = getDatabaseConnection();
    echo "<p class='success'>✓ Database connection successful!</p>";

    // Check if books table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'books'");
    if ($stmt->rowCount() > 0) {
        echo "<p class='success'>✓ Books table exists</p>";

        // Show table structure
        $columns = $pdo->query("DESCRIBE books")->fetchAll(PDO::FETCH_ASSOC);
        echo "<p><strong>Table Structure:</strong></p>";
        echo "<pre>";
        foreach ($columns as $col) {
            echo "{$col['Field']} - {$col['Type']} " .
                ($col['Null'] == 'NO' ? '(Required)' : '(Optional)') . "\n";
        }
        echo "</pre>";
    } else {
        echo "<p class='error'>✗ Books table does not exist!</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>✗ Database Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
echo "</div>";

// Check 2: Directory Permissions
echo "<div class='debug-section'>";
echo "<h2>2. Upload Directories</h2>";

$directories = [
    'Books' => '../../../assets/books/',
    'Covers' => '../../../assets/books/images/'
];

foreach ($directories as $name => $dir) {
    $absolutePath = realpath(dirname(__FILE__) . '/' . $dir) ?: $dir;
    echo "<p><strong>{$name} Directory:</strong> {$absolutePath}</p>";

    if (file_exists($dir)) {
        echo "<p class='success'>✓ Directory exists</p>";
        if (is_writable($dir)) {
            echo "<p class='success'>✓ Directory is writable</p>";
        } else {
            echo "<p class='error'>✗ Directory is NOT writable! Fix permissions with chmod 755 or 777</p>";
        }
    } else {
        echo "<p class='warning'>⚠ Directory does not exist (will be created on first upload)</p>";
        // Try to create it
        if (mkdir($dir, 0777, true)) {
            echo "<p class='success'>✓ Successfully created directory</p>";
        } else {
            echo "<p class='error'>✗ Failed to create directory</p>";
        }
    }
}
echo "</div>";

// Check 3: PHP Configuration
echo "<div class='debug-section'>";
echo "<h2>3. PHP Upload Configuration</h2>";
echo "<p><strong>upload_max_filesize:</strong> " . ini_get('upload_max_filesize') . "</p>";
echo "<p><strong>post_max_size:</strong> " . ini_get('post_max_size') . "</p>";
echo "<p><strong>max_file_uploads:</strong> " . ini_get('max_file_uploads') . "</p>";
echo "<p><strong>File uploads enabled:</strong> " . (ini_get('file_uploads') ? 'Yes ✓' : 'No ✗') . "</p>";

if (ini_get('upload_max_filesize') < '10M') {
    echo "<p class='warning'>⚠ upload_max_filesize is less than 10M, large files may fail</p>";
}
echo "</div>";

// Check 4: Sample Request Test
echo "<div class='debug-section'>";
echo "<h2>4. Test Form</h2>";
echo "<p>Use this form to test the upload functionality:</p>";
echo "<form action='insert.php' method='POST' enctype='multipart/form-data' style='max-width: 500px;'>";
echo "<div style='margin: 10px 0;'>";
echo "<label>Title: <input type='text' name='title' value='Test Book' required style='width: 100%; padding: 5px;'></label>";
echo "</div>";
echo "<div style='margin: 10px 0;'>";
echo "<label>Author: <input type='text' name='author' value='Test Author' required style='width: 100%; padding: 5px;'></label>";
echo "</div>";
echo "<div style='margin: 10px 0;'>";
echo "<label>Language: ";
echo "<select name='language' required style='width: 100%; padding: 5px;'>";
echo "<option value='English'>English</option>";
echo "<option value='Spanish'>Spanish</option>";
echo "<option value='French'>French</option>";
echo "</select>";
echo "</label>";
echo "</div>";
echo "<div style='margin: 10px 0;'>";
echo "<label>Description: <textarea name='description' required style='width: 100%; padding: 5px;'>Test Description</textarea></label>";
echo "</div>";
echo "<div style='margin: 10px 0;'>";
echo "<label>Age Group: ";
echo "<select name='age_group' required style='width: 100%; padding: 5px;'>";
echo "<option value='4-6'>4-6</option>";
echo "<option value='7-9'>7-9</option>";
echo "<option value='10-12'>10-12</option>";
echo "</select>";
echo "</label>";
echo "</div>";
echo "<div style='margin: 10px 0;'>";
echo "<label><input type='checkbox' name='isActive' checked> Is Active</label>";
echo "</div>";
echo "<div style='margin: 10px 0;'>";
echo "<label>Cover Image: <input type='file' name='cover_image' accept='image/*' required></label>";
echo "</div>";
echo "<div style='margin: 10px 0;'>";
echo "<label>Book File: <input type='file' name='book_file' accept='.pdf,.epub' required></label>";
echo "</div>";
echo "<button type='submit' style='background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;'>Upload Test Book</button>";
echo "</form>";
echo "</div>";

// Check 5: Recent Errors
echo "<div class='debug-section'>";
echo "<h2>5. PHP Error Log (Last 20 lines)</h2>";
$errorLog = ini_get('error_log');
if ($errorLog && file_exists($errorLog)) {
    $lines = file($errorLog);
    $recentLines = array_slice($lines, -20);
    echo "<pre>" . htmlspecialchars(implode('', $recentLines)) . "</pre>";
} else {
    echo "<p>Error log location: " . ($errorLog ?: "Not configured") . "</p>";
    echo "<p class='warning'>⚠ Check your PHP error_log configuration</p>";
}
echo "</div>";

echo "</body></html>";
