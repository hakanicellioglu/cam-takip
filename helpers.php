<?php
function base_path(): string {
    // Uygulama alt klasördeyse (örn. /campro), buraya '/campro' yaz.
    return '';
}

function url(string $path = ''): string {
    $base = rtrim(base_path(), '/');
    $path = ltrim($path, '/');
    return $base . '/' . $path;
}

