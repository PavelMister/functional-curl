<?php

namespace pavelstudio\tests;


require_once '../autoload.php';

use pavelstudio\src\FunctionalCurl;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class TestFunctionalCurl extends TestCase
{
    /**
     * This test have live connection, need configure SSL in your environment for CURL
     */
    public function testRunGet()
    {
        $ext = new FunctionalCurl();
//        $ext->SetProxy('192.168.1.5:8888');

        $ext->request('GET', 'example.com');
        $this->assertEmpty(empty($ext->GetBody()), 'Not found response for GET');

        $this->assertStringContainsString('Example Domain', $ext->GetBody(), 'Not found body');

        $responseHeaders = $ext->GetResponseHeaders();
        $this->assertIsArray($responseHeaders, 'Not found response headers for GET');

        $this->assertArrayHasKey('Server', $responseHeaders, 'Not found response headers for GET');

        $ext->request('GET', 'empty.host');
        $this->assertTrue($ext->HasCurlError(), 'Not found error curl for GET');

        $this->assertStringContainsString('', $ext->GetCurlError(), 'Not found error curl for GET');

        $this->assertEmpty($ext->GetBody(), 'Not found error curl for GET');


//        $this->assertEmpty(empty($ext->GetBody()), 'Not found response for GET');
    }



}