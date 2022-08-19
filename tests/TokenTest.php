<?php

namespace GameWith\Oidc\Tests\Util;

use GameWith\Oidc\ClientMetadata;
use GameWith\Oidc\Exception\InvalidTokenException;
use GameWith\Oidc\Exception\NotFoundException;
use GameWith\Oidc\Provider;
use GameWith\Oidc\Tests\Fixture\Loader;
use GameWith\Oidc\Token;
use Yoast\PHPUnitPolyfills\TestCases\TestCase;

/**
 * Class TokenTest
 * @package GameWith\Oidc\Tests\Util
 */
class TokenTest extends TestCase
{
    private $provider;
    private $metadata;

    public function set_up()
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

    public function testGetAccessToken()
    {
        $token = $this->setupToken([
            'access_token'  => 'access',
            'refresh_token' => 'refresh',
            'expires_in'    => 600,
            'scope'         => 'openid',
            'id_token'      => 'idt',
        ]);
        $this->assertEquals('access', $token->getAccessToken());
    }

    public function testGetRefreshToken()
    {
        $token = $this->setupToken([
            'access_token'  => 'access',
            'refresh_token' => 'refresh',
            'expires_in'    => 600,
            'scope'         => 'openid',
            'id_token'      => 'idt',
        ]);
        $this->assertEquals('refresh', $token->getRefreshToken());
    }

    public function testGetScope()
    {
        $token = $this->setupToken([
            'access_token'  => 'access',
            'refresh_token' => 'refresh',
            'expires_in'    => 600,
            'scope'         => 'openid',
            'id_token'      => 'idt',
        ]);
        $this->assertEquals('openid', $token->getScope());
    }

    public function testGetExpiresIn()
    {
        $token = $this->setupToken([
            'access_token'  => 'access',
            'refresh_token' => 'refresh',
            'expires_in'    => 600,
            'scope'         => 'openid',
            'id_token'      => 'idt',
        ]);
        $this->assertEquals(600, $token->getExpiresIn());
    }

    public function testGetIdToken()
    {
        $token = $this->setupToken([
            'access_token'  => 'access',
            'refresh_token' => 'refresh',
            'expires_in'    => 600,
            'scope'         => 'openid',
            'id_token'      => 'idt',
        ]);
        $this->assertEquals('idt', $token->getIdToken());
        $token = $this->setupToken([
            'access_token'  => 'access',
            'refresh_token' => 'refresh',
            'expires_in'    => 600,
            'scope'         => 'openid',
        ]);
        $this->assertNull($token->getIdToken());
    }

    /**
     * @dataProvider providerParseIdToken
     * @param array $body
     * @param array $jwks
     * @param $exception
     * @param null $nonce
     * @param null $expected
     * @throws InvalidTokenException
     * @throws NotFoundException
     * @throws \GameWith\Oidc\Exception\JsonErrorException
     * @throws \GameWith\Oidc\Exception\Base64Exception
     */
    public function testParseIdToken(
        array $body,
        array $jwks,
        $exception,
        $nonce = null,
        $expected = null
    ) {
        if (!is_null($exception)) {
            $this->expectException($exception);
        }
        $token = $this->setupToken($body, $jwks);
        $act = $token->parseIdToken($nonce);
        if (!is_null($expected)) {
            $this->assertEquals($expected, $act);
        }
    }

