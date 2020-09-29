<?php

namespace Krehak\SkFirmy\Libs;

use Krehak\SkFirmy\Fields\BusinessId;
use Krehak\SkFirmy\Fields\FieldType;
use Krehak\SkFirmy\Fields\TaxId;

class FindInRegisterUz {
    private const URL_MAIN = 'http://www.registeruz.sk/cruz-public/api/uctovne-jednotky?zmenene-od=2000-01-01&pokracovat-za-id=1&max-zaznamov=100&{field}={search}';
    private const URL_DETAIL = 'http://www.registeruz.sk/cruz-public/api/uctovna-jednotka?id={id}';
    private const FIELD_STATE = 'stav';
    private const INVALID_STATES = ['ZMAZANÉ'];
    
    private $validator;

    public function __construct() {
        $this->validator = new TaxIdValidator();
    }

    public function find(FieldType $field): array {
        if(
            $field instanceof BusinessId ||
            $field instanceof TaxId
        ) {
            $items = $this->getAllResults($field);
            $results = [];
    
            foreach($items as $id) {
                $detail = $this->getResultDetail($id);
        
                if(!is_null($detail)) {
                    $results[$detail['business_id']] = $detail;
                }
            }
    
            return $results;
        }

        return [];
    }

    private function getAllResults(FieldType $field): ?array {
        $data = [
            'field' => $field->getName(),
            'search' => $field->getValue()
        ];

        $request = new Request();
        $url = Request::buildUrl(self::URL_MAIN, $data);
        $response = $request
            ->setConnection($url)
            ->getResponse();

        $json = json_decode($response);

        if(!!$json) {
            return array_unique($json->id);
        }

        return null;
    }

    private function getResultDetail(string $id): ?array {
        $data = [
            'id' => $id
        ];

        $request = new Request();
        $url = $request->buildUrl(self::URL_DETAIL, $data);
        $request->setConnection($url);
        $response = $request->getResponse();

        $json = json_decode($response);

        if(!!$json) {
            return $this->parseDetail($json);
        }

        return null;
    }

    private function parseDetail(object $detailObject): ?array {
        if(property_exists($detailObject, self::FIELD_STATE)) {
            $state = $detailObject->{self::FIELD_STATE};
            
            if(in_array($state, self::INVALID_STATES)) {
                return null;
            }
        }
        
        $return = [];
        $return['name'] = $detailObject->nazovUJ;
        $return['street'] = $detailObject->ulica;
        $return['city'] = $detailObject->mesto;
        $return['zip'] = $detailObject->psc;
        $return['business_id'] = $detailObject->ico;

        $taxId = (string)$detailObject->dic;
        $return['tax_id'] = $taxId;

        if($this->isTaxIdValid($taxId)) {
            $return['vat_id'] = $this->getVatId($taxId);
        }

        return array_map('trim', $return);
    }

    private function isTaxIdValid(string $taxId): bool {
        return $this->validator->validate($taxId);
    }

    private function getVatId(string $taxId): string {
        return $this->validator->getVatId($taxId);
    }
}
