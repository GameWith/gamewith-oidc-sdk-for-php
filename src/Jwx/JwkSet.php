<?php declare(strict_types=1);

namespace GameWith\Oidc\Jwx;

use GameWith\Oidc\Exception\NotFoundException;

/**
 * Class JwkSet
 * @package GameWith\Oidc\Jwx
 */
class JwkSet
{
    /**
     * トークンのヘッダー情報から一致する Jwk を取得する
     *
     * @param array<string, mixed> $jwks
     * @param array<string, string> $header
     * @return Jwk
     * @throws \UnexpectedValueException
     * @throws NotFoundException
     */
    public static function find(array $jwks, array $header): Jwk
    {
        if (empty($jwks)) {
            throw new \UnexpectedValueException('jwks is empty');
        }

        if (!isset($jwks['keys'])) {
            throw new \UnexpectedValueException('jwks is invalid format');
        }

        if (empty($header) || !isset($header['kid']) || $header['kid'] === '') {
            throw new \UnexpectedValueException('header.kid is required');
        }
        $keyId = $header['kid'];
        foreach ($jwks['keys'] as $jwk) {
            if ($jwk['kid'] === $keyId) {
                return new Jwk($jwk);
            }
        }
        throw new NotFoundException('not found jwk');
    }
}
