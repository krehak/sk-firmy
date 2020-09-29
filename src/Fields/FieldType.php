<?php

namespace Krehak\SkFirmy\Fields;

abstract class FieldType implements FieldInterface {
    private $value;
    
    public function __construct($value) {
        $this->value = $value;
    }

    public function getValue(): string {
        return $this->value;
    }
}
