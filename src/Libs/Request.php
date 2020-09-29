<?php

namespace Krehak\SkFirmy\Libs;

class Request {
    private $encoding = null;
    private $content;

    public function getResponse(): ?string {
        return $this->content;
    }
    
    public function setEncoding(?string $encoding): Request {
        $this->encoding = $encoding;
        
        return $this;
    }

    public function setConnection(string $url): Request {
        $curlSession = curl_init();
        curl_setopt($curlSession, CURLOPT_URL, $url);
        curl_setopt($curlSession, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0)');
        curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curlSession, CURLOPT_SSL_VERIFYHOST,false);
        curl_setopt($curlSession, CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($curlSession, CURLOPT_MAXREDIRS, 10);
        curl_setopt($curlSession, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curlSession, CURLOPT_CONNECTTIMEOUT, 5);

        if(is_null($this->encoding)) {
            $this->content = curl_exec($curlSession);
        } else {
            $this->content = iconv($this->encoding, 'UTF-8', curl_exec($curlSession));
        }

        curl_close($curlSession);
        
        return $this;
    }

    public static function buildUrl(string $url, array $options): string {
        foreach($options as $key => $value) {
            $url = str_replace("{{$key}}", self::clearValue($value), $url);
        }

        return $url;
    }

    private static function clearValue(string $value): string {
        return str_replace(' ', '', trim($value));
    }
}
