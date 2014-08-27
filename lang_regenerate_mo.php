<?php

$langDir = __DIR__ . '/../language';
$dir = dir($langDir);
while (false !== $entry = $dir->read()) {
    $currentEntry = "$langDir/$entry";
    if (preg_match('/^(.*)\.po$/', $currentEntry, $matches)) {
        echo shell_exec("msgfmt {$matches[1]}.po --output-file={$matches[1]}.mo");
    }
}
