<?php

use Krehak\SkFirmy\Fields\TaxId;
use Krehak\SkFirmy\SkFirmy;

$skFirmy = new SkFirmy();
$results = $skFirmy->find(new TaxId('2020372640'))->getResults();

echo '<pre>';
print_r($results);
echo '</pre>';
