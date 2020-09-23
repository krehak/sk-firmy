<?php

namespace Krehak\SkFirmy\Libs;

class FindInRegisterUz {
    private $fieldToFind = 'ico';
    private $urlDomainMain = 'http://www.registeruz.sk/cruz-public/api/uctovne-jednotky?zmenene-od=2000-01-01&pokracovat-za-id=1&max-zaznamov=1&{field}={search}';
    private $urlDomainDetail = 'http://www.registeruz.sk/cruz-public/api/uctovna-jednotka?id={id}';
    private $validator;

    public function __construct() {
        $this->validator = new TaxIdValidator();
    }

    public function find(string $search): array {
        $items = $this->getAllResults($search);
        $results = [];

        foreach($items as $id) {
            $detail = $this->getResultDetail($id);

            if(!is_null($detail)) {
                $results[$detail['business_id']] = $detail;
            }
        }

        return $results;
    }

    private function getAllResults(string $search): ?array {
        $data = [
            'field' => $this->fieldToFind,
            'search' => $search
        ];

        $request = new Request();
        $url = $request->buildUrl($this->urlDomainMain, $data);
        $request->setConnection($url);
        $response = $request->getResponse();

        $json = json_decode($response);

        if(!!$json) {
            return array_unique($json->id);
        }

        return null;
    }

    private function getResultDetail(string $id): array {
        $data = [
            'id' => $id
        ];

        $request = new Request();
        $url = $request->buildUrl($this->urlDomainDetail, $data);
        $request->setConnection($url);
        $response = $request->getResponse();

        $json = json_decode($response);

        if(!!$json) {
            return $this->parseDetail($json);
        }

        return [];
    }

    private function parseDetail(object $detailObject): array {
        $return = [];
        $return['name'] = $detailObject->nazovUJ;
        $return['street'] = $detailObject->ulica;
        $return['city'] = $detailObject->mesto;
        $return['zip'] = $detailObject->psc;
        $return['business_id'] = $detailObject->ico;

        $taxId = (string)$detailObject->dic;

        if($this->isTaxIdValid($taxId)) {
            $return['tax_id'] = $taxId;
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
