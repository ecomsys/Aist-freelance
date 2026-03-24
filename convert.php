<?php
// Конфигурация
$directories = ['./src/vendor']; // добавили styles
$rootFontSize = 16;
$mode = 'toRem';

// Рекурсивная функция
function scanDirRecursively($dir) {
    $files = [];
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($iterator as $file) {
        if ($file->isFile() && preg_match('/\.(js|jsx|ts|tsx|html|css)$/', $file->getFilename())) {
            $files[] = $file->getPathname();
        }
    }
    return $files;
}

// px → rem
function pxToRem($content, $rootFontSize) {
    return preg_replace_callback('/(-?\d+(\.\d+)?)px/', function($matches) use ($rootFontSize) {
        $pxValue = floatval($matches[1]);
        $remValue = round($pxValue / $rootFontSize, 4);
        return $remValue . 'rem';
    }, $content);
}

// rem → px
function remToPx($content, $rootFontSize) {
    return preg_replace_callback('/(-?\d+(\.\d+)?)rem/', function($matches) use ($rootFontSize) {
        $remValue = floatval($matches[1]);
        $pxValue = round($remValue * $rootFontSize, 2);
        return $pxValue . 'px';
    }, $content);
}

// Собираем все файлы из всех папок
$files = [];
foreach ($directories as $dir) {
    $files = array_merge($files, scanDirRecursively($dir));
}

// Обработка
foreach ($files as $file) {
    $content = file_get_contents($file);

    if ($mode === 'toRem') {
        $newContent = pxToRem($content, $rootFontSize);
    } else {
        $newContent = remToPx($content, $rootFontSize);
    }

    if ($newContent !== $content) {
        file_put_contents($file, $newContent);
        echo "Обработан файл: $file\n";
    }
}

echo "Готово! Режим: $mode\n";