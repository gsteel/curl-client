<?php
namespace Http\Curl\Tests;

use Http\Client\Exception;
use Http\Client\Exception\RequestException;
use Http\Client\Promise;
use Http\Curl\PromiseCore;
use Psr\Http\Message\ResponseInterface;

/**
 * Tests for Http\Curl\PromiseCore
 *
 * @covers Http\Curl\PromiseCore
 */
class PromiseCoreTest extends BaseUnitTestCase
{
    /**
     * Test on fulfill actions
     */
    public function testOnFulfill()
    {
        $request = $this->createRequest('GET', '/');
        $this->handle = curl_init();

        $core = new PromiseCore($request, $this->handle);
        static::assertSame($request, $core->getRequest());
        static::assertSame($this->handle, $core->getHandle());

        $core->addOnFulfilled(
            function (ResponseInterface $response) {
                return $response->withAddedHeader('X-Test', 'foo');
            }
        );

        $core->fulfill($this->createResponse());
        static::assertEquals(Promise::FULFILLED, $core->getState());
        static::assertInstanceOf(ResponseInterface::class, $core->getResponse());
        static::assertEquals('foo', $core->getResponse()->getHeaderLine('X-Test'));

        $core->addOnFulfilled(
            function (ResponseInterface $response) {
                return $response->withAddedHeader('X-Test', 'bar');
            }
        );
        static::assertEquals('foo, bar', $core->getResponse()->getHeaderLine('X-Test'));
    }

    /**
     * Test on reject actions
     */
    public function testOnReject()
    {
        $request = $this->createRequest('GET', '/');
        $this->handle = curl_init();

        $core = new PromiseCore($request, $this->handle);
        $core->addOnRejected(
            function (RequestException $exception) {
                return new RequestException('Foo', $exception->getRequest(), $exception);
            }
        );

        $exception = new RequestException('Error', $request);
        $core->reject($exception);
        static::assertEquals(Promise::REJECTED, $core->getState());
        static::assertInstanceOf(Exception::class, $core->getException());
        static::assertEquals('Foo', $core->getException()->getMessage());

        $core->addOnRejected(
            function (RequestException $exception) {
                return new RequestException('Bar', $exception->getRequest(), $exception);
            }
        );
        static::assertEquals('Bar', $core->getException()->getMessage());
    }

    /**
     * @expectedException \LogicException
     */
    public function testNotFulfilled()
    {
        $request = $this->createRequest('GET', '/');
        $this->handle = curl_init();
        $core = new PromiseCore($request, $this->handle);
        $core->getResponse();
    }


    /**
     * @expectedException \LogicException
     */
    public function testNotRejected()
    {
        $request = $this->createRequest('GET', '/');
        $this->handle = curl_init();
        $core = new PromiseCore($request, $this->handle);
        $core->getException();
    }
}
