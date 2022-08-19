<?php

namespace GameWith\Oidc\Tests\Jwx;

use GameWith\Oidc\Jwx\Jwk;
use GameWith\Oidc\Tests\Fixture\Loader;
use Yoast\PHPUnitPolyfills\TestCases\TestCase;

class JwkTest extends TestCase
{
    /**
     * @dataProvider providerToPublicKey
     */
    public function testToPublicKey($expected, $jwks, $equal)
    {
        $jwk = new Jwk($jwks['keys'][0]);
        $pubKey = $jwk->toPublicKey();
        $actual = str_replace(["\r\n", "\r"], "\n", (string) $pubKey);
        if ($equal) {
            $this->assertEquals($expected, $actual);
        } else {
            $this->assertNotEquals($expected, $actual);
        }
    }

    public function providerToPublicKey()
    {
        return [
            [
                Loader::load('test1_rsa.pub'),
                Loader::loadJson('jwks.json'),
                true,
            ],
            [
                Loader::load('test2_rsa.pub'),
                Loader::loadJson('jwks.json'),
                false,
            ],
        ];
    }
}
