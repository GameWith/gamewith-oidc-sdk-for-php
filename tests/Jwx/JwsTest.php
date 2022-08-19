<?php

namespace GameWith\Oidc\Tests\Jwx;

use GameWith\Oidc\Exception\Base64Exception;
use GameWith\Oidc\Exception\InvalidTokenException;
use GameWith\Oidc\Exception\JsonErrorException;
use GameWith\Oidc\Jwx\Jws;
use GameWith\Oidc\Tests\Fixture\Loader;
use Yoast\PHPUnitPolyfills\TestCases\TestCase;

class JwsTest extends TestCase
{
    /**
     * @dataProvider providerVerify
     * @covers \GameWith\Oidc\Jwx\Jws::verifyBySplitToken()
     * @param $expected
     * @param $token
     * @param $pubKey
     * @param $exception
     * @throws InvalidTokenException
     * @throws JsonErrorException
     * @throws \GameWith\Oidc\Exception\Base64Exception
     */
    public function testVerify($expected, $token, $pubKey, $exception)
    {
        if (!is_null($exception)) {
            $this->expectException($exception);
        }
        $jws = new Jws();
        $this->assertEquals($expected, $jws->verify($token, $pubKey));
    }

    public function providerVerify()
    {
        return [
            'empty' => [
                false,
                '',
                Loader::loadPublicKey('test1_rsa.pub'),
                \UnexpectedValueException::class,
            ],
            'invalid token format' => [
                false,
                'x.x.x',
                Loader::loadPublicKey('test1_rsa.pub'),
                Base64Exception::class,
            ],
            'unsupported alg: HS256' => [
                false,
                'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6ImhvZ2UiLCJ0ZXN0IjoieWVzIiwiaWF0IjoxNTE2MjM5MDIyfQ.iuCtX6SDCja19f_Y2YnMj8w0-1x1fWDe8coqjmy93CI',
                Loader::loadPublicKey('test1_rsa.pub'),
                \UnexpectedValueException::class,
            ],
            'unsupported alg: HS384' => [
                false,
                'eyJhbGciOiJIUzM4NCIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6ImhvZ2UiLCJ0ZXN0IjoieWVzIiwiaWF0IjoxNTE2MjM5MDIyfQ.DsTcKnlyXHMpOabfsnTjK_HJr6E4hmrCW8bALviNqiklWv8TAsW6N31m5ImwVZEn',
                Loader::loadPublicKey('test1_rsa.pub'),
                \UnexpectedValueException::class,
            ],
            'unsupported alg: HS512' => [
                false,
                'eyJhbGciOiJIUzUxMiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6ImhvZ2UiLCJ0ZXN0IjoieWVzIiwiaWF0IjoxNTE2MjM5MDIyfQ.-a4VPFkHwtuWDInCb3eK8HF-qkY8cI_8eWkPPfH1lxuxqBy8lp3hTZi_-5aWGYvHTuWB_aMSvVikb4oxrfP0fQ',
                Loader::loadPublicKey('test1_rsa.pub'),
                \UnexpectedValueException::class,
            ],
            'unsupported alg: ES256' => [
                false,
                'eyJhbGciOiJFUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6InRlc3QiLCJpYXQiOjI1MzM3MDczMjQwMCwiZXhwIjowfQ.SflKxwRJSMeKKF2QT4fwpMeJf36POk6yJV_adQssw5c',
                Loader::loadPublicKey('test1_rsa.pub'),
                \UnexpectedValueException::class,
            ],
            'unsupported alg: ES384' => [
                false,
                'eyJhbGciOiJFUzM4NCIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6InRlc3QiLCJpYXQiOjI1MzM3MDczMjQwMCwiZXhwIjowfQ.W-jEzRfkc6taW_hcsWwxk5E_J9gQsETD-UzIwvZOIo',
                Loader::loadPublicKey('test1_rsa.pub'),
                \UnexpectedValueException::class,
            ],
            'unsupported alg: ES512' => [
                false,
                'eyJhbGciOiJFUzUxMiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6InRlc3QiLCJpYXQiOjI1MzM3MDczMjQwMCwiZXhwIjowfQ.XbPkXJ-jhjWgPYQVw5HgKJkPZCgKvkK-VzsSL3_MnB8',
                Loader::loadPublicKey('test1_rsa.pub'),
                \UnexpectedValueException::class,
            ],
            'unsupported alg: PS256' => [
                false,
                'eyJhbGciOiJQUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6InRlc3QiLCJpYXQiOjI1MzM3MDczMjQwMCwiZXhwIjowfQ.SflKxwRJSMeKKF2QT4fwpMeJf36POk6yJV_adQssw5c',
                Loader::loadPublicKey('test1_rsa.pub'),
                \UnexpectedValueException::class,
            ],
            'unsupported alg: PS384' => [
                false,
                'eyJhbGciOiJQUzM4NCIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6InRlc3QiLCJpYXQiOjI1MzM3MDczMjQwMCwiZXhwIjowfQ.W-jEzRfkc6taW_hcsWwxk5E_J9gQsETD-UzIwvZOIo',
                Loader::loadPublicKey('test1_rsa.pub'),
                \UnexpectedValueException::class,
            ],
            'unsupported alg: PS512' => [
                false,
                'eyJhbGciOiJQUzUxMiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6InRlc3QiLCJpYXQiOjI1MzM3MDczMjQwMCwiZXhwIjowfQ.XbPkXJ-jhjWgPYQVw5HgKJkPZCgKvkK-VzsSL3_MnB8',
                Loader::loadPublicKey('test1_rsa.pub'),
                \UnexpectedValueException::class,
            ],
            'unsupported alg: RSA384' => [
                false,
                'eyJhbGciOiJSUzM4NCIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6InRlc3QiLCJpYXQiOjI1MzM3MDczMjQwMCwiZXhwIjowfQ.MJcwed9mtJluF5Ufw2kpbdxk47Mtk6WyIRklcdHOoXv-rHIjX9zaUZYPfQXj6iTTEek9zaQ4FMrliJFNIxKL7xrbvDOUgBXUIPYRcDZILh9bo-_lHDfDYGGSoMDSyUd40HBz71POCLh-mQ2OnRW8a6O51XdKvjko7tNQgMFEoZaF3MOjCFhiqk-OWC34dYuATehyfvZ24gXiDF_kOfXCLVS6zafysattb2PuhtEjnqrDIf-Yq2f-aNB9JsX1AdxGadHazk50FCA_5AINxzbVDO_LW4ELP1UYrVsmPaNYwLslAlwmTFhY7GaV0PYLkrHCFwl4gekf7ju5Ct-J5NXYyA',
                Loader::loadPublicKey('test1_rsa.pub'),
                \UnexpectedValueException::class,
            ],
            'unsupported alg: RSA512' => [
                false,
                'eyJhbGciOiJSUzUxMiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6InRlc3QiLCJpYXQiOjI1MzM3MDczMjQwMCwiZXhwIjowfQ.MJcwed9mtJluF5Ufw2kpbdxk47Mtk6WyIRklcdHOoXv-rHIjX9zaUZYPfQXj6iTTEek9zaQ4FMrliJFNIxKL7xrbvDOUgBXUIPYRcDZILh9bo-_lHDfDYGGSoMDSyUd40HBz71POCLh-mQ2OnRW8a6O51XdKvjko7tNQgMFEoZaF3MOjCFhiqk-OWC34dYuATehyfvZ24gXiDF_kOfXCLVS6zafysattb2PuhtEjnqrDIf-Yq2f-aNB9JsX1AdxGadHazk50FCA_5AINxzbVDO_LW4ELP1UYrVsmPaNYwLslAlwmTFhY7GaV0PYLkrHCFwl4gekf7ju5Ct-J5NXYyA',
                Loader::loadPublicKey('test1_rsa.pub'),
                \UnexpectedValueException::class,
            ],
            'expired' => [
                false,
                'eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6InRlc3QiLCJpYXQiOjI1MzM3MDczMjQwMCwiZXhwIjowfQ.euPjhLgTWMRCwEDoF53Fth0n_t2aC3psTc3fanSlQNc3gNJt7muckIPmnWmwnaLaHh5NIqR3rtpIVCVTkZ7Vgmq_D1XWzeegwsUxar5lrnO6QhFuBIfAZebP_7mLq44IhuqAgFXj7_2U2g7bzgq0VI3fRXaexopNy5roYwwQiGKBxBrPQJocZuxaxjPMGQ4hfEvL7KnEiVnSey4Fm-I4K2BScU1MRaqYA68s5O_o3h91PMYG76hH_8_V3d_PQWwWeQF9nW10Gazu6NMqQ98PALYRCcw7HNgwhKA_vmvw0V3grPWiWqZ6i38CW_G-qYTZ6bnlsK6CJFDHjbsoR7sHBw',
                Loader::loadPublicKey('test1_rsa.pub'),
                InvalidTokenException::class,
            ],
            'before iat' => [
                false,
                'eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6InRlc3QiLCJpYXQiOjI1MzM3MDczMjQwMCwiZXhwIjoyNTMzNzA3MzI0MDB9.DQSLqn0y3rWohF_UcywGAGdcxTIx97z0dD6DCCDxLuaCKXW7vX8x6Z-U2m_rPbLYU1nvZlrMgrN8PG4hYNNQl2j3TOs74CTDLyx_np5KugRVeHlNYmW6RaeELc2iUHcbG5pvvZ2C_OxfrpC060EdZ-22rlJYNufHpX5zGtINy-K5q0z8oeZIns-WAsrwJOcZPxcALzPebV-XxbSs362ISvmcYQ5eKy5ZL8Zo8Sy1MvQheOHjob8KrjWvnP6M9m3en-6WK-W5EpL7w3CIFVKMvpyqEGQAhJ62iQQA_Q51S71UivOLAMtq7Oi8MTcNFXsxe9qjyunIsE08BqmyldYkzw',
                Loader::loadPublicKey('test1_rsa.pub'),
                InvalidTokenException::class,
            ],
            'before nbf' => [
                false,
                'eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6InRlc3QiLCJpYXQiOjAsImV4cCI6MjUzMzcwNzMyNDAwLCJuYmYiOjI1MzM3MDczMjQwMH0.Is0bhk1Vwl8HOmTM5YJtJDCHp5w1mA-vf4T4zSkUuosdtqUsnNQJN3xiRH5UMtNtUW0piHXcIdUOq6zbjhuGHlZVi1AOkrDs3oyPEeKcnmTgCPO4QN2zbglwDNEEEZRphho1aOlZSQz6b3BFCCzzdNIdyVdV6I_z7zGIZ35tQAaA6pIt3gdeWol9-5JaYjV4cJjIa4Ou9cA53GlWFjEJ9X4Z82lYUM1UMDB4yEu-1LVztxXhJhqzKYt3UDDdqATHUQ5DXQ4Glwxi2Ub9gzaOSpywSybf-uVxMrlHPLBqvuNQtdNAR0revkVwSeY0lXDwH_mQ7pz0yHubtckcmw1uHA',
                Loader::loadPublicKey('test1_rsa.pub'),
                InvalidTokenException::class,
            ],
            'invalid signature' => [
                false,
                'eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6InRlc3QiLCJpYXQiOjAsImV4cCI6MjUzMzcwNzMyNDAwfQ.kWktHwTFkqDecfVwqgY0oYF6xrnkcgj5o_68OHK4YffZIkwzuvu0WWdxaOyTatCli3AU_MqbISD0BoGzXeYtX2cSUGQ9U8gg9voE8KFOwMehJ_Cx4jAqiKUqCm5cKXAiqwP7tK-_1kWuQ8FktTvW4xFQOhI4a-DzcO2XV5JFTcp13BZ-s2Jn4h4BXkJH0A2xk9DOfeWYw_wDj-_u8oEnvfIUDQUZ06RQlv3pBKfQA6ee6Atuwl8grnjVWsw4kzIJ7bu3iRffIkpPYZZRhtnr9UOa3me8sz-LtazAyWUP3SK9zrHDMTzBip6a4Yu1f44ckk4C5nLsZUCZ0hffjprsLw',
                Loader::loadPublicKey('test2_rsa.pub'),
                null,
            ],
            'empty exp' => [
                true,
                'eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6InRlc3QiLCJpYXQiOjAsIm5iZiI6MH0.RCgRz5XRua5KxosEJ77QKfJMILI0sFyEQGNegEgQFj09PAQe4mRsN6OGl9-sFqdhSuYPxT8m6-3JAOKZlUPyqhyXPLP3rl8VV6OkiAZ-Id0hAcFUKTCrUssTyyeRCPbxtkzCVDLPKRD-KrsrX1HRKjnXgPfHxOZQ2eG2PbFldjWLyJHt_6yS5HJ86fJJjeiOZhbi8GZOlkmn2ZsvPpJHtI_OfWelTCseCbHuIT06-ZcutNa8aM4N-ZqAFn-Jpv3cyWOY_qMmXuaqfrGEXqSZeLmPdMNnz5vJwxLvSpF5PcBXRCqW-so1Y7clprDb93yNXmDVW_852iNiOz0_V7t7mA',
                Loader::loadPublicKey('test1_rsa.pub'),
                null,
            ],
            'empty iat' => [
                true,
                'eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6InRlc3QiLCJleHAiOjI1MzM3MDczMjQwMCwibmJmIjowfQ.VqcHEa9fRmBD4WsFbZHBbDqull__9u_65IwiQPlbrrjTHgqBk13QDc6ZWPK2zeVZ965KfVKI5dM7_gdPAXeYlTuN--MAaGx3w9tvHIjrdRRYWWz4RBEc3G-HaCFUVsQuVv3Mhv9SG6nYmKJ6QpzAuXST0JlCy6QZzdT6hnA5MhRwL9j3P-g2MaMU8oc_wtZ6KwcmmLt15viB61Q7tDxHxUYBgjvwk620zBYSZZIQ8Cc5z2xLj_0C0A4IhcxaDRAq2_rOpS_aD_xZjlI9KrZyRypBSc__jCf02AZtgyJZ0aA0hpRbiFONCW4g9F56Q2Alu38m8ka_CoeJd5XrlFS_dQ',
                Loader::loadPublicKey('test1_rsa.pub'),
                null,
            ],
            'empty nbf' => [
                true,
                'eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6InRlc3QiLCJpYXQiOjAsImV4cCI6MjUzMzcwNzMyNDAwfQ.kWktHwTFkqDecfVwqgY0oYF6xrnkcgj5o_68OHK4YffZIkwzuvu0WWdxaOyTatCli3AU_MqbISD0BoGzXeYtX2cSUGQ9U8gg9voE8KFOwMehJ_Cx4jAqiKUqCm5cKXAiqwP7tK-_1kWuQ8FktTvW4xFQOhI4a-DzcO2XV5JFTcp13BZ-s2Jn4h4BXkJH0A2xk9DOfeWYw_wDj-_u8oEnvfIUDQUZ06RQlv3pBKfQA6ee6Atuwl8grnjVWsw4kzIJ7bu3iRffIkpPYZZRhtnr9UOa3me8sz-LtazAyWUP3SK9zrHDMTzBip6a4Yu1f44ckk4C5nLsZUCZ0hffjprsLw',
                Loader::loadPublicKey('test1_rsa.pub'),
                null,
            ],
            'valid' => [
                true,
                'eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6InRlc3QiLCJpYXQiOjAsImV4cCI6MjUzMzcwNzMyNDAwLCJuYmYiOjB9.EnG5cxaDH4cLRtqm_NNqlolhOZOw_GGFFLS6zmvVzLSTpSKvbilrvOKff3OSnyjpcrYyO7eXPKjSLPWRoUxUh5EAsiNNjfIPx4VlNO4IQGyt4OHBuXjM8OurPC5zUPShk1fgkZe0krcqHywRkDza2ECydMDijMTDxGoDoUItVNDVv5YWvg7FhQYywFKrzD94TrcUF3njAA4KQK4WEHNlwwWaqMNK1NzusED1AgL3evRjRmK94S_s0GHMtYpc1475E_fbM4JzCQj_q0BfXX2XdxT6lFegpa1n8NASQscIjVfIkS__bn9Abeg8GSwCkhVsy_mRaDX23iiG2J47BFpZiw',
                Loader::loadPublicKey('test1_rsa.pub'),
                null,
            ],
        ];
    }
}
