<?php

namespace Krehak\SkFirmy;

use Krehak\SkFirmy\Fields\FieldType;
use Krehak\SkFirmy\Libs\FindInOrsr;
use Krehak\SkFirmy\Libs\FindInRegisterUz;
use Krehak\SkFirmy\Libs\Results;

class SkFirmy {
    private $results;
    
    public function __construct() {
        $this->results = new Results();
    }

    public function find(FieldType $field): SkFirmy {
        $orsr = new FindInOrsr();
        $results = $orsr->find($field);
        $this->results->append($results);
    
        $registerUz = new FindInRegisterUz();
        $results = $registerUz->find($field);
        $this->results->append($results);

        return $this;
    }
    
    public function getResults(): array {
        return $this->results->getAll();
    }
}
