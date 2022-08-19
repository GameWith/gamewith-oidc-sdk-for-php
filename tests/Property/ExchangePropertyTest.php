<?php

namespace GameWith\Oidc\Tests\Property;

use GameWith\Oidc\ClientMetadata;
use GameWith\Oidc\Constant\GrantType;
use GameWith\Oidc\Property\ExchangeProperty;
use Yoast\PHPUnitPolyfills\TestCases\TestCase;

class ExchangePropertyTest extends TestCase
{
    public function testGetGrantType()
    {
        $property = new ExchangeProperty('code');
        $this->assertEquals(
            GrantType::AUTHORIZATION_CODE,
            $property->getGrantType()
        );
        $property = new ExchangeProperty('code', 'dummy');
        $this->assertEquals('dummy', $property->getGrantType());
    }

    public function testGetCode()
    {
        $code = 'code';
        $property = new ExchangeProperty($code);
        $this->assertEquals($code, $property->getCode());
    }

    public function testGetCodeVerifier()
    {
        $property = new ExchangeProperty('code');
        $this->assertNull($property->getCodeVerifier());
        $property->setCodeVerifier('dummy');
        $this->assertEquals('dummy', $property->getCodeVerifier());
    }

    public function testGetScope()
    {
        $property = new ExchangeProperty('code');
        $this->assertEquals('', $property->getScope());
        $property->addScope('openid')->addScope('openid');
        $this->assertEquals('openid', $property->getScope());
        $property->addScope('profile');
        $this->assertEquals('openid profile', $property->getScope());
    }

    /**
     * @dataProvider providerValid
     * @param $params
     * @param $exception
     */
    public function testValid($params, $exception)
    {
        if (!is_null($exception)) {
            $this->expectException($exception);
        }
        $property = new ExchangeProperty(
            $params['code'],
            $params['grant_type']
        );
        if (isset($params['scope'])) {
            $property->addScope(...$params['scope']);
        }
        if (isset($params['metadata'])) {
            $property->setMetadata($params['metadata']);
        }
        if (isset($params['code_verifier'])) {
            $property->setCodeVerifier($params['code_verifier']);
        }
        $this->assertNull($property->valid());
    }

    public function providerValid()
    {
        return [
            [
                [
                    'code'       => '',
                    'grant_type' => '',
                ],
                \UnexpectedValueException::class,
            ],
            [
                [
                    'code'       => 'code',
                    'grant_type' => '',
                ],
                \UnexpectedValueException::class,
            ],
            [
                [
                    'code'       => 'code',
                    'grant_type' => GrantType::AUTHORIZATION_CODE,
                ],
                \UnexpectedValueException::class,
            ],
            [
                [
                    'code'       => 'code',
                    'grant_type' => GrantType::AUTHORIZATION_CODE,
                    'metadata'   => new ClientMetadata(
                        'a',
                        'b',
                        'http://localhost'
                    ),
                ],
                \UnexpectedValueException::class,
            ],
            [
                [
                    'code'       => 'code',
                    'grant_type' => GrantType::AUTHORIZATION_CODE,
                    'metadata'   => new ClientMetadata(
                        'a',
                        'b',
                        'http://localhost'
                    ),
                    'code_verifier' => '',
                ],
                \UnexpectedValueException::class,
            ],
            [
                [
                    'code'       => 'code',
                    'grant_type' => GrantType::AUTHORIZATION_CODE,
                    'metadata'   => new ClientMetadata(
                        'a',
                        'b',
                        'http://localhost'
                    ),
                    'code_verifier' => 'dummy',
                ],
                \UnexpectedValueException::class,
            ],
            [
                [
                    'code'       => 'code',
                    'grant_type' => GrantType::AUTHORIZATION_CODE,
                    'metadata'   => new ClientMetadata(
                        'a',
                        'b',
                        'http://localhost'
                    ),
                    'code_verifier' => 'dummy',
                    'scope'         => [],
                ],
                \UnexpectedValueException::class,
            ],
            [
                [
                    'code'       => 'code',
                    'grant_type' => GrantType::AUTHORIZATION_CODE,
                    'metadata'   => new ClientMetadata(
                        'a',
                        'b',
                        'http://localhost'
                    ),
                    'code_verifier' => 'dummy',
                    'scope'         => ['alert(1)'],
                ],
                \UnexpectedValueException::class,
            ],
            [
                [
                    'code'       => 'code',
                    'grant_type' => GrantType::AUTHORIZATION_CODE,
                    'metadata'   => new ClientMetadata(
                        'a',
                        'b',
                        'http://localhost'
                    ),
                    'code_verifier' => 'dummy',
                    'scope'         => ['openid', 'alert(1)'],
                ],
                \UnexpectedValueException::class,
            ],
            [
                [
                    'code'       => 'code',
                    'grant_type' => GrantType::AUTHORIZATION_CODE,
                    'metadata'   => new ClientMetadata(
                        'a',
                        'b',
                        'http://localhost'
                    ),
                    'scope' => ['openid'],
                ],
                null,
            ],
            [
                [
                    'code'       => 'code',
                    'grant_type' => GrantType::AUTHORIZATION_CODE,
                    'metadata'   => new ClientMetadata(
                        'a',
                        'b',
                        'http://localhost'
                    ),
                    'code_verifier' => 'dummy',
                    'scope'         => ['openid'],
                ],
                null,
            ],
            [
                [
                    'code'       => 'code',
                    'grant_type' => GrantType::AUTHORIZATION_CODE,
                    'metadata'   => new ClientMetadata(
                        'a',
                        'b',
                        'http://localhost'
                    ),
                    'code_verifier' => 'dummy',
                    'scope'         => ['openid', 'profile'],
                ],
                null,
            ],
        ];
    }

