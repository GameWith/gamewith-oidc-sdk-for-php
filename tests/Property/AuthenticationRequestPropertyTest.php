<?php

namespace GameWith\Oidc\Tests\Property;

use GameWith\Oidc\ClientMetadata;
use GameWith\Oidc\Constant\ResponseType;
use GameWith\Oidc\Property\AuthenticationRequestProperty;
use GameWith\Oidc\Util\Pkce;
use GameWith\Oidc\Util\Random;
use Yoast\PHPUnitPolyfills\TestCases\TestCase;

/**
 * Class AuthenticationRequestPropertyTest
 * @package GameWith\Oidc\Tests\Property
 */
class AuthenticationRequestPropertyTest extends TestCase
{
    public function testValid()
    {
        $property = new AuthenticationRequestProperty();
        $property->setMetadata(
            new ClientMetadata('a', 'b', 'http://localhost')
        );
        $property->addScope('openid');
        $this->assertNull($property->valid());
    }

    /**
     * @dataProvider providerValidFailure
     * @param $params
     */
    public function testValidFailure($params, $expectionClass)
    {
        $this->expectException($expectionClass);
        $property = new AuthenticationRequestProperty($params['response_type']);
        $property->addScope(...$params['scopes']);
        if (!empty($params['metadata'])) {
            $property->setMetadata($params['metadata']);
        }
        $property->valid();
    }

    /**
     * @return array
     */
    public function providerValidFailure()
    {
        return [
            [
                [
                    'response_type' => '',
                    'metadata'      => null,
                    'scopes'        => [],
                ],
                \UnexpectedValueException::class,
            ],
            [
                [
                    'response_type' => ResponseType::CODE,
                    'metadata'      => null,
                    'scopes'        => [],
                ],
                \UnexpectedValueException::class,
            ],
            [
                [
                    'response_type' => ResponseType::CODE,
                    'metadata'      => new ClientMetadata('a', 'b', 'c'),
                    'scopes'        => [],
                ],
                \UnexpectedValueException::class,
            ],
            [
                [
                    'response_type' => '',
                    'metadata'      => new ClientMetadata('a', 'b', 'c'),
                    'scopes'        => [],
                ],
                \UnexpectedValueException::class,
            ],
            [
                [
                    'response_type' => '',
                    'metadata'      => new ClientMetadata('a', 'b', 'c'),
                    'scopes'        => ['openid'],
                ],
                \UnexpectedValueException::class,
            ],
            [
                [
                    'response_type' => ResponseType::CODE,
                    'metadata'      => null,
                    'scopes'        => ['openid'],
                ],
                \UnexpectedValueException::class,
            ],
            [
                [
                    'response_type' => ResponseType::CODE,
                    'metadata'      => new ClientMetadata('a', 'b', 'c'),
                    'scopes'        => ['alert(1)'],
                ],
                \UnexpectedValueException::class,
            ],
        ];
    }

    public function testGetResponseType()
    {
        $property = new AuthenticationRequestProperty();
        $this->assertEquals(ResponseType::CODE, $property->getResponseType());
        $property = new AuthenticationRequestProperty('dummy');
        $this->assertEquals('dummy', $property->getResponseType());
    }

    /**
     * @covers AuthenticationRequestProperty::addScope()
     */
    public function testGetScope()
    {
        $property = new AuthenticationRequestProperty();
        $this->assertEquals('', $property->getScope());
        $property->addScope('openid')->addScope('openid');
        $this->assertEquals('openid', $property->getScope());
        $property->addScope('profile');
        $this->assertEquals('openid profile', $property->getScope());
    }

    public function testGetState()
    {
        $property = new AuthenticationRequestProperty();
        $this->assertNull($property->getState());
        $expected = Random::str();
        $property->setState($expected);
        $this->assertEquals($expected, $property->getState());
    }

    public function testGetMaxAge()
    {
        $property = new AuthenticationRequestProperty();
        $this->assertNull($property->getMaxAge());
        $expected = 100;
        $property->setMaxAge($expected);
        $this->assertEquals($expected, $property->getMaxAge());
    }

    public function testGetNonce()
    {
        $property = new AuthenticationRequestProperty();
        $this->assertNull($property->getNonce());
        $expected = Random::str();
        $property->setNonce($expected);
        $this->assertEquals($expected, $property->getNonce());
    }

    public function testGetCodeChallenge()
    {
        $property = new AuthenticationRequestProperty();
        $this->assertNull($property->getCodeChallenge());
        $expected = Pkce::createCodeChallenge(Pkce::generateCodeVerifier());
        $property->setCodeChallenge($expected);
        $this->assertEquals($expected, $property->getCodeChallenge());
    }

    public function testParams()
    {
        $property = new AuthenticationRequestProperty();
        $property->setMetadata(
            new ClientMetadata('a', 'b', 'http://localhost')
        );
        $property->addScope('openid');
        $this->assertEquals(
            [
                'response_type' => ResponseType::CODE,
                'redirect_uri'  => 'http://localhost',
                'client_id'     => 'a',
                'scope'         => 'openid',
            ],
            $property->params()
        );
        $property = new AuthenticationRequestProperty();
        $property->setMetadata(
            new ClientMetadata('a', 'b', 'http://localhost')
        );
        $property->addScope('openid');
        $property->setState(Random::str());
        $this->assertEquals(
            [
                'response_type' => ResponseType::CODE,
                'redirect_uri'  => 'http://localhost',
                'client_id'     => 'a',
                'scope'         => 'openid',
                'state'         => $property->getState(),
            ],
            $property->params()
        );
        $property = new AuthenticationRequestProperty();
        $property->setMetadata(
            new ClientMetadata('a', 'b', 'http://localhost')
        );
        $property->addScope('openid');
        $property->setState(Random::str());
        $property->setMaxAge(100);
        $this->assertEquals(
            [
                'response_type' => ResponseType::CODE,
                'redirect_uri'  => 'http://localhost',
                'client_id'     => 'a',
                'scope'         => 'openid',
                'state'         => $property->getState(),
                'max_age'       => 100,
            ],
            $property->params()
        );
        $property = new AuthenticationRequestProperty();
        $property->setMetadata(
            new ClientMetadata('a', 'b', 'http://localhost')
        );
        $property->addScope('openid');
        $property->setState(Random::str());
        $property->setMaxAge(100);
        $property->setNonce(Random::str());
        $this->assertEquals(
            [
                'response_type' => ResponseType::CODE,
                'redirect_uri'  => 'http://localhost',
                'client_id'     => 'a',
                'scope'         => 'openid',
                'state'         => $property->getState(),
                'max_age'       => 100,
                'nonce'         => $property->getNonce(),
            ],
            $property->params()
        );
        $property = new AuthenticationRequestProperty();
        $property->setMetadata(
            new ClientMetadata('a', 'b', 'http://localhost')
        );
        $property->addScope('openid');
        $property->setState(Random::str());
        $property->setMaxAge(100);
        $property->setNonce(Random::str());
        $property->setCodeChallenge(
            Pkce::createCodeChallenge(Pkce::generateCodeVerifier())
        );
        $this->assertEquals(
            [
                'response_type'  => ResponseType::CODE,
                'redirect_uri'   => 'http://localhost',
                'client_id'      => 'a',
                'scope'          => 'openid',
                'state'          => $property->getState(),
                'max_age'        => $property->getMaxAge(),
                'nonce'          => $property->getNonce(),
                'code_challenge' => $property->getCodeChallenge(),
            ],
            $property->params()
        );
    }
}
