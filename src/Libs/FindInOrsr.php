<?php

namespace Krehak\SkFirmy\Libs;

class FindInOrsr {
    private $fieldToFind = 'ICO';
    private $urlDomainMain = 'http://www.orsr.sk/hladaj_ico.asp?{field}={search}&SID=0';
    private $urlDomainDetail = 'http://www.orsr.sk/vypis.asp?ID={id}&SID=2&P=0';
    private $htmlFieldId = 'IČO';
    private $htmlFieldName = 'Oddiel';
    private $htmlFieldAddress = 'Sídlo';

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

    private function getAllResults(string $search): array {
        $data = [
            'field' => $this->fieldToFind,
            'search' => $search
        ];

        $request = new Request();
        $url = $request->buildUrl($this->urlDomainMain, $data);
        $request->setConnection($url, true);
        $response = $request->getResponse();

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
        $url = $request->buildUrl($this->urlDomainDetail, $data);
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

        if(!array_key_exists($this->htmlFieldId, $founds)) return null;

        $foundico = preg_replace("/[^0-9]/", "", $founds[$this->htmlFieldId][0]);

        $return = [];
        $return['name'] = $founds[$this->htmlFieldName][0];
        unset($founds[$this->htmlFieldAddress][count($founds[$this->htmlFieldAddress]) - 1]);

        if(count($founds[$this->htmlFieldAddress]) == 1) {
            $return['street'] = '';
            $return['city'] = $founds[$this->htmlFieldAddress][0];
            $return['zip'] = '';
        } elseif(count($founds[$this->htmlFieldAddress]) == 2) {
            $return['street'] = '';
            $return['city'] = $founds[$this->htmlFieldAddress][0];
            $return['zip'] = $founds[$this->htmlFieldAddress][1];
        } else {
            $return['zip'] = $founds[$this->htmlFieldAddress][count($founds[$this->htmlFieldAddress]) - 1];
            unset($founds[$this->htmlFieldAddress][count($founds[$this->htmlFieldAddress]) - 1]);
            $return['city'] = $founds[$this->htmlFieldAddress][count($founds[$this->htmlFieldAddress]) - 1];
            unset($founds[$this->htmlFieldAddress][count($founds[$this->htmlFieldAddress]) - 1]);
            $return['street'] = implode(' ', $founds[$this->htmlFieldAddress]);
        }

        $return['business_id'] = $foundico;
        $return['tax_id'] = ''; // Nezname z ORSR
        $return['vat_id'] = '';

        return array_map('trim', $return);
    }
}
