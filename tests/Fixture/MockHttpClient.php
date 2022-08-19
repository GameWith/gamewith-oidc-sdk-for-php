<?php

namespace GameWith\Oidc\Tests\Fixture;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;

/**
 * Class MockHttpClient
 * @package GameWith\Oidc\Tests\Fixture
 */
class MockHttpClient extends Client
{
    /**
     * @var MockHandler
     */
    private $mock;
    /**
     * @var HandlerStack
     */
    private $handlerStack;
    /**
     * @var array
     */
    private $container = [];

    /**
     * MockHttpClient constructor.
     *
     * @param array $responses
     */
    public function __construct(array $responses)
    {
        $this->mock = new MockHandler($responses);
        $history = Middleware::history($this->container);
        $this->handlerStack = HandlerStack::create($this->mock);
        $this->handlerStack->push($history);
        parent::__construct([
            'handler' => $this->handlerStack
        ]);
    }

    public function getHandlerStack(): HandlerStack
    {
        return $this->handlerStack;
    }

    public function getContainer(): array
    {
        return $this->container;
    }
}
