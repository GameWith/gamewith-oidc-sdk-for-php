<?php

namespace GameWith\Oidc\Tests\Jwx;

use GameWith\Oidc\Exception\NotFoundException;
use GameWith\Oidc\Jwx\JwkSet;
use Yoast\PHPUnitPolyfills\TestCases\TestCase;

/**
 * Class JwkSetTest
 * @package GameWith\Oidc\Tests\Jwx
 */
class JwkSetTest extends TestCase
{
    /**
     * @dataProvider providerFind
     * @param $jwks
     * @param $header
     * @param $exception
     * @throws \GameWith\Oidc\Exception\NotFoundException
     */
    public function testFind($jwks, $header, $exception)
    {
        if (!is_null($exception)) {
            $this->expectException($exception);
        }
        $jwk = JwkSet::find($jwks, $header, $exception);
        $this->assertEquals($header['kid'], $jwk->getKeyId());
    }

    public function providerFind()
    {
        return [
            [[], [], \UnexpectedValueException::class],
            [
                [
                    'dummy' => [],
                ],
                [],
                \UnexpectedValueException::class,
            ],
            [
                [
                    'keys' => [],
                ],
                [],
                \UnexpectedValueException::class,
            ],
            [
                [
                    'keys' => [],
                ],
                [
                    'dummy' => '',
                ],
                \UnexpectedValueException::class,
            ],
            [
                [
                    'keys' => [],
                ],
                [
                    'kid' => '',
                ],
                \UnexpectedValueException::class,
            ],
            [
                [
                    'keys' => [],
                ],
                [
                    'kid' => 'dummy',
                ],
                NotFoundException::class,
            ],
            [
                [
                    'keys' => [
                        [
                            'kid' => 'dummy',
                        ],
                    ],
                ],
                [
                    'kid' => 'dummy2',
                ],
                NotFoundException::class,
            ],
            [
                [
                    'keys' => [
                        [
                            'kid' => 'dummy',
                        ],
                        [
                            'kid' => 'dummy2',
                        ],
                    ],
                ],
                [
                    'kid' => 'dummy2',
                ],
                \UnexpectedValueException::class,
            ],
            [
                [
                    'keys' => [
                        [
                            'kid' => 'dummy',
                            'kty' => 'RSA',
                        ],
                    ],
                ],
                [
                    'kid' => 'dummy',
                ],
                \UnexpectedValueException::class,
            ],
            [
                [
                    'keys' => [
                        [
                            'kid' => 'dummy',
                            'kty' => 'RSA',
                            'e'   => 'AQAB',
                        ],
                    ],
                ],
                [
                    'kid' => 'dummy',
                ],
                \UnexpectedValueException::class,
            ],
            [
                [
                    'keys' => [
                        [
                            'kid' => 'dummy',
                            'kty' => 'RSA',
                            'n'   => '...',
                        ],
                    ],
                ],
                [
                    'kid' => 'dummy',
                ],
                \UnexpectedValueException::class,
            ],
            [
                [
                    'keys' => [
                        [
                            'kid' => 'dummy',
                            'kty' => 'RSA',
                            'use' => 'sig',
                            'e'   => 'AQAB',
                            'n'   => '...',
                        ],
                        [
                            'kid' => 'dummy2',
                            'kty' => 'EC',
                            'use' => 'sig',
                            'x'   => '...',
                            'y'   => '...',
                            'crv' => 'P-521',
                        ],
                    ],
                ],
                [
                    'kid' => 'dummy2',
                ],
                \UnexpectedValueException::class,
            ],
            [
                [
                    'keys' => [
                        [
                            'kid' => 'dummy',
                            'kty' => 'RSA',
                            'use' => 'sig',
                            'e'   => 'AQAB',
                            'n'   => '...',
                        ],
                        [
                            'kid' => 'dummy2',
                            'kty' => 'EC',
                            'use' => 'sig',
                            'x'   => '...',
                            'y'   => '...',
                            'crv' => 'P-521',
                        ],
                    ],
                ],
                [
                    'kid' => 'dummy',
                ],
                null,
            ],
        ];
    }
}
