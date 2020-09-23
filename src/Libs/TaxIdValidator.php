<?php

namespace Krehak\SkFirmy\Libs;

use SoapClient;

class TaxIdValidator {
    private $countryCode = 'SK';
    private $urlTaxationEU = 'http://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl';

    public function validate(string $taxId): bool {
        try {
            $client = new SoapClient($this->urlTaxationEU);

            $obj3 = $client->checkVat(array(
                'countryCode' => $this->countryCode,
                'vatNumber' => $taxId
            ));

            if($obj3->valid) {
                return true;
            }
        } catch (\SoapFault $e) {
        }

        return false;
    }

    public function getVatId(string $taxId): string {
        return "{$this->countryCode}{$taxId}";
    }
}
