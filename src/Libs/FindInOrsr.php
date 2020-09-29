<?php

namespace Krehak\SkFirmy\Libs;

use Krehak\SkFirmy\Fields\BusinessId;
use Krehak\SkFirmy\Fields\FieldType;

class FindInOrsr {
    private const URL_FIND_BUSINESS_ID = 'http://www.orsr.sk/hladaj_ico.asp?ICO={search}&SID=0';
    private const URL_FIND_NAME = 'http://www.orsr.sk/hladaj_subjekt.asp?OBMENO={search}&PF=0&SID=0&S=on';
    private const URL_DETAIL = 'http://www.orsr.sk/vypis.asp?ID={id}&SID=2&P=0';
    private const HTML_FIELD_ID = 'IČO';
    private const HTML_FIELD_NAME = 'Oddiel';
    private const HTML_FIELD_ADDRESS = 'Sídlo';

    public function find(FieldType $field): array {
        if(
            $field instanceof BusinessId
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

    private function getAllResults(FieldType $field): array {
        if($field instanceof BusinessId) $baseUrl = self::URL_FIND_BUSINESS_ID;
        else $baseUrl = null;
        
        $data = [
            'search' => $field->getValue()
        ];

        $request = new Request();
        $url = Request::buildUrl($baseUrl, $data);
        $response = $request
            ->setEncoding('CP1250')
            ->setConnection($url)
            ->getResponse();

        preg_match_all('/vypis\.asp\?ID=([0-9a-z]+)/mi', $response,$found);

        if(isset($found[1])) {
            return array_unique($found[1]);
        }

        return [];
    }

    private function getResultDetail(string $id): ?array {
        $data = [
            'id' => $id
        ];

        $request = new Request();
        $url = $request->buildUrl(self::URL_DETAIL, $data);
        $request->setConnection($url, true);
        $response = $request->getResponse();

        preg_match_all('/<tr>\s+?<td.+?>\s+?(.+?<span class=\'ra\'>.+?<\/span>.+?)<\/tr>/is', $response, $found);

        if(isset($found[1])) {
            return $this->parseDetail($found[1]);
        }

        return null;
    }

    private function parseDetail($detailHtml): ?array {
        if(!is_array($detailHtml) || !$detailHtml) return null;

        $founds = [];
        foreach($detailHtml as $k => $v) {
            if(preg_match('/<span class="tl">(.+?)<\/span>/is', $v, $f)) {
                $field = trim(str_replace(['&nbsp;',':'], '', $f[1]));
            } else {
                $field = $k;
            }

            if(preg_match_all('/<span class=\'ra\'>(.+?)<\/span>/is', $v, $f)) {
                $values = array_map('trim', $f[1]);
            } else {
                $values = '';
            }

            $founds[$field] = $values;
        }

        if(!array_key_exists(self::HTML_FIELD_ID, $founds)) return null;

        $foundico = preg_replace("/[^0-9]/", "", $founds[self::HTML_FIELD_ID][0]);

        $return = [];
        $return['name'] = $founds[self::HTML_FIELD_NAME][0];
        unset($founds[self::HTML_FIELD_ADDRESS][count($founds[self::HTML_FIELD_ADDRESS]) - 1]);

        if(count($founds[self::HTML_FIELD_ADDRESS]) == 1) {
            $return['street'] = '';
            $return['city'] = $founds[self::HTML_FIELD_ADDRESS][0];
            $return['zip'] = '';
        } elseif(count($founds[self::HTML_FIELD_ADDRESS]) == 2) {
            $return['street'] = '';
            $return['city'] = $founds[self::HTML_FIELD_ADDRESS][0];
            $return['zip'] = $founds[self::HTML_FIELD_ADDRESS][1];
        } else {
            $return['zip'] = $founds[self::HTML_FIELD_ADDRESS][count($founds[self::HTML_FIELD_ADDRESS]) - 1];
            unset($founds[self::HTML_FIELD_ADDRESS][count($founds[self::HTML_FIELD_ADDRESS]) - 1]);
            $return['city'] = $founds[self::HTML_FIELD_ADDRESS][count($founds[self::HTML_FIELD_ADDRESS]) - 1];
            unset($founds[self::HTML_FIELD_ADDRESS][count($founds[self::HTML_FIELD_ADDRESS]) - 1]);
            $return['street'] = implode(' ', $founds[self::HTML_FIELD_ADDRESS]);
        }

        $return['business_id'] = $foundico;
        $return['tax_id'] = ''; // Nezname z ORSR
        $return['vat_id'] = '';

        return array_map('trim', $return);
    }
}
