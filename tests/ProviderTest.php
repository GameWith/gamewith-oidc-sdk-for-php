<?php

namespace GameWith\Oidc\Tests;

use GameWith\Oidc\Provider;
use Yoast\PHPUnitPolyfills\TestCases\TestCase;

/**
 * Class ProviderTest
 * @package GameWith\Oidc\Tests
 */
class ProviderTest extends TestCase
{
    /**
     * @dataProvider providerConstructorFailure
     */
    public function testConstructorFailure($attributes)
    {
        $this->expectException(\UnexpectedValueException::class);
        new Provider($attributes);
    }

    public function providerConstructorFailure()
    {
        return [
            [
                []
            ],
            [
                [
                    'issuer'                 => '',
                    'authorization_endpoint' => '',
                    'token_endpoint'         => '',
                    'jwks_endpoint'          => '',
                    'userinfo_endpoint'      => '',
                ]
            ],
            [
                [
                    'issuer'  => 'http://localhost',
                ]
            ],
            [
                [
                    'issuer'                 => 'http://localhost',
                    'authorization_endpoint' => 'http://localhost/authorize',
                ]
            ],
            [
                [
                    'issuer'                 => 'http://localhost',
                    'authorization_endpoint' => 'http://localhost/authorize',
                    'token_endpoint'         => 'http://localhost/token',
                ]
            ],
            [
                [
                    'issuer'                 => 'http://localhost',
                    'authorization_endpoint' => 'http://localhost/authorize',
                    'token_endpoint'         => 'http://localhost/token',
                    'userinfo_endpoint'      => 'http://localhost/userinfo',
                ]
            ],
            [
                [
                    'issuer'                 => 'http://localhost',
                    'authorization_endpoint' => 'http://localhost/authorize',
                    'token_endpoint'         => 'http://localhost/token',
                    'jwks_endpoint'          => 'http://localhost/.well-known/jwks.json',
                ]
            ]
        ];
    }

    public function testGetIssuer()
    {
        $provider = new Provider([
            'issuer'                 => 'http://localhost',
            'authorization_endpoint' => 'http://localhost/authorize',
            'token_endpoint'         => 'http://localhost/token',
            'userinfo_endpoint'      => 'http://localhost/userinfo',
            'jwks_endpoint'          => 'http://localhost/.well-known/jwks.json',
        ]);
        $this->assertEquals('http://localhost', $provider->getIssuer());
    }

    public function testGetAuthorizationEndpoint()
    {
        $provider = new Provider([
            'issuer'                 => 'http://localhost',
            'authorization_endpoint' => 'http://localhost/authorize',
            'token_endpoint'         => 'http://localhost/token',
            'userinfo_endpoint'      => 'http://localhost/userinfo',
            'jwks_endpoint'          => 'http://localhost/.well-known/jwks.json',
        ]);
        $this->assertEquals('http://localhost/authorize', $provider->getAuthorizationEndpoint());
    }

    public function testGetTokenEndpoint()
    {
        $provider = new Provider([
            'issuer'                 => 'http://localhost',
            'authorization_endpoint' => 'http://localhost/authorize',
            'token_endpoint'         => 'http://localhost/token',
            'userinfo_endpoint'      => 'http://localhost/userinfo',
            'jwks_endpoint'          => 'http://localhost/.well-known/jwks.json',
        ]);
        $this->assertEquals('http://localhost/token', $provider->getTokenEndpoint());
    }

    public function testGetUserInfoEndpoint()
    {
        $provider = new Provider([
            'issuer'                 => 'http://localhost',
            'authorization_endpoint' => 'http://localhost/authorize',
            'token_endpoint'         => 'http://localhost/token',
            'userinfo_endpoint'      => 'http://localhost/userinfo',
            'jwks_endpoint'          => 'http://localhost/.well-known/jwks.json',
        ]);
        $this->assertEquals('http://localhost/userinfo', $provider->getUserInfoEndpoint());
    }

    public function testGetJwksEndpoint()
    {
        $provider = new Provider([
            'issuer'                 => 'http://localhost',
            'authorization_endpoint' => 'http://localhost/authorize',
            'token_endpoint'         => 'http://localhost/token',
            'userinfo_endpoint'      => 'http://localhost/userinfo',
            'jwks_endpoint'          => 'http://localhost/.well-known/jwks.json',
        ]);
        $this->assertEquals('http://localhost/.well-known/jwks.json', $provider->getJwksEndpoint());
    }
}
