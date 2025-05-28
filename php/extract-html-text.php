<?php
// Parse useful text from USAgov static site html files into files containing
// the relevant data from each html file, without the html stuff - just a line
// of text for each relevant html element

$SKIP_TEXT = [
    "Find an office near you:",
    "Contact:",
    "Website:",
    "Phone number:",
    "Ask a real person any government-related question for free. They will get you the answer or let you know where to find it.",
    "SHARE THIS PAGE:",
    "LAST UPDATED:"
];

$CSS_CLASSES = ['usa-prose', 'usa-card__body', 'life-events-item-content', 'usagov-directory-table'];

$ROOT_PATH = '..';
$INPUT_PATH = $ROOT_PATH . '/input';
$OUTPUT_PATH = $ROOT_PATH . '/output';

$EXCLUDE_DIRS = ['es', 'espanol', 'sites', 'core', 'modules', 'themes', 's3', '_data'];

function getHtmlFiles($dir, $excludeDirs) {
    $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    $files = [];
    foreach ($rii as $file) {
        if ($file->isDir()) continue;
        $filePath = $file->getPathname();
        $parts = explode(DIRECTORY_SEPARATOR, $file->getPath());
        if (array_intersect($parts, $excludeDirs)) continue;
        if (substr($filePath, -5) === ".html") {
            $files[] = $filePath;
        }
    }
    return $files;
}

$html_files = getHtmlFiles($INPUT_PATH, $EXCLUDE_DIRS);

foreach ($html_files as $html_file) {
    $html_content = file_get_contents($html_file);

    $parts = explode(DIRECTORY_SEPARATOR, dirname($html_file));
    $page_path = end($parts);
    $output_file = $OUTPUT_PATH . '/' . $page_path . '.dat';

    $num_items_written = 0;
    $check_duplicates = [];

    // Use DOMDocument and DOMXPath for parsing
    libxml_use_internal_errors(true);
    $dom = new DOMDocument();
    $dom->loadHTML($html_content, LIBXML_NOERROR | LIBXML_NOWARNING);
    $xpath = new DOMXPath($dom);

    $class_selector = [];
    foreach ($CSS_CLASSES as $class) {
        $class_selector[] = "contains(concat(' ', normalize-space(@class), ' '), ' $class ')";
    }
    $selector = "//div[" . implode(' or ', $class_selector) . "]";

    $ofile = fopen($output_file, 'w');
    foreach ($xpath->query($selector) as $div) {
        foreach (['p', 'span', 'a'] as $tag) {
            foreach ($div->getElementsByTagName($tag) as $item) {
                $text = $item->textContent;
                if (!in_array($text, $check_duplicates) && !in_array($text, $SKIP_TEXT)) {
                    $check_duplicates[] = $text;
                    $text = preg_replace('/\s+/', ' ', $text);
                    $text = trim($text);
                    if ($text !== '') {
                        fwrite($ofile, $text . PHP_EOL);
                        $num_items_written += 1;
                    }
                }
            }
        }
    }
    fclose($ofile);

    if ($num_items_written == 0) {
        unlink($output_file);
    }
}
