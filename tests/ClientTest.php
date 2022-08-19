<?php

namespace GameWith\Oidc\Tests;

use GameWith\Oidc\Client;
use GameWith\Oidc\ClientMetadata;
use GameWith\Oidc\Exception\InvalidResponseException;
use GameWith\Oidc\Exception\JsonErrorException;
use GameWith\Oidc\Property\AuthenticationRequestProperty;
use GameWith\Oidc\Property\ExchangeProperty;
use GameWith\Oidc\Property\RefreshProperty;
use GameWith\Oidc\Provider;
use GameWith\Oidc\Tests\Fixture\Loader;
use GameWith\Oidc\Tests\Fixture\MockHttpClient;
use GameWith\Oidc\Util\FilterInput;
use GameWith\Oidc\Util\Json;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Psr7\Response;
use Yoast\PHPUnitPolyfills\TestCases\TestCase;

/**
 * Class ClientTest
 * @package GameWith\Oidc\Tests
 */
class ClientTest extends TestCase
{
    /**
     * @var ClientMetadata
     */
    private $metadata;

    /**
     * @var Provider
     */
    private $provider;

    protected function set_up()
    {
        parent::set_up();
        $this->provider = new Provider([
            'issuer'                 => 'http://localhost/issuer',
            'authorization_endpoint' => 'http://localhost/authorization',
            'token_endpoint'         => 'http://localhost/token',
            'userinfo_endpoint'      => 'http://localhost/userinfo',
            'jwks_endpoint'          => 'http://localhost/jwks',
        ]);
        $this->metadata = new ClientMetadata('abc', 'abc', 'http://localhost');
    }

    public function tear_down()
    {
        parent::tear_down();
        \Mockery::close();
    }

    /**
     * @dataProvider providerSendAuthenticationRequest
     * @param AuthenticationRequestProperty $property
     * @param $expected
     * @param null $exception
     */
    public function testSendAuthenticationRequest(
        AuthenticationRequestProperty $property,
        $expected = null,
        $exception = null
    ) {
        if (!is_null($exception)) {
            $this->expectException($exception);
        }
        $client = $this->setupClient();
        $redirectResponse = $client->sendAuthenticationRequest($property);
        if (!is_null($expected)) {
            $this->assertEquals($expected, $redirectResponse->getUrl());
        }
    }

    /**
     * @return array
     */
    public function providerSendAuthenticationRequest()
    {
        return [
            'empty' => [
                new AuthenticationRequestProperty(),
                null,
                \UnexpectedValueException::class,
            ],
            'empty response_type' => [
                (new AuthenticationRequestProperty(''))->addScope('openid'),
                null,
                \UnexpectedValueException::class,
            ],
            'unsupported response_type' => [
                (new AuthenticationRequestProperty('invalid'))->addScope(
                    'openid'
                ),
                null,
                \UnexpectedValueException::class,
            ],
            'supported response_type' => [
                (new AuthenticationRequestProperty('code'))->addScope('openid'),
                'http://localhost/authorization?redirect_uri=http%3A%2F%2Flocalhost&response_type=code&scope=openid&client_id=abc',
            ],
            'add multiple scope' => [
                (new AuthenticationRequestProperty())->addScope(
                    'openid',
                    'profile'
                ),
                'http://localhost/authorization?redirect_uri=http%3A%2F%2Flocalhost&response_type=code&scope=openid%20profile&client_id=abc',
            ],
            'set max_age' => [
                (new AuthenticationRequestProperty())
                    ->addScope('openid')
                    ->setMaxAge(10),
                'http://localhost/authorization?redirect_uri=http%3A%2F%2Flocalhost&response_type=code&scope=openid&client_id=abc&max_age=10',
            ],
            'set state' => [
                (new AuthenticationRequestProperty())
                    ->addScope('openid')
                    ->setState('dummy-state'),
                'http://localhost/authorization?redirect_uri=http%3A%2F%2Flocalhost&response_type=code&scope=openid&client_id=abc&state=dummy-state',
            ],
            'set nonce' => [
                (new AuthenticationRequestProperty())
                    ->addScope('openid')
                    ->setNonce('dummy-nonce'),
                'http://localhost/authorization?redirect_uri=http%3A%2F%2Flocalhost&response_type=code&scope=openid&client_id=abc&nonce=dummy-nonce',
            ],
            'set code_challenge' => [
                (new AuthenticationRequestProperty())
                    ->addScope('openid')
                    ->setNonce('dummy-code-challenge'),
                'http://localhost/authorization?redirect_uri=http%3A%2F%2Flocalhost&response_type=code&scope=openid&client_id=abc&nonce=dummy-code-challenge',
            ],
            'all' => [
                (new AuthenticationRequestProperty())
                    ->addScope('openid', 'profile')
                    ->setMaxAge(10)
                    ->setState('a')
                    ->setNonce('b')
                    ->setState('c')
                    ->setCodeChallenge('d'),
                'http://localhost/authorization?redirect_uri=http%3A%2F%2Flocalhost&response_type=code&scope=openid%20profile&client_id=abc&state=c&max_age=10&nonce=b&code_challenge=d',
            ],
        ];
    }

