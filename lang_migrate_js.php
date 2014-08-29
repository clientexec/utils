<?php

if (!isset($argv[1]) || !isset($argv[2])) {
    die("Usage: lang_migrate.php old_lang_file new_po_file\n");
}

$origFile = $argv[1];
if (!is_readable($origFile)) {
    die("Error: the file $origFile is not readable.\n");
}

$poFile = $argv[2];
if (!is_readable($poFile)) {
    die("Error: the file $poFile is not readable.\n");
}
$poContents = file($poFile);
$poContents = array_map('trim', $poContents);

$translations = array();

$json = file_get_contents($origFile);
// remove leading "var language = " part to get a nice json struct
if (($pos = strpos($json, '{')) === false) {
    die("Error: invalid original file format\n");
}
$json = substr($json, $pos);
$json = json_decode($json, true);
if (is_null($json)) {
    die("Error: problem decoding original file\n");
}

$orig = false;
foreach ($json as $orig => $translation) {
    // preg_quote is not compatible with unix regex, so gotta do my own escaping
    $origRegex = '^' . str_replace(
        array('.', '\\', '+', '*', '?', '[', '^', ']', '$', '(', ')', '{', '}', '=', '!', '|', ':' , '-', ')', '"'),
        array('\\.', '\\\\', '\\+', '\\*', '\\?', '\\[', '\\^', '\\]', '\\$', '\\(', '\\)', '\\{', '\\}', '\\=', '\\!', '\\|', '\\:' , '\\-', '\\)', '\\"'),
        $orig
    ) . '$';

    $searchResult = shell_exec("msggrep --msgid --extended-regexp -e \"$origRegex\" $poFile");
    if (!$searchResult) {
        echo "MSGID MISSING IN PO FILE: $orig\n";
    } else {
        // strip result headers
        $lines = explode("\n", trim($searchResult));
        $searchResult = array();
        $pastHeaders = false;
        for ($i = 0; $i < count($lines); $i++) {
            $lines[$i] = trim($lines[$i]);
            if ($pastHeaders || (preg_match('/msgid ".+"/', $lines[$i])
                    // long messages will start with msgid ""
                    || ($lines[$i] == 'msgid ""' && $lines[$i + 1]{0} == '"'))) {
                $searchResult[] = $lines[$i];
                $pastHeaders = true;
            }
        }
        //echo "searchResult: ";print_r($searchResult);
        $sizeSearchResult = count($searchResult) - 1;
        $comparing = 0;
        foreach ($poContents as $line => $content) {
            $content = trim($content);
            //echo "comparing {$searchResult[$comparing]} AND $content\n";
            if ($content == $searchResult[$comparing]) {
                if ($comparing == $sizeSearchResult) {
                    $translation = str_replace('"', '\\"', $translation);
                    //echo "adding $orig -> $translation\n";
                    $poContents[$line] = "msgstr \"$translation\"";
                    break;
                } else {
                    $comparing++;
                }
            } else {
                $comparing = 0;
            }
        }
    }

    $translations[$orig] = true;
    $orig = false;
}

$poContents = implode("\n", $poContents);
file_put_contents($poFile, $poContents);
