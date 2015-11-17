<?php
namespace Http\Curl\Tests;

use Http\Client\HttpClient;
use Http\Curl\CurlHttpClient;
use Http\Curl\Tests\StreamFactory\DiactorosStreamFactory;
use Http\Discovery\MessageFactory\DiactorosFactory;
use Zend\Diactoros\Request;
use Zend\Diactoros\Response;

/**
 * Tests for Http\Curl\CurlHttpClient
 */
class CurlHttpClientDiactorosTest extends CurlHttpClientTestCase
{
    /**
     * @return HttpClient
     */
    protected function createHttpAdapter()
    {
        return new CurlHttpClient(new DiactorosFactory(), new DiactorosStreamFactory());
    }
}
