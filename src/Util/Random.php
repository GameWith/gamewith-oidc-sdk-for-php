<?php declare(strict_types=1);

namespace GameWith\Oidc\Util;

/**
 * Class Random
 * @package GameWith\Oidc\Util
 */
class Random
{
    /**
     * ランダム文字列を生成する
     *
     * @param positive-int $length
     * @return string
     * @throws \Exception
     */
    public static function str($length = 32): string
    {
        return bin2hex(random_bytes($length));
    }
}
