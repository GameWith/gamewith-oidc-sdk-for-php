<?php declare(strict_types=1);

namespace GameWith\Oidc\Util;

/**
 * Class Base64Url
 * @package GameWith\Oidc\Util
 * @link https://datatracker.ietf.org/doc/html/rfc4648
 */
class Base64Url
{
    /**
     * Base64URL safe encode
     *
     * @param string $data
     * @return string
     */
    public static function encode(string $data): string
    {
        return str_replace('=', '', strtr(base64_encode($data), '+/', '-_'));
    }

    /**
     * Base64URL safe decode
     *
     * @param string $data
     * @return string|false
     */
    public static function decode(string $data)
    {
        $dataLen = strlen($data);
        $remainder = $dataLen % 4;
        if ($remainder) {
            $padLen = 4 - $remainder;
            $data = str_pad($data, $dataLen + $padLen, '=', STR_PAD_RIGHT);
        }
        return base64_decode(strtr($data, '-_', '+/'));
    }
}
