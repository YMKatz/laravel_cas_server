<?php
/**
 * Created by PhpStorm.
 * User: leo108
 * Date: 2016/10/27
 * Time: 07:08
 */

namespace YMKatz\CAS\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Middleware;
use TestCase;

class PGTCallerTest extends TestCase
{
    public function testCall()
    {
        $mock      = new MockHandler(
            [
                new Response(200),
                new Response(404),
            ]
        );
        $container = [];
        $history   = Middleware::history($container);
        $handler   = HandlerStack::create($mock);
        $handler->push($history);
        app()->instance(Client::class, new Client(['handler' => $handler]));

        $call = app(PGTCaller::class);
        $this->assertTrue($call->call('https://leo108.com/callback?a=1', 'pgt', 'pgtiou'));
        $this->assertCount(1, $container);
        $transaction = $container[0];
        /* @var Request $request */
        $request = $transaction['request'];
        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals('leo108.com', $request->getUri()->getHost());
        $this->assertEquals('https', $request->getUri()->getScheme());
        $this->assertEquals('/callback', $request->getUri()->getPath());
        $this->assertEquals('a=1&pgtId=pgt&pgtIou=pgtiou', $request->getUri()->getQuery());

        $this->assertFalse($call->call('https://leo108.com/404?a=1', 'pgt', 'pgtiou'));
    }
}
