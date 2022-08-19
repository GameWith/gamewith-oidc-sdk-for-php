<?php

namespace GameWith\Oidc\Tests\Property;

use GameWith\Oidc\ClientMetadata;
use GameWith\Oidc\Constant\GrantType;
use GameWith\Oidc\Property\RefreshProperty;
use Yoast\PHPUnitPolyfills\TestCases\TestCase;

/**
 * Class RefreshPropertyTest
 * @package GameWith\Oidc\Tests\Property
 */
class RefreshPropertyTest extends TestCase
{
    public function testGetRefreshToken()
    {
        $property = new RefreshProperty('a');
        $this->assertEquals('a', $property->getRefreshToken());
    }

    public function testGetScope()
    {
        $property = new RefreshProperty('');
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
        $property = new RefreshProperty($params['refresh_token']);
        if (isset($params['metadata'])) {
            $property->setMetadata($params['metadata']);
        }
        if (isset($params['scope'])) {
            $property->addScope(...$params['scope']);
        }
        $this->assertNull($property->valid());
    }

    /**
     * @return array
     */
    public function providerValid()
    {
        return [
            [
                [
                    'refresh_token' => '',
                ],
                \UnexpectedValueException::class,
            ],
            [
                [
                    'refresh_token' => 'a',
                ],
                \UnexpectedValueException::class,
            ],
            [
                [
                    'refresh_token' => '',
                    'scope'         => ['openid'],
                ],
                \UnexpectedValueException::class,
            ],
            [
                [
                    'refresh_token' => 'a',
                    'scope'         => ['alert(1)'],
                ],
                \UnexpectedValueException::class,
            ],
            [
                [
                    'refresh_token' => 'a',
                    'scope'         => ['openid'],
                ],
                \UnexpectedValueException::class,
            ],
            [
                [
                    'refresh_token' => 'a',
                    'scope'         => ['openid', 'alert(1)'],
                ],
                \UnexpectedValueException::class,
            ],
            [
                [
                    'refresh_token' => '',
                    'metadata'      => new ClientMetadata(
                        'a',
                        'b',
                        'http://localhost'
                    ),
                ],
                \UnexpectedValueException::class,
            ],
            [
                [
                    'refresh_token' => 'a',
                    'metadata'      => new ClientMetadata(
                        'a',
                        'b',
                        'http://localhost'
                    ),
                ],
                \UnexpectedValueException::class,
            ],
            [
                [
                    'refresh_token' => '',
                    'scope'         => ['openid'],
                    'metadata'      => new ClientMetadata(
                        'a',
                        'b',
                        'http://localhost'
                    ),
                ],
                \UnexpectedValueException::class,
            ],
            [
                [
                    'refresh_token' => 'a',
                    'scope'         => ['alert(1)'],
                    'metadata'      => new ClientMetadata(
                        'a',
                        'b',
                        'http://localhost'
                    ),
                ],
                \UnexpectedValueException::class,
            ],
            [
                [
                    'refresh_token' => 'a',
                    'scope'         => ['openid '],
                    'metadata'      => new ClientMetadata(
                        'a',
                        'b',
                        'http://localhost'
                    ),
                ],
                \UnexpectedValueException::class,
            ],
            [
                [
                    'refresh_token' => 'a',
                    'scope'         => ['openid'],
                    'metadata'      => new ClientMetadata(
                        'a',
                        'b',
                        'http://localhost'
                    ),
                ],
                null,
            ],
            [
                [
                    'refresh_token' => 'a',
                    'scope'         => ['openid', 'openid'],
                    'metadata'      => new ClientMetadata(
                        'a',
                        'b',
                        'http://localhost'
                    ),
                ],
                null,
            ],
            [
                [
                    'refresh_token' => 'a',
                    'scope'         => ['openid', 'profile'],
                    'metadata'      => new ClientMetadata(
                        'a',
                        'b',
                        'http://localhost'
                    ),
                ],
                null,
            ],
        ];
    }

    /**
     * @dataProvider providerParams
     * @param $params
     * @param $expected
     * @param $err
     */
    public function testParams($params, $expected, $err = [])
    {
        if (isset($err['exception'])) {
            $this->expectException($err['exception']);
        }
        if (isset($err['error'])) {
            $this->expectException('Error');
        }
        $property = new RefreshProperty($params['refresh_token']);
        if (isset($params['scope'])) {
            $property->addScope(...$params['scope']);
        }
        if (isset($params['metadata'])) {
            $property->setMetadata($params['metadata']);
        }
        $this->assertEquals($expected, $property->params());
    }

