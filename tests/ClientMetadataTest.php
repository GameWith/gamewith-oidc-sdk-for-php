<?php

namespace GameWith\Oidc\Tests;

use GameWith\Oidc\ClientMetadata;
use Yoast\PHPUnitPolyfills\TestCases\TestCase;

/**
 * Class ClientMetadataTest
 * @package GameWith\Oidc\Tests
 */
class ClientMetadataTest extends TestCase
{
    /**
     * @dataProvider providerConstructorFailure
     */
    public function testConstructorFailure($clientId, $clientSecret, $redirectUri)
    {
        $this->expectException(\UnexpectedValueException::class);
        new ClientMetadata($clientId, $clientSecret, $redirectUri);
    }

    public function providerConstructorFailure()
    {
        return [
            ['', '', ''],
            ['a', '', ''],
            ['', 'b', ''],
            ['', '', 'http://localhost'],
            ['a', 'b', ''],
            ['a', '', 'http://localhost'],
            ['', 'b', 'http://localhost'],
        ];
    }

    public function testGetClientId()
    {
        $clientId = 'a';
        $clientMetadata = new ClientMetadata($clientId, 'b', 'http://localhost');
        $this->assertEquals($clientId, $clientMetadata->getClientId());
    }

    public function testGetClientSecret()
    {
        $clientSecret = 'b';
        $clientMetadata = new ClientMetadata('a', $clientSecret, 'http://localhost');
        $this->assertEquals($clientSecret, $clientMetadata->getClientSecret());
    }

    public function testGetRedirectUri()
    {
        $redirectUri = 'http://localhost';
        $clientMetadata = new ClientMetadata('a', 'b', $redirectUri);
        $this->assertEquals($redirectUri, $clientMetadata->getRedirectUri());
    }

    public function testGetAuthorization()
    {
        $clientId = 'a';
        $clientSecret = 'b';
        $redirectUri = 'http://localhost';
        $clientMetadata = new ClientMetadata($clientId, $clientSecret, $redirectUri);
        $this->assertEquals(
            sprintf("Basic %s", $clientSecret),
            $clientMetadata->getAuthorization()
        );
    }
}
