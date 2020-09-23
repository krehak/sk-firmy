<?php

namespace Krehak\SkFirmy\Libs;

class Request {
    private $content;

    public function getResponse(): ?string {
        return $this->content;
    }

    public function setConnection(string $url, bool $isCp1250Encoding = false): void {
        $curlSession = curl_init();
        curl_setopt($curlSession, CURLOPT_URL, $url);
        curl_setopt($curlSession, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0)");
        curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curlSession, CURLOPT_SSL_VERIFYHOST,false);
        curl_setopt($curlSession, CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($curlSession, CURLOPT_MAXREDIRS, 10);
        curl_setopt($curlSession, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curlSession, CURLOPT_CONNECTTIMEOUT, 5);

        if($isCp1250Encoding) {
            $this->content = iconv('CP1250', 'UTF-8', curl_exec($curlSession));
        } else {
            $this->content = curl_exec($curlSession);
        }

        curl_close($curlSession);
    }

    public function buildUrl(string $url, array $options): string {
        foreach($options as $key => $value) {
            $url = str_replace("{{$key}}", $this->clearValue($value), $url);
        }

        return $url;
    }

    private function clearValue(string $value): string {
        return str_replace(' ', '', trim($value));
    }
}