    /**
     * @return array
     */
    public function providerParams()
    {
        return [
            [
                [
                    'refresh_token' => '',
                ],
                [],
                [
                    'exception' => \UnexpectedValueException::class,
                ],
            ],
            [
                [
                    'refresh_token' => 'a',
                ],
                [],
                [
                    'exception' => \UnexpectedValueException::class,
                ],
            ],
            [
                [
                    'refresh_token' => '',
                    'scope'         => ['openid'],
                ],
                [],
                [
                    'exception' => \UnexpectedValueException::class,
                ],
            ],
            [
                [
                    'refresh_token' => 'a',
                    'scope'         => ['?redirect_url=openid'],
                ],
                [],
                [
                    'exception' => \UnexpectedValueException::class,
                ],
            ],
            [
                [
                    'refresh_token' => 'a',
                    'scope'         => ['openid'],
                ],
                [],
                [
                    'exception' => \UnexpectedValueException::class,
                ],
            ],
            [
                [
                    'refresh_token' => 'a',
                    'scope'         => ['openid', 'alert(1)'],
                ],
                [],
                [
                    'exception' => \UnexpectedValueException::class,
                ],
            ],
            [
                [
                    'refresh_token' => 'a',
                    'scope'         => ['alert(1)'],
                    'metadata'      => new ClientMetadata(
                        'a',
                        'b',
                        'http://localhost'
                    ),
                ],
                [],
                [
                    'exception' => \UnexpectedValueException::class,
                ],
            ],
            [
                [
                    'refresh_token' => 'a',
                    'scope'         => ['openid alert(1)'],
                    'metadata'      => new ClientMetadata(
                        'a',
                        'b',
                        'http://localhost'
                    ),
                ],
                [],
                [
                    'exception' => \UnexpectedValueException::class,
                ],
            ],
            [
                [
                    'refresh_token' => '',
                    'metadata'      => new ClientMetadata(
                        'a',
                        'b',
                        'http://localhost'
                    ),
                ],
                [
                    'client_id'     => 'a',
                    'client_secret' => 'b',
                    'grant_type'    => GrantType::REFRESH_TOKEN,
                    'refresh_token' => '',
                    'scope'         => '',
                ],
            ],
            [
                [
                    'refresh_token' => 'a',
                    'metadata'      => new ClientMetadata(
                        'a',
                        'b',
                        'http://localhost'
                    ),
                ],
                [
                    'client_id'     => 'a',
                    'client_secret' => 'b',
                    'grant_type'    => GrantType::REFRESH_TOKEN,
                    'refresh_token' => 'a',
                    'scope'         => '',
                ],
            ],
            [
                [
                    'refresh_token' => '',
                    'scope'         => ['openid'],
                    'metadata'      => new ClientMetadata(
                        'a',
                        'b',
                        'http://localhost'
                    ),
                ],
                [
                    'client_id'     => 'a',
                    'client_secret' => 'b',
                    'grant_type'    => GrantType::REFRESH_TOKEN,
                    'refresh_token' => '',
                    'scope'         => 'openid',
                ],
            ],
            [
                [
                    'refresh_token' => 'a',
                    'scope'         => ['openid'],
                    'metadata'      => new ClientMetadata(
                        'a',
                        'b',
                        'http://localhost'
                    ),
                ],
                [
                    'client_id'     => 'a',
                    'client_secret' => 'b',
                    'grant_type'    => GrantType::REFRESH_TOKEN,
                    'refresh_token' => 'a',
                    'scope'         => 'openid',
                ],
            ],
            [
                [
                    'refresh_token' => 'a',
                    'scope'         => ['openid', 'openid'],
                    'metadata'      => new ClientMetadata(
                        'a',
                        'b',
                        'http://localhost'
                    ),
                ],
                [
                    'client_id'     => 'a',
                    'client_secret' => 'b',
                    'grant_type'    => GrantType::REFRESH_TOKEN,
                    'refresh_token' => 'a',
                    'scope'         => 'openid',
                ],
            ],
            [
                [
                    'refresh_token' => 'a',
                    'scope'         => ['openid', 'profile'],
                    'metadata'      => new ClientMetadata(
                        'a',
                        'b',
                        'http://localhost'
                    ),
                ],
                [
                    'client_id'     => 'a',
                    'client_secret' => 'b',
                    'grant_type'    => GrantType::REFRESH_TOKEN,
                    'refresh_token' => 'a',
                    'scope'         => 'openid profile',
                ],
            ],
        ];
    }
}