    /**
     * @dataProvider providerReceiveAuthenticationRequest
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     * @param $params
     * @param $settings
     * @throws InvalidResponseException
     */
    public function testReceiveAuthenticationRequest($params, $settings)
    {
        $mock = \Mockery::mock('overload:' . FilterInput::class)->makePartial();
        $mock->shouldReceive('values')->andReturn($params);
        if (isset($settings['exception'])) {
            $this->expectException($settings['exception']);
        }
        $client = $this->setupClient();
        $code = $client->receiveAuthenticationRequest($settings['state'] ?? null);
        if (isset($settings['expected'])) {
            $this->assertEquals($settings['expected'], $code);
        }
    }

    public function providerReceiveAuthenticationRequest()
    {
        return [
            'params: false' => [
                false,
                [
                    'exception' => InvalidResponseException::class,
                ],
            ],
            'params: null' => [
                false,
                [
                    'exception' => InvalidResponseException::class,
                ],
            ],
            'params: []' => [
                [],
                [
                    'exception' => InvalidResponseException::class,
                ],
            ],
            'only error' => [
                [
                    'error' => 'invalid request',
                ],
                [
                    'exception' => InvalidResponseException::class,
                ],
            ],
            'error and error_description' => [
                [
                    'error'             => 'invalid',
                    'error_description' => 'invalid scope',
                ],
                [
                    'exception' => InvalidResponseException::class,
                ],
            ],
            'mismatch state: null' => [
                [
                    'state' => 'test',
                    'code'  => 'ok',
                ],
                [
                    'state'     => null,
                    'exception' => InvalidResponseException::class,
                ],
            ],
            'mismatch state: dummy' => [
                [
                    'state' => 'test',
                    'code'  => 'ok',
                ],
                [
                    'state'     => 'dummy',
                    'exception' => InvalidResponseException::class,
                ],
            ],
            'match state' => [
                [
                    'state' => 'test',
                    'code'  => 'ok',
                ],
                [
                    'state'    => 'test',
                    'expected' => 'ok',
                ],
            ],
            'included error and code' => [
                [
                    'error' => 'invalid request',
                    'code'  => 'ok',
                ],
                [
                    'exception' => InvalidResponseException::class,
                ],
            ],
            'only code' => [
                [
                    'code' => 'ok',
                ],
                [
                    'expected' => 'ok',
                ],
            ],
        ];
    }

