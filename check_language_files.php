<?php

// Function to recursively get all keys from a multi-dimensional array
function array_keys_recursive(array $array) {
    $keys = [];
    foreach ($array as $key => $value) {
        $keys[] = $key;
        if (is_array($value)) {
            $nestedKeys = array_keys_recursive($value);
            $nestedKeys = array_map(function($nestedKey) use ($key) {
                return $key . '.' . $nestedKey;
            }, $nestedKeys);
            $keys = array_merge($keys, $nestedKeys);
        }
    }
    return $keys;
}

// Function to check for duplicate keys in a language file
function check_duplicate_keys($file) {
    $content = include $file;
    if (!is_array($content)) {
        return [];
    }
    
    $keys = [];
    $duplicates = [];
    
    // Flatten the array to check for duplicates
    $iterator = new RecursiveIteratorIterator(new RecursiveArrayIterator($content));
    foreach ($iterator as $key => $value) {
        $keys[] = $key;
    }
    
    // Find duplicates
    $counts = array_count_values($keys);
    foreach ($counts as $key => $count) {
        if ($count > 1) {
            $duplicates[] = $key;
        }
    }
    
    return $duplicates;
}

// Function to compare two language files and find missing keys
function compare_language_files($file1, $file2) {
    $content1 = include $file1;
    $content2 = include $file2;
    
    if (!is_array($content1) || !is_array($content2)) {
        return [];
    }
    
    $keys1 = array_keys_recursive($content1);
    $keys2 = array_keys_recursive($content2);
    
    $missing_in_2 = array_diff($keys1, $keys2);
    $missing_in_1 = array_diff($keys2, $keys1);
    
    return [
        'missing_in_second' => $missing_in_2,
        'missing_in_first' => $missing_in_1
    ];
}

// Main execution
$basePath = __DIR__ . '/lang';
$languages = ['en', 'ar'];
$files = ['app.php', 'auth.php', 'cart.php', 'pagination.php', 'passwords.php', 'profile.php', 'ui.php', 'validation.php'];

$results = [];

// Check for duplicate keys in each file
foreach ($languages as $lang) {
    foreach ($files as $file) {
        $filePath = "$basePath/$lang/$file";
        if (file_exists($filePath)) {
            $duplicates = check_duplicate_keys($filePath);
            if (!empty($duplicates)) {
                $results["$lang/$file"]['duplicates'] = $duplicates;
            }
        }
    }
}

// Compare language files between languages
foreach ($files as $file) {
    $enFile = "$basePath/en/$file";
    $arFile = "$basePath/ar/$file";
    
    if (file_exists($enFile) && file_exists($arFile)) {
        $comparison = compare_language_files($enFile, $arFile);
        
        if (!empty($comparison['missing_in_second'])) {
            $results["comparison/$file"]['missing_in_ar'] = $comparison['missing_in_second'];
        }
        
        if (!empty($comparison['missing_in_first'])) {
            $results["comparison/$file"]['missing_in_en'] = $comparison['missing_in_first'];
        }
    }
}

// Output results
echo "Language File Analysis Results:\n\n";

foreach ($results as $file => $issues) {
    echo "File: $file\n";
    echo str_repeat("=", 80) . "\n";
    
    if (isset($issues['duplicates'])) {
        echo "DUPLICATE KEYS FOUND (" . count($issues['duplicates']) . "):\n";
        foreach ($issues['duplicates'] as $duplicate) {
            echo "  - $duplicate\n";
        }
        echo "\n";
    }
    
    if (isset($issues['missing_in_ar'])) {
        echo "KEYS MISSING IN ARABIC (" . count($issues['missing_in_ar']) . "):\n";
        foreach ($issues['missing_in_ar'] as $missing) {
            echo "  - $missing\n";
        }
        echo "\n";
    }
    
    if (isset($issues['missing_in_en'])) {
        echo "KEYS MISSING IN ENGLISH (" . count($issues['missing_in_en']) . "):\n";
        foreach ($issues['missing_in_en'] as $missing) {
            echo "  - $missing\n";
        }
        echo "\n";
    }
    
    echo "\n";
}

echo "Analysis complete.\n";