    public function providerParseIdToken()
    {
        return [
            'empty_id_token' => [
                [
                    'access_token'  => 'access',
                    'refresh_token' => 'refresh',
                    'expires_in'    => 600,
                    'scope'         => 'openid',
                ],
                Loader::loadJson('jwks.json'),
                \UnexpectedValueException::class,
            ],
            'invalid_id_token_format' => [
                [
                    'access_token'  => 'access',
                    'refresh_token' => 'refresh',
                    'expires_in'    => 600,
                    'scope'         => 'openid',
                    'id_token'      => 'idt',
                ],
                Loader::loadJson('jwks.json'),
                \UnexpectedValueException::class,
            ],
            'not found jwks' => [
                [
                    'access_token'  => 'access',
                    'refresh_token' => 'refresh',
                    'expires_in'    => 600,
                    'scope'         => 'openid',
                    'id_token'      =>
                        'eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCIsImtpZCI6ImR1bW15In0.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6InRlc3QiLCJpYXQiOjAsImV4cCI6MjUzMzcwNzMyNDAwfQ.p_iMMoXAqBV6sXwD8-dnCZvvIDKGvhWLWFaOzY9nbsk8qYnojGI46stgJtlMYy0hdraeGhytB75HiEyrHGTFPBfqWcZErpSkrpcjNKwSZsrjMyz59Qe8bf65o6CFWZfqSFz8DmVwOf2nKcuHZCRhpOmGcHbFDvPJyXjNNBfg2XftFpNHvkwJYQ2_QIzflHPoQ0YiH6babGwp_bnyLAv7HVLxyWkZkFuE1DX0CSWLpKRaLUbMiKwg33AqDDiQOMUTJxabWbEzUavY_xvnDX67tMEwClVIIq6YIYgwKrtn0r_M64Oy9TRGSKkQO-rDcGXkShcRh_AT3alTuEfxY-e50Q',
                ],
                Loader::loadJson('jwks.json'),
                NotFoundException::class,
            ],
            'invalid signature' => [
                [
                    'access_token'  => 'access',
                    'refresh_token' => 'refresh',
                    'expires_in'    => 600,
                    'scope'         => 'openid',
                    'id_token'      =>
                        'eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCIsImtpZCI6Iks0K0tVdTYydWZpVm9CQ1p2Ym9HZEhJVWJtVTlEM3pIIn0.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6InRlc3QiLCJpYXQiOjAsImV4cCI6MjUzMzcwNzMyNDAwfQ.NrWoRZ1iT0oxUAqCGUTXHXx06tASbeACfWm04-2F0eKNL9L8RPxXctm0WVcwyt0cT6YKeVZV3oUJDbkhVoagC5_xo2nQQoz30oPDuxS1D1KwcdBfbP4x49iLWc4O_kBah2gNwBREpuoUQxYoD8Vnzk8IZ5sr2kRmKwqzNP5S1uh1rUUT3knGqsmmnluneiDocBirYbWqV95_kpBbpcNBh9_Z8IAoyQK1s0SMLRXd03brVOwWtnZaFO9GjK6kzAy4lwOMeIZB67ftTZCk-5jJY4jhaX720FQKLMgmMpXmi3UZ9OT_tBdhmv6XG8M8c6PUydjHrCLjoviEVH-7c13SWx',
                ],
                Loader::loadJson('jwks.json'),
                InvalidTokenException::class,
            ],
            'invalid iss' => [
                [
                    'access_token'  => 'access',
                    'refresh_token' => 'refresh',
                    'expires_in'    => 600,
                    'scope'         => 'openid',
                    'id_token'      =>
                        'eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCIsImtpZCI6Iks0K0tVdTYydWZpVm9CQ1p2Ym9HZEhJVWJtVTlEM3pIIn0.eyJzdWIiOiIxMjM0NTY3ODkwIiwiaXNzIjoiaHR0cDovL2xvY2FsaG9zdC9pc3N1ZXJ4IiwiYXVkIjoiYWJjIiwiYXRfaGFzaCI6Im9GWWYxa25OdHJxbmhBVmZCUnV0ZVEiLCJuYW1lIjoidGVzdCIsImlhdCI6MCwiZXhwIjoyNTMzNzA3MzI0MDB9.XCgdgtPRJADTOR8h-YyjCjbvtBZE7EAld7009r64cuWWn20NAeqQSV6bH20ouAXYxl8CTkI2QS6zHj2rhOfkd54Os7YaqALVdkTxSagoYddXe8ofuWgaAvJ5xe0P4TyYbCkO5UN1DHW11-sW_cBKJHtBGMrPk-o_2EFydXTfn4XLxQhfDSqVvQjn1LKk6oWM3biWGJGNIa8184TAaXwIR_hBoZJGQZvlm5mmuy2QBdzZ5m6NZiO0rAJDvphM5aseqOhKGarwI0Lp7OE6zL7os00piVwHzog_WuVQRgdVKZt6gM6ugoUwoEM-rfV1OuvzNA43W11xWWuX9kMLXLMwMg',
                ],
                Loader::loadJson('jwks.json'),
                InvalidTokenException::class,
            ],
            'invalid aud' => [
                [
                    'access_token'  => 'access',
                    'refresh_token' => 'refresh',
                    'expires_in'    => 600,
                    'scope'         => 'openid',
                    'id_token'      =>
                        'eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCIsImtpZCI6Iks0K0tVdTYydWZpVm9CQ1p2Ym9HZEhJVWJtVTlEM3pIIn0.eyJzdWIiOiIxMjM0NTY3ODkwIiwiaXNzIjoiaHR0cDovL2xvY2FsaG9zdC9pc3N1ZXIiLCJhdWQiOiJhYmN4IiwiYXRfaGFzaCI6Im9GWWYxa25OdHJxbmhBVmZCUnV0ZVEiLCJuYW1lIjoidGVzdCIsImlhdCI6MCwiZXhwIjoyNTMzNzA3MzI0MDB9.ljf7faNM4thjxbP5Kwv1bKDtT7nJUVurHtnSWGflvNpK-uGGzaYJGAntsRcUSeYgQJ6ZsKRdkE3F7AZ7Pnf1v3HyHcgOVAM70x1zVNvzZanA5A3O1llyks6qLetkSkM80g8M7bNrArxzhyo_px3ox1PoAnu_qyzv6lHgst48cnwSX2orVWhP-q-4RYExDsR_G1OfZvIC6ClP-4TQMHXDtSO80eZjtlk_I-Ra-O-IMaf3ZUU3lydmww4Fo5_ryXgS6ELAaAqh1qnropa8miHQPVJM1LXmfkj-Y1YH0zmGyCL_nwJBY-DOcoGYW8GmSUGSvSgQbUBm4XeXV-1XwH1s6g',
                ],
                Loader::loadJson('jwks.json'),
                InvalidTokenException::class,
            ],
            'invalid at_hash' => [
                [
                    'access_token'  => 'access',
                    'refresh_token' => 'refresh',
                    'expires_in'    => 600,
                    'scope'         => 'openid',
                    'id_token'      =>
                        'eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCIsImtpZCI6Iks0K0tVdTYydWZpVm9CQ1p2Ym9HZEhJVWJtVTlEM3pIIn0.eyJzdWIiOiIxMjM0NTY3ODkwIiwiaXNzIjoiaHR0cDovL2xvY2FsaG9zdC9pc3N1ZXIiLCJhdWQiOiJhYmMiLCJhdF9oYXNoIjoib0ZZZjFrbk50cnFuaEFWZkJSdXRlUXgiLCJuYW1lIjoidGVzdCIsImlhdCI6MCwiZXhwIjoyNTMzNzA3MzI0MDB9.H-BqQwmtJBpmKQROwhsyxriIU5JtZ5y4gCJDp9ggskluI72Qm5vTqp-0o2ViD26Qxgeve6YILD8EXKD6EZYHjYSrQxxyVIcwvm_oVHe9Ks9dkIX9qKJ1Jj5y0xMnIPAYkwu9YZtCxCvlUbZRu4CChBuhRFL-08ANCnS1LZZ4K3_pN9wazgEgdmJWO_RaZszFW6Z7SXvT1Qm4KdEKIjYtRBqTjNKX7a-hExjKSa_2btWCj7sA-koKrFqMAelRX4sfC9mFF4Z6RTPJPzy5ORXWLYz3-wnQmPhT2mcZoAteZJu4Iv_DcUOHfgPzcpVx-oV66StHoeSyvt6HqxYzVBUBTg',
                ],
                Loader::loadJson('jwks.json'),
                InvalidTokenException::class,
            ],
            'valid minimum token' => [
                [
                    'access_token'  => 'access',
                    'refresh_token' => 'refresh',
                    'expires_in'    => 600,
                    'scope'         => 'openid',
                    'id_token'      =>
                        'eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCIsImtpZCI6Iks0K0tVdTYydWZpVm9CQ1p2Ym9HZEhJVWJtVTlEM3pIIn0.eyJzdWIiOiIxMjM0NTY3ODkwIiwiaXNzIjoiaHR0cDovL2xvY2FsaG9zdC9pc3N1ZXIiLCJhdWQiOiJhYmMiLCJhdF9oYXNoIjoib0ZZZjFrbk50cnFuaEFWZkJSdXRlUSIsIm5hbWUiOiJ0ZXN0IiwiaWF0IjowLCJleHAiOjI1MzM3MDczMjQwMH0.ZND6916mdEGKDASfacevMZKsVw6oQ1SLFcJkzzkNmZZBqgiEeRpaT8MccTLnscUzeV1FqpIIFMfzT-7eI-Du8202ygNBVPdZl2JlS8Y1BWPMqYENvR4-fb6-Ojicd4ksj51pkul5o0nspaoUpUSNOwN6puKLz-Q0g3SzoUrG9m5t8ZYANl-RmdGCu6fG-qfTG_8O8UPJ9hYstf8x9PR8SLOES8do5JU3OOJxaL3YdFN4DFLcI_qbYmr5i1jB_UDTXLxeKAuOj88PORHnblyQpwLKtH7H0Dlk94s7JRh8pNowofIC3PRoIlH_RS-XInDjGq2HJqGwz6lW22WkLRbiIQ',
                ],
                Loader::loadJson('jwks.json'),
                null,
                null,
                [
                    'header' => [
                        'alg' => 'RS256',
                        'typ' => 'JWT',
                        'kid' => 'K4+KUu62ufiVoBCZvboGdHIUbmU9D3zH',
                    ],
                    'payload' => [
                        'sub'     => '1234567890',
                        'iss'     => 'http://localhost/issuer',
                        'aud'     => 'abc',
                        'at_hash' => 'oFYf1knNtrqnhAVfBRuteQ',
                        'name'    => 'test',
                        'iat'     => 0,
                        'exp'     => 253370732400,
                    ],
                ],
            ],
            'nonce arg: null, included nonce on token' => [
                [
                    'access_token'  => 'access',
                    'refresh_token' => 'refresh',
                    'expires_in'    => 600,
                    'scope'         => 'openid',
                    'id_token'      =>
                        'eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCIsImtpZCI6Iks0K0tVdTYydWZpVm9CQ1p2Ym9HZEhJVWJtVTlEM3pIIn0.eyJzdWIiOiIxMjM0NTY3ODkwIiwiaXNzIjoiaHR0cDovL2xvY2FsaG9zdC9pc3N1ZXIiLCJhdWQiOiJhYmMiLCJhdF9oYXNoIjoib0ZZZjFrbk50cnFuaEFWZkJSdXRlUSIsIm5vbmNlIjoiZHVtbXktbm9uY2UiLCJuYW1lIjoidGVzdCIsImlhdCI6MCwiZXhwIjoyNTMzNzA3MzI0MDB9.sfskqb2Hu2c8VMYm-A3dRZSzLt-zdhqgtk7T81pnn7iZas9sXCMNms1XJED4D9Gnfd8gyqtcn2zXRQ5NR3YARj8pSfjRqUxiSXfFpHXt77K4GJNjws04rE5UgWaTr2PBBBpsn8-IUY5CrJYP6GgIdptJh8XGmiOr1XH-p6uJ3efFKlhAQ140_jUYr32Q7QQeXqpE7T56WLKdHLSYiRKZix_Ht5WqjuNJIa5ZFQHSMTyi1sRj0E_5dmlP0OQKHqffHCWduoHuE3kh-DxKeNwlrioQX0Gurw4V96vj_GkynNbQ3uVepv2t4IFHh23XTnp-SoZm-DjyJnto6hUJk8sE8Q',
                ],
                Loader::loadJson('jwks.json'),
                InvalidTokenException::class,
            ],
            'nonce arg: missing, included nonce on token' => [
                [
                    'access_token'  => 'access',
                    'refresh_token' => 'refresh',
                    'expires_in'    => 600,
                    'scope'         => 'openid',
                    'id_token'      =>
                        'eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCIsImtpZCI6Iks0K0tVdTYydWZpVm9CQ1p2Ym9HZEhJVWJtVTlEM3pIIn0.eyJzdWIiOiIxMjM0NTY3ODkwIiwiaXNzIjoiaHR0cDovL2xvY2FsaG9zdC9pc3N1ZXIiLCJhdWQiOiJhYmMiLCJhdF9oYXNoIjoib0ZZZjFrbk50cnFuaEFWZkJSdXRlUSIsIm5vbmNlIjoiZHVtbXktbm9uY2UiLCJuYW1lIjoidGVzdCIsImlhdCI6MCwiZXhwIjoyNTMzNzA3MzI0MDB9.sfskqb2Hu2c8VMYm-A3dRZSzLt-zdhqgtk7T81pnn7iZas9sXCMNms1XJED4D9Gnfd8gyqtcn2zXRQ5NR3YARj8pSfjRqUxiSXfFpHXt77K4GJNjws04rE5UgWaTr2PBBBpsn8-IUY5CrJYP6GgIdptJh8XGmiOr1XH-p6uJ3efFKlhAQ140_jUYr32Q7QQeXqpE7T56WLKdHLSYiRKZix_Ht5WqjuNJIa5ZFQHSMTyi1sRj0E_5dmlP0OQKHqffHCWduoHuE3kh-DxKeNwlrioQX0Gurw4V96vj_GkynNbQ3uVepv2t4IFHh23XTnp-SoZm-DjyJnto6hUJk8sE8Q',
                ],
                Loader::loadJson('jwks.json'),
                InvalidTokenException::class,
                'dummy',
            ],
            'nonce arg: match, included nonce on token' => [
                [
                    'access_token'  => 'access',
                    'refresh_token' => 'refresh',
                    'expires_in'    => 600,
                    'scope'         => 'openid',
                    'id_token'      =>
                        'eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCIsImtpZCI6Iks0K0tVdTYydWZpVm9CQ1p2Ym9HZEhJVWJtVTlEM3pIIn0.eyJzdWIiOiIxMjM0NTY3ODkwIiwiaXNzIjoiaHR0cDovL2xvY2FsaG9zdC9pc3N1ZXIiLCJhdWQiOiJhYmMiLCJhdF9oYXNoIjoib0ZZZjFrbk50cnFuaEFWZkJSdXRlUSIsIm5vbmNlIjoiZHVtbXktbm9uY2UiLCJuYW1lIjoidGVzdCIsImlhdCI6MCwiZXhwIjoyNTMzNzA3MzI0MDB9.sfskqb2Hu2c8VMYm-A3dRZSzLt-zdhqgtk7T81pnn7iZas9sXCMNms1XJED4D9Gnfd8gyqtcn2zXRQ5NR3YARj8pSfjRqUxiSXfFpHXt77K4GJNjws04rE5UgWaTr2PBBBpsn8-IUY5CrJYP6GgIdptJh8XGmiOr1XH-p6uJ3efFKlhAQ140_jUYr32Q7QQeXqpE7T56WLKdHLSYiRKZix_Ht5WqjuNJIa5ZFQHSMTyi1sRj0E_5dmlP0OQKHqffHCWduoHuE3kh-DxKeNwlrioQX0Gurw4V96vj_GkynNbQ3uVepv2t4IFHh23XTnp-SoZm-DjyJnto6hUJk8sE8Q',
                ],
                Loader::loadJson('jwks.json'),
                null,
                'dummy-nonce',
                [
                    'header' => [
                        'alg' => 'RS256',
                        'typ' => 'JWT',
                        'kid' => 'K4+KUu62ufiVoBCZvboGdHIUbmU9D3zH',
                    ],
                    'payload' => [
                        'sub'     => '1234567890',
                        'iss'     => 'http://localhost/issuer',
                        'aud'     => 'abc',
                        'at_hash' => 'oFYf1knNtrqnhAVfBRuteQ',
                        'nonce'   => 'dummy-nonce',
                        'name'    => 'test',
                        'iat'     => 0,
                        'exp'     => 253370732400,
                    ],
                ],
            ],
            'expire auth_time' => [
                [
                    'access_token'  => 'access',
                    'refresh_token' => 'refresh',
                    'expires_in'    => 600,
                    'scope'         => 'openid',
                    'id_token'      =>
                        'eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCIsImtpZCI6Iks0K0tVdTYydWZpVm9CQ1p2Ym9HZEhJVWJtVTlEM3pIIn0.eyJzdWIiOiIxMjM0NTY3ODkwIiwiaXNzIjoiaHR0cDovL2xvY2FsaG9zdC9pc3N1ZXIiLCJhdWQiOiJhYmMiLCJhdF9oYXNoIjoib0ZZZjFrbk50cnFuaEFWZkJSdXRlUSIsImF1dGhfdGltZSI6MCwibmFtZSI6InRlc3QiLCJpYXQiOjAsImV4cCI6MjUzMzcwNzMyNDAwfQ.c_FmuzrAADuqnkOv3QE-2KACISQa8n8_xXy-ZR7pKLeKqoDp5IsZvZntgfnXDmBX0Xu6EVgYRNax-kD8vvd2mQlX57wiXcmj4DUTC9UeX6ev2c1lfIX1_F-2FxldvU_DOTSIc2r7JX7Yszizyl-I5s_z4hn3RB5UW-8QdZ5xVNjHj18jE2Msg4IrblPT0Ogx3ao6pvq8UL0efV-GakZP-nAXEZnKlksNAzpwVWDXC_yG_xIGbEyGcLhu84jP3Th7AW9jkvVhDUGmm-ZrNNFN9dZaXWvRPzl1_DTp62Plqf9S9QENPXCpMtwbwdumMGPDEhafFKroT1Qkaao7DsvA9g',
                ],
                Loader::loadJson('jwks.json'),
                InvalidTokenException::class,
            ],
            'valid auth_time' => [
                [
                    'access_token'  => 'access',
                    'refresh_token' => 'refresh',
                    'expires_in'    => 600,
                    'scope'         => 'openid',
                    'id_token'      =>
                        'eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCIsImtpZCI6Iks0K0tVdTYydWZpVm9CQ1p2Ym9HZEhJVWJtVTlEM3pIIn0.eyJzdWIiOiIxMjM0NTY3ODkwIiwiaXNzIjoiaHR0cDovL2xvY2FsaG9zdC9pc3N1ZXIiLCJhdWQiOiJhYmMiLCJhdF9oYXNoIjoib0ZZZjFrbk50cnFuaEFWZkJSdXRlUSIsImF1dGhfdGltZSI6MjUzMzcwNzMyNDAwLCJuYW1lIjoidGVzdCIsImlhdCI6MCwiZXhwIjoyNTMzNzA3MzI0MDB9.LM-ekU-zHuAjZTaT69JFrrB32Cys_2NznafZnUoa5abH89pCZdPIvxpcVyNPlXmPvBVzIJ2rFdnBJ9JtL4uDPcCMiVESKdohsCJdU9Sibo-ssSdbQAXG6dQfIpHJ5Gms-2au1-I7oDdMcKf0Mm5aHtfkHVgnEgEAAghOJTKZBrU8Tb3Pu8VV5SA5yedg3wqTQQknqhThe3ZABXgy8JLjXNLaNIKVfK0J32f3k0QmjXAMFqmydz70qUBXlh44jACak6WojGQR5iXPawooDRiHIIIW-SJup7e-6ADp6Qw-YGIT_IhIuAVFIQC46Y4astP5yJqGoOQfNGGnX3x7anCYlA',
                ],
                Loader::loadJson('jwks.json'),
                null,
                null,
                [
                    'header' => [
                        'alg' => 'RS256',
                        'typ' => 'JWT',
                        'kid' => 'K4+KUu62ufiVoBCZvboGdHIUbmU9D3zH',
                    ],
                    'payload' => [
                        'sub'       => '1234567890',
                        'iss'       => 'http://localhost/issuer',
                        'aud'       => 'abc',
                        'at_hash'   => 'oFYf1knNtrqnhAVfBRuteQ',
                        'auth_time' => 253370732400,
                        'name'      => 'test',
                        'iat'       => 0,
                        'exp'       => 253370732400,
                    ],
                ],
            ],
            'valid' => [
                [
                    'access_token'  => 'access',
                    'refresh_token' => 'refresh',
                    'expires_in'    => 600,
                    'scope'         => 'openid',
                    'id_token'      => 'eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCIsImtpZCI6Iks0K0tVdTYydWZpVm9CQ1p2Ym9HZEhJVWJtVTlEM3pIIn0.eyJzdWIiOiIxMjM0NTY3ODkwIiwiaXNzIjoiaHR0cDovL2xvY2FsaG9zdC9pc3N1ZXIiLCJhdWQiOiJhYmMiLCJhdF9oYXNoIjoib0ZZZjFrbk50cnFuaEFWZkJSdXRlUSIsIm5vbmNlIjoiZHVtbXktbm9uY2UiLCJhdXRoX3RpbWUiOjI1MzM3MDczMjQwMCwibmFtZSI6InRlc3QiLCJpYXQiOjAsImV4cCI6MjUzMzcwNzMyNDAwfQ.dLTpUDwRe-8wrdDin8ApDFoLmjvwm_5_IQ8YStOeS4VUJPVU2AlTAzYW5Fq6IucCm7gkxLHO7wJpoDXlundGVFtuyO9NQg14yIjV2QoVEqGm4cvPxWUnwqhoBRcXjUfYqHDhy2zpcmpLawhwH4cYVbZvyouPfu9LsdHc2MXNwTm4Hf0ZcdmmbBeF1SR-w5BqnEqS5wIN2xk6r8TC9-eSWZRJipgIElbRr0wYUDivq5GoD76ctzbiigoSzr8EYHYyPuPPxE-58pDxkeBrndbNKGTig2D3H5yw5d0a5B1COEstwltFxPl71FdT_Nk31inKS80EPPTIDehCs-G4Js7zng',
                ],
                Loader::loadJson('jwks.json'),
                null,
                'dummy-nonce',
                [
                    'header' => [
                        'alg' => 'RS256',
                        'typ' => 'JWT',
                        'kid' => 'K4+KUu62ufiVoBCZvboGdHIUbmU9D3zH',
                    ],
                    'payload' => [
                        'sub'       => '1234567890',
                        'iss'       => 'http://localhost/issuer',
                        'aud'       => 'abc',
                        'at_hash'   => 'oFYf1knNtrqnhAVfBRuteQ',
                        'nonce'     => 'dummy-nonce',
                        'auth_time' => 253370732400,
                        'name'      => 'test',
                        'iat'       => 0,
                        'exp'       => 253370732400,
                    ],
                ],
            ]
        ];
    }

    private function setupToken(array $body, array $jwks = [])
    {
        return new Token($body, $jwks, $this->provider, $this->metadata);
    }
}
