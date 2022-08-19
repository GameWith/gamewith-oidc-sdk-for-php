<?php declare(strict_types=1);

namespace GameWith\Oidc\Util;

use GameWith\Oidc\Exception\JsonErrorException;

/**
 * Class Json
 * @package GameWith\Oidc\Util
 */
class Json
{
    /**
     * json_encode wrapper
     *
     * @param mixed $value
     * @param int $flags
     * @param positive-int $depth
     * @return string|false
     * @throws JsonErrorException
     */
    public static function encode($value, int $flags = 0, int $depth = 512)
    {
        $data = json_encode($value, $flags, $depth);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new JsonErrorException(json_last_error_msg());
        }
        return $data;
    }

    /**
     * json_decode wrapper
     *
     * @param string $json
     * @param bool $assoc
     * @param positive-int $depth
     * @param int $options
     * @return mixed
     * @throws JsonErrorException
     */
    public static function decode(string $json, bool $assoc = false, int $depth = 512, int $options = 0)
    {
        $data = json_decode($json, $assoc, $depth, $options);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new JsonErrorException(json_last_error_msg());
        }
        return $data;
    }
}
