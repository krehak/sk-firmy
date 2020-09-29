<?php

namespace Krehak\SkFirmy\Libs;

use SoapClient;

class TaxIdValidator {
    private const COUNTRY_CODE = 'SK';
    private const TAXATION_URL = 'http://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl';

    public function validate(string $taxId): bool {
        try {
            $client = new SoapClient(self::TAXATION_URL);

            $result = $client->checkVat(array(
                'countryCode' => self::COUNTRY_CODE,
                'vatNumber' => $taxId
            ));

            if($result->valid) {
                return true;
            }
        } catch (\SoapFault $e) {
        }

        return false;
    }

    public function getVatId(string $taxId): string {
        return self::COUNTRY_CODE . $taxId;
    }
}
