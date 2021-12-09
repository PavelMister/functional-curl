<?php

namespace pavelstudio\src;

use pavelstudio\src\FunctionalCurlInterface;

class FunctionalCurl implements FunctionalCurlInterface
{
    protected $client;
    protected $latestHtml;
    protected $params = [];
    protected $headers = [];
    protected $contentType = '';
    protected $proxy = '';
    protected $autoRedirects = false;
    protected $responseHeaders = [];
    protected $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.93 Safari/537.36';
    protected $defaultAccept = 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9';

    /**
     * @param $field
     * @param $value
     */
    public function SetParam($field, $value)
    {
        array_push($this->params, [$field => $value]);
    }

    public function EnableRedirects()
    {
        return $autoRedirect = true;
    }

    public function DisableRedirects()
    {
        return $autoRedirect = false;
    }

    public function AllowRedirects()
    {
        return $this->autoRedirects;
    }

    /**
     * @return array
     */
    public function GetParams()
    {
        return $this->params;
    }

    /**
     * @param $name
     * @param $value
     * @throws \Exception
     */
    public function SetHeader($name, $value)
    {
        if (array_key_exists($name, $this->headers)) {
            throw new \Exception('SetHeader::$name - duplicate');
        }
        $this->headers = array_merge($this->headers, [$name => $value]);
    }

    /**
     * @param $name
     */
    public function RemoveHeader($name)
    {
        if (in_array($name, $this->headers)) {
            unset($this->headers[$name]);
        }
    }

    /**
     * @return array
     */
    public function GetHeaders()
    {
        $headers = [];

        if ( ! array_key_exists('User-Agent', $this->headers)) {
            $headers['User-Agent'] = $this->userAgent;
        }

        if ( ! array_key_exists('Accept', $this->headers)) {
            $headers['Accept'] = $this->defaultAccept;
        }

        if (count($this->headers) == 0) {
            return $headers;
        }

        ;
        return array_merge($headers, $this->headers);
    }

    /**
     * @param $contentType
     */
    public function SetContentType($contentType)
    {
        $this->contentType = $contentType;
    }

    /**
     * @param $proxy
     */
    public function SetProxy($proxy)
    {
        $this->proxy = $proxy;
    }

    /**
     * @return string
     */
    public function GetProxy()
    {
        return $this->proxy;
    }

    /**
     * @param $headers array
     * @return array
     */
    public function SetResponseHeaders($headers)
    {
        $headersOut = [];
        if (empty(trim($headers)))
        {
            return [];
        }
        $headers = explode("\n", $headers);
        $headers = array_filter($headers, function($var){
            return !empty(trim($var));
        });

        foreach ($headers as $header) {
            if (strpos($header, ':') == false)
                continue;
            $header = explode(': ', $header, 2);

            $setCookie = strripos($header[0], 'set-cookie');
            if ($setCookie == 0 && $setCookie !== false) {
                $header[0] = strtolower($header[0]);
            }

            $headersOut[$header[0]] = $header[1];
        }

        return $this->responseHeaders = $headersOut;
    }

    /**
     * @return array
     */
    public function GetResponseHeaders()
    {
        return $this->responseHeaders;
    }

    /**
     * @param $method
     * @param $url
     */
    public function request($method, $url)
    {
        var_dump($this->GetHeaders());
        return;
        $method = strtoupper($method);

        $curlHanlde = curl_init();
        $requestOptions = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_FOLLOWLOCATION => $this->AllowRedirects(),
            CURLOPT_HEADER    => true,
            CURLOPT_HTTPHEADER => $this->GetHeaders(),
        ];

        var_dump($curlHanlde);

        if ( ! empty($this->GetProxy())) {
            $requestOptions[CURLOPT_PROXY] = $this->GetProxy();
        }

        switch ($method)
        {
            case "GET":
                {
                }
                break;
            case "POST":
                {
                    $requestOptions[CURLOPT_CUSTOMREQUEST] = 'POST';
                }
                break;
            case "PUT":
                {
                    $requestOptions[CURLOPT_CUSTOMREQUEST] = 'PUT';
                }
                break;
            default:
                $requestOptions[CURLOPT_CUSTOMREQUEST] = 'PUT';
                break;
        }


        curl_setopt_array($curlHanlde, $requestOptions);
        $response = curl_exec($curlHanlde);

        $info = curl_getinfo($curlHanlde);

        $header_size = curl_getinfo($curlHanlde, CURLINFO_HEADER_SIZE);
        $headers = substr($response, 0, $header_size);
        $this->SetResponseHeaders($headers);
        $response = substr($response, $header_size);
        //if (array_key_exists(''))

        //var_dump($info);

        $this->latestHtml = $response;
        return $response;
    }
}