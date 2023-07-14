<?php


function convertHindiToEnglishNumber($hindiNumber)
{
    $hindiToEnglishMap = array(
        '०' => '0',
        '१' => '1',
        '२' => '2',
        '३' => '3',
        '४' => '4',
        '५' => '5',
        '६' => '6',
        '७' => '7',
        '८' => '8',
        '९' => '9'
    );

    $englishNumber = strtr($hindiNumber, $hindiToEnglishMap);

    return $englishNumber;
}

$hindiNumber = '१२३४५६७८९०';
$englishNumber = convertHindiToEnglishNumber($hindiNumber);

echo $englishNumber; // Output: 1234567890
