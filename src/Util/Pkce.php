<?php declare(strict_types=1);

namespace GameWith\Oidc\Util;

/**
 * Class Pkce
 * @package GameWith\Oidc\Util
 */
class Pkce
{
    /**
     * PKCE 対策のために利用し、43~128 までのランダムな文字列を生成する
     *
     * @return string
     */
    public static function generateCodeVerifier(): string
    {
        $max = mt_rand(43, 128);
        $reserved = str_split('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-._~');
        $codeVerifier = '';
        for ($i = 0; $i < $max; $i++) {
            $codeVerifier .= $reserved[mt_rand(0, count($reserved) - 1)];
        }
        return $codeVerifier;
    }

    /**
     * PKCE 対策のために利用し、codeVerifier を元に計算した値を作成
     *
     * @param string $codeVerifier
     * @return string
     * @throws \Exception
     */
    public static function createCodeChallenge(string $codeVerifier): string
    {
        return Base64Url::encode(hash('sha256', $codeVerifier, true));
    }
}