    /**
     * @dataProvider providerParams
     * @param $params
     * @param $expected
     * @param $exception
     */
    public function testParams($params, $expected, $exception)
    {
        if (!is_null($exception)) {
            $this->expectException($exception);
        }
        $property = new ExchangeProperty(
            $params['code'],
            $params['grant_type']
        );
        if (isset($params['scope'])) {
            $property->addScope(...$params['scope']);
        }
        if (isset($params['metadata'])) {
            $property->setMetadata($params['metadata']);
        }
        if (isset($params['code_verifier'])) {
            $property->setCodeVerifier($params['code_verifier']);
        }
        $this->assertEquals($expected, $property->params());
    }

    public function providerParams()
    {
        return [
            [
                [
                    'code'       => '',
                    'grant_type' => '',
                ],
                [],
                \UnexpectedValueException::class,
            ],
            [
                [
                    'code'       => 'code',
                    'grant_type' => '',
                ],
                [],
                \UnexpectedValueException::class,
            ],
            [
                [
                    'code'       => 'code',
                    'grant_type' => GrantType::AUTHORIZATION_CODE,
                ],
                [],
                \UnexpectedValueException::class,
            ],
            [
                [
                    'code'          => 'code',
                    'grant_type'    => GrantType::AUTHORIZATION_CODE,
                    'code_verifier' => 'dummy',
                ],
                [],
                \UnexpectedValueException::class,
            ],
            [
                [
                    'code'       => 'code',
                    'grant_type' => GrantType::AUTHORIZATION_CODE,
                    'scope'      => ['openid'],
                ],
                [],
                \UnexpectedValueException::class,
            ],
            [
                [
                    'code'          => 'code',
                    'grant_type'    => GrantType::AUTHORIZATION_CODE,
                    'code_verifier' => 'dummy',
                    'scope'         => ['openid'],
                ],
                [],
                \UnexpectedValueException::class,
            ],
            [
                [
                    'code'       => 'code',
                    'grant_type' => GrantType::AUTHORIZATION_CODE,
                    'metadata'   => new ClientMetadata(
                        'a',
                        'b',
                        'http://localhost'
                    ),
                    'code_verifier' => 'dummy',
                    'scope'         => ['openid'],
                ],
                [
                    'code'          => 'code',
                    'grant_type'    => GrantType::AUTHORIZATION_CODE,
                    'scope'         => 'openid',
                    'code_verifier' => 'dummy',
                    'client_id'     => 'a',
                    'redirect_uri'  => 'http://localhost',
                ],
                null,
            ],
            [
                [
                    'code'       => 'code',
                    'grant_type' => GrantType::AUTHORIZATION_CODE,
                    'metadata'   => new ClientMetadata(
                        'a',
                        'b',
                        'http://localhost'
                    ),
                    'code_verifier' => 'dummy',
                    'scope'         => ['openid', 'profile'],
                ],
                [
                    'code'          => 'code',
                    'grant_type'    => GrantType::AUTHORIZATION_CODE,
                    'scope'         => 'openid profile',
                    'code_verifier' => 'dummy',
                    'client_id'     => 'a',
                    'redirect_uri'  => 'http://localhost',
                ],
                null,
            ],
            [
                [
                    'code'       => 'code',
                    'grant_type' => GrantType::AUTHORIZATION_CODE,
                    'metadata'   => new ClientMetadata(
                        'a',
                        'b',
                        'http://localhost'
                    ),
                    'code_verifier' => 'dummy',
                    'scope'         => ['openid', 'openid', 'profile'],
                ],
                [
                    'code'          => 'code',
                    'grant_type'    => GrantType::AUTHORIZATION_CODE,
                    'scope'         => 'openid profile',
                    'code_verifier' => 'dummy',
                    'client_id'     => 'a',
                    'redirect_uri'  => 'http://localhost',
                ],
                null,
            ],
        ];
    }
}
