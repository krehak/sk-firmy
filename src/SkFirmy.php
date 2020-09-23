<?php

namespace Krehak\SkFirmy;

use Krehak\SkFirmy\Libs\FindInOrsr;
use Krehak\SkFirmy\Libs\FindInRegisterUz;
use Krehak\SkFirmy\Libs\Results;

class SkFirmy {
    private const FIELD_BUSINESS_ID = 'ico';
    private const FIELD_TAX_ID = 'dic';
    private $results;
    
    public function __construct() {
        $this->results = new Results();
    }

    public function find(string $field, string $search): SkFirmy {
        $field = strtolower($field);

        if($field === self::FIELD_BUSINESS_ID) {
            $orsr = new FindInOrsr();
    
            $this->results->append(
                $orsr->find($search)
            );
        }

        if($field === self::FIELD_BUSINESS_ID || $field === self::FIELD_TAX_ID) {
            $registerUz = new FindInRegisterUz();
    
            $this->results->append(
                $registerUz->find($search)
            );
        }

        return $this;
    }
    
    public function getResults(): array {
        return $this->results->getAll();
    }
}