    /**
     * @dataProvider providerExchange
     * @param array $responses
     * @param ExchangeProperty $property
     * @param array $settings
     * @throws JsonErrorException
     * @throws \GameWith\Oidc\Exception\OidcClientException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testExchange(
        array $responses,
        ExchangeProperty $property,
        array $settings
    ) {
        if (isset($settings['exception'])) {
            $this->expectException($settings['exception']);
        }
        $client = $this->setupClient();
        $client->setHttpClient(new MockHttpClient($responses));
        $token = $client->exchange($property);
        if (isset($settings['expected'])) {
            $this->assertEquals($settings['expected'], [
                'access_token'  => $token->getAccessToken(),
                'refresh_token' => $token->getRefreshToken(),
                'expires_in'    => $token->getExpiresIn(),
                'scope'         => $token->getScope(),
                'id_token'      => $token->getIdToken(),
            ]);
        }
    }

    /**
     * @return array
     * @throws JsonErrorException
     */
    public function providerExchange()
    {
        return [
            'empty property' => [
                [
                    new Response(
                        200,
                        ['Content-Type' => 'application/json'],
                        Loader::load('jwks.json')
                    ),
                    new Response(
                        200,
                        ['Content-Type' => 'application/json'],
                        Json::encode([
                            'access_token'  => 'dummy-access-token',
                            'refresh_token' => 'dummy-refresh-token',
                            'expires_in'    => 600,
                            'scope'         => 'openid',
                            'id_token'      => 'dummy-access-token',
                        ])
                    ),
                ],
                new ExchangeProperty(''),
                [
                    'exception' => \UnexpectedValueException::class,
                ],
            ],
            'empty scope property' => [
                [
                    new Response(
                        200,
                        ['Content-Type' => 'application/json'],
                        Loader::load('jwks.json')
                    ),
                    new Response(
                        200,
                        ['Content-Type' => 'application/json'],
                        Json::encode([
                            'access_token'  => 'dummy-access-token',
                            'refresh_token' => 'dummy-refresh-token',
                            'expires_in'    => 600,
                            'scope'         => 'openid',
                            'id_token'      => 'dummy-access-token',
                        ])
                    ),
                ],
                new ExchangeProperty('code'),
                [
                    'exception' => \UnexpectedValueException::class,
                ],
            ],
            'invalid grant_type property' => [
                [
                    new Response(
                        200,
                        ['Content-Type' => 'application/json'],
                        Loader::load('jwks.json')
                    ),
                    new Response(
                        200,
                        ['Content-Type' => 'application/json'],
                        Json::encode([
                            'access_token'  => 'dummy-access-token',
                            'refresh_token' => 'dummy-refresh-token',
                            'expires_in'    => 600,
                            'scope'         => 'openid',
                            'id_token'      => 'dummy-access-token',
                        ])
                    ),
                ],
                (new ExchangeProperty('code', 'dummy'))->addScope('openid'),
                [
                    'exception' => \UnexpectedValueException::class,
                ],
            ],
            'server error: jwks' => [
                [
                    new Response(
                        500,
                        ['Content-Type' => 'application/json'],
                        'Internal Server Error'
                    ),
                    new Response(
                        200,
                        ['Content-Type' => 'application/json'],
                        Json::encode([
                            'access_token'  => 'dummy-access-token',
                            'refresh_token' => 'dummy-refresh-token',
                            'expires_in'    => 600,
                            'scope'         => 'openid',
                            'id_token'      => 'dummy-access-token',
                        ])
                    ),
                ],
                (new ExchangeProperty('dummy'))->addScope('openid'),
                [
                    'exception' => ServerException::class,
                ],
            ],
            'client error: jwks' => [
                [
                    new Response(
                        403,
                        ['Content-Type' => 'application/json'],
                        'Forbidden'
                    ),
                    new Response(
                        200,
                        ['Content-Type' => 'application/json'],
                        Json::encode([
                            'access_token'  => 'dummy-access-token',
                            'refresh_token' => 'dummy-refresh-token',
                            'expires_in'    => 600,
                            'scope'         => 'openid',
                            'id_token'      => 'dummy-access-token',
                        ])
                    ),
                ],
                (new ExchangeProperty('dummy'))->addScope('openid'),
                [
                    'exception' => ClientException::class,
                ],
            ],
            'server error: token' => [
                [
                    new Response(
                        200,
                        ['Content-Type' => 'application/json'],
                        Loader::load('jwks.json')
                    ),
                    new Response(
                        500,
                        ['Content-Type' => 'application/json'],
                        '{"error":"server_error"}'
                    ),
                ],
                (new ExchangeProperty('dummy'))->addScope('openid'),
                [
                    'exception' => ServerException::class,
                ],
            ],
            'client error: token' => [
                [
                    new Response(
                        200,
                        ['Content-Type' => 'application/json'],
                        Loader::load('jwks.json')
                    ),
                    new Response(
                        400,
                        ['Content-Type' => 'application/json'],
                        '{"error":"invalid_request"}'
                    ),
                ],
                (new ExchangeProperty('dummy'))->addScope('openid'),
                [
                    'exception' => ClientException::class,
                ],
            ],
            'exclude id_token' => [
                [
                    new Response(
                        200,
                        ['Content-Type' => 'application/json'],
                        Loader::load('jwks.json')
                    ),
                    new Response(
                        200,
                        ['Content-Type' => 'application/json'],
                        Json::encode([
                            'access_token'  => 'dummy-access-token',
                            'refresh_token' => 'dummy-refresh-token',
                            'expires_in'    => 600,
                            'scope'         => 'profile',
                        ])
                    ),
                ],
                (new ExchangeProperty('code'))->addScope('openid', 'profile'),
                [
                    'expected' => [
                        'access_token'  => 'dummy-access-token',
                        'refresh_token' => 'dummy-refresh-token',
                        'expires_in'    => 600,
                        'scope'         => 'profile',
                        'id_token'      => null,
                    ],
                ],
            ],
            'success' => [
                [
                    new Response(
                        200,
                        ['Content-Type' => 'application/json'],
                        Loader::load('jwks.json')
                    ),
                    new Response(
                        200,
                        ['Content-Type' => 'application/json'],
                        Json::encode([
                            'access_token'  => 'dummy-access-token',
                            'refresh_token' => 'dummy-refresh-token',
                            'expires_in'    => 600,
                            'scope'         => 'openid profile',
                            'id_token'      => 'dummy-access-token',
                        ])
                    ),
                ],
                (new ExchangeProperty('code'))->addScope('openid', 'profile'),
                [
                    'expected' => [
                        'access_token'  => 'dummy-access-token',
                        'refresh_token' => 'dummy-refresh-token',
                        'expires_in'    => 600,
                        'scope'         => 'openid profile',
                        'id_token'      => 'dummy-access-token',
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider providerRefresh
     * @param array $responses
     * @param RefreshProperty $property
     * @param array $settings
     * @throws InvalidResponseException
     * @throws JsonErrorException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testRefresh(
        array $responses,
        RefreshProperty $property,
        array $settings
    ) {
        if (isset($settings['exception'])) {
            $this->expectException($settings['exception']);
        }
        $client = $this->setupClient();
        $client->setHttpClient(new MockHttpClient($responses));
        $token = $client->refresh($property);
        if (isset($settings['expected'])) {
            $this->assertEquals($settings['expected'], [
                'access_token'  => $token->getAccessToken(),
                'refresh_token' => $token->getRefreshToken(),
                'expires_in'    => $token->getExpiresIn(),
                'scope'         => $token->getScope(),
                'id_token'      => $token->getIdToken(),
            ]);
        }
    }

    /**
     * @return array
     * @throws JsonErrorException
     */
    public function providerRefresh()
    {
        return [
            'empty property' => [
                [
                    new Response(
                        200,
                        ['Content-Type' => 'application/json'],
                        Loader::load('jwks.json')
                    ),
                    new Response(
                        200,
                        ['Content-Type' => 'application/json'],
                        Json::encode([
                            'access_token'  => 'dummy-access-token',
                            'refresh_token' => 'dummy-refresh-token',
                            'expires_in'    => 600,
                            'scope'         => 'openid',
                            'id_token'      => 'dummy-access-token',
                        ])
                    ),
                ],
                new RefreshProperty(''),
                [
                    'exception' => \UnexpectedValueException::class,
                ],
            ],
            'empty scope property' => [
                [
                    new Response(
                        200,
                        ['Content-Type' => 'application/json'],
                        Loader::load('jwks.json')
                    ),
                    new Response(
                        200,
                        ['Content-Type' => 'application/json'],
                        Json::encode([
                            'access_token'  => 'dummy-access-token',
                            'refresh_token' => 'dummy-refresh-token',
                            'expires_in'    => 600,
                            'scope'         => 'openid',
                            'id_token'      => 'dummy-access-token',
                        ])
                    ),
                ],
                new RefreshProperty('success-refresh-token'),
                [
                    'exception' => \UnexpectedValueException::class,
                ],
            ],
            'server error: jwks' => [
                [
                    new Response(
                        500,
                        ['Content-Type' => 'application/json'],
                        'Internal Server Error'
                    ),
                    new Response(
                        200,
                        ['Content-Type' => 'application/json'],
                        Json::encode([
                            'access_token'  => 'dummy-access-token',
                            'refresh_token' => 'dummy-refresh-token',
                            'expires_in'    => 600,
                            'scope'         => 'openid',
                            'id_token'      => 'dummy-access-token',
                        ])
                    ),
                ],
                (new RefreshProperty('dummy'))->addScope('openid'),
                [
                    'exception' => ServerException::class,
                ],
            ],
            'client error: jwks' => [
                [
                    new Response(
                        403,
                        ['Content-Type' => 'application/json'],
                        'Forbidden'
                    ),
                    new Response(
                        200,
                        ['Content-Type' => 'application/json'],
                        Json::encode([
                            'access_token'  => 'dummy-access-token',
                            'refresh_token' => 'dummy-refresh-token',
                            'expires_in'    => 600,
                            'scope'         => 'openid',
                            'id_token'      => 'dummy-access-token',
                        ])
                    ),
                ],
                (new RefreshProperty('dummy'))->addScope('openid'),
                [
                    'exception' => ClientException::class,
                ],
            ],
            'server error: token' => [
                [
                    new Response(
                        200,
                        ['Content-Type' => 'application/json'],
                        Loader::load('jwks.json')
                    ),
                    new Response(
                        500,
                        ['Content-Type' => 'application/json'],
                        '{"error":"server_error"}'
                    ),
                ],
                (new RefreshProperty('dummy'))->addScope('openid'),
                [
                    'exception' => ServerException::class,
                ],
            ],
            'client error: token' => [
                [
                    new Response(
                        200,
                        ['Content-Type' => 'application/json'],
                        Loader::load('jwks.json')
                    ),
                    new Response(
                        400,
                        ['Content-Type' => 'application/json'],
                        '{"error":"invalid_request"}'
                    ),
                ],
                (new RefreshProperty('dummy'))->addScope('openid'),
                [
                    'exception' => ClientException::class,
                ],
            ],
            'exclude id_token' => [
                [
                    new Response(
                        200,
                        ['Content-Type' => 'application/json'],
                        Loader::load('jwks.json')
                    ),
                    new Response(
                        200,
                        ['Content-Type' => 'application/json'],
                        Json::encode([
                            'access_token'  => 'dummy-access-token',
                            'refresh_token' => 'dummy-refresh-token',
                            'expires_in'    => 600,
                            'scope'         => 'profile',
                        ])
                    ),
                ],
                (new RefreshProperty('success-refresh-token'))->addScope(
                    'openid',
                    'profile'
                ),
                [
                    'expected' => [
                        'access_token'  => 'dummy-access-token',
                        'refresh_token' => 'dummy-refresh-token',
                        'expires_in'    => 600,
                        'scope'         => 'profile',
                        'id_token'      => null,
                    ],
                ],
            ],
            'success' => [
                [
                    new Response(
                        200,
                        ['Content-Type' => 'application/json'],
                        Loader::load('jwks.json')
                    ),
                    new Response(
                        200,
                        ['Content-Type' => 'application/json'],
                        Json::encode([
                            'access_token'  => 'dummy-access-token',
                            'refresh_token' => 'dummy-refresh-token',
                            'expires_in'    => 600,
                            'scope'         => 'openid profile',
                            'id_token'      => 'dummy-access-token',
                        ])
                    ),
                ],
                (new RefreshProperty('success-refresh-token'))->addScope(
                    'openid',
                    'profile'
                ),
                [
                    'expected' => [
                        'access_token'  => 'dummy-access-token',
                        'refresh_token' => 'dummy-refresh-token',
                        'expires_in'    => 600,
                        'scope'         => 'openid profile',
                        'id_token'      => 'dummy-access-token',
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider providerGetJwks
     * @param array $responses
     * @param array|null $expected
     * @param string|null $exception
     * @throws InvalidResponseException
     * @throws JsonErrorException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testGetJwks(
        array $responses,
        $expected = null,
        $exception = null
    ) {
        if (!is_null($exception)) {
            $this->expectException($exception);
        }
        $client = $this->setupClient();
        $client->setHttpClient(new MockHttpClient($responses));
        $act = $client->getJwks();
        if (!is_null($expected)) {
            $this->assertEquals($expected, $act);
        }
    }

    public function providerGetJwks()
    {
        return [
            'empty body' => [
                [new Response(200, ['Content-Type' => 'text/plain'], '')],
                null,
                JsonErrorException::class,
            ],
            'invalid body' => [
                [
                    new Response(
                        200,
                        ['Content-Type' => 'application/json'],
                        '{"dummy":"ok"}'
                    ),
                ],
                null,
                InvalidResponseException::class,
            ],
            'invalid status: 400' => [
                [
                    new Response(
                        400,
                        ['Content-Type' => 'application/json'],
                        '{"dummy":"ok"}'
                    ),
                ],
                null,
                ClientException::class,
            ],
            'invalid status: 500' => [
                [
                    new Response(
                        500,
                        ['Content-Type' => 'application/json'],
                        '{"dummy":"ok"}'
                    ),
                ],
                null,
                ServerException::class,
            ],
            'valid' => [
                [
                    new Response(
                        200,
                        ['Content-Type' => 'application/json'],
                        Loader::load('jwks.json')
                    ),
                ],
                Loader::loadJson('jwks.json'),
            ],
        ];
    }

    /**
     * @dataProvider providerUserInfoRequest
     * @param array $responses
     * @param string $accessToken
     * @param array $settings
     * @throws JsonErrorException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testUserInfoRequest(
        array $responses,
        string $accessToken,
        array $settings
    ) {
        if (isset($settings['exception'])) {
            $this->expectException($settings['exception']);
        }
        $client = $this->setupClient();
        $client->setHttpClient(new MockHttpClient($responses));
        $res = $client->userInfoRequest($accessToken);
        if (isset($settings['expected'])) {
            $this->assertEquals($settings['expected'], $res);
        }
    }

    /**
     * @return array
     * @throws JsonErrorException
     */
    public function providerUserInfoRequest()
    {
        return [
            'unauthorized error' => [
                [
                    new Response(
                        401,
                        ['Content-Type' => 'application/json'],
                        Json::encode(['error' => 'unauthorized_error'])
                    )
                ],
                'xxx',
                [
                    'exception' => ClientException::class
                ]
            ],
            'server error' => [
                [
                    new Response(
                        500,
                        ['Content-Type' => 'application/json'],
                        Json::encode(['error' => 'server_error'])
                    )
                ],
                'xxx',
                [
                    'exception' => ServerException::class
                ]
            ],
            'scope: openid' => [
                [
                    new Response(
                        200,
                        ['Content-Type' => 'application/json'],
                        Json::encode(['sub' => '1009332'])
                    )
                ],
                'xxx',
                [
                    'expected' => [
                        'sub' => '1009332'
                    ]
                ]
            ],
            'scope: openid profile' => [
                [
                    new Response(
                        200,
                        ['Content-Type' => 'application/json'],
                        Json::encode(['sub' => '1009332', 'name' => 'dummy'])
                    )
                ],
                'xxx',
                [
                    'expected' => [
                        'sub'  => '1009332',
                        'name' => 'dummy'
                    ]
                ]
            ]
        ];
    }

    public function testRequest()
    {
        $client = $this->setupClient();
        $mockClient = new MockHttpClient([
            new Response(200, ['Content-Type' => 'application/json'], '{"status":"ok"}'),
        ]);
        $client->setHttpClient($mockClient);
        $client->request('GET', 'http://localhost', 'xxx', [
            'headers' => [
                'X-Test-Header' => 'test',
                'Authorization' => 'ccc',
            ]
        ]);
        $container = $mockClient->getContainer();
        $request = $container[0]['request'];
        $headers = $request->getHeaders();
        $this->assertEquals('test', $headers['X-Test-Header'][0]);
        $this->assertEquals('Bearer xxx', $headers['Authorization'][0]);
    }

    /**
     * @return Client
     */
    private function setupClient(): Client
    {
        return new Client($this->metadata, $this->provider);
    }
}
