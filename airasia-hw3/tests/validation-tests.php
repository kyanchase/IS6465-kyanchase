<?php
require_once __DIR__ . '/../includes/validation.php';

$valid = [
    'cardName' => 'QA Test Card',
    'cardType' => 'Travel',
    'cardValue' => '25.00',
    'points' => '2500',
];

$cases = [
    ['Valid gift card', [], []],
    ['Blank card name', ['cardName' => ''], ['cardName']],
    ['Altered card type', ['cardType' => 'Unknown'], ['cardType']],
    ['Zero card value', ['cardValue' => '0'], ['cardValue']],
    ['Fractional points', ['points' => '2.5'], ['points']],
];

$results = [];
foreach ($cases as [$label, $changes, $expectedFields]) {
    $input = array_replace($valid, $changes);
    $actualFields = array_keys(validateGiftCard($input)['errors']);
    $results[] = ($actualFields === $expectedFields ? 'PASS' : 'FAIL')
        . ' — ' . $label;
}

if (PHP_SAPI !== 'cli') {
    header('Content-Type: text/plain; charset=UTF-8');
}

echo implode(PHP_EOL, $results) . PHP_EOL;