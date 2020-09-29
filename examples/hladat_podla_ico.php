<?php

use Krehak\SkFirmy\Fields\BusinessId;
use Krehak\SkFirmy\SkFirmy;

$skFirmy = new SkFirmy();
$results = $skFirmy->find(new BusinessId('31322832'))->getResults();

echo '<pre>';
print_r($results);
echo '</pre>';
