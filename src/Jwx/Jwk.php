<?php declare(strict_types=1);

namespace GameWith\Oidc\Jwx;

use GameWith\Oidc\Exception\Base64Exception;
use GameWith\Oidc\Util\Base64Url;
use phpseclib3\Crypt\Common\PublicKey;
use phpseclib3\Crypt\PublicKeyLoader;
use phpseclib3\Math\BigInteger;

/**
 * Class Jwk
 * @package GameWith\Oidc\Jwx
 */
class Jwk
{
    /**
     * 対応しているキータイプ
     */
    const SUPPORT_KEY_TYPES = [
        'RSA',
    ];

    /**
     * @var array<string, mixed>
     */
    private $jwk;

    /**
     * Jwk constructor.
     *
     * @param array<string, mixed> $jwk
     */
    public function __construct(array $jwk)
    {
        if (empty($jwk)) {
            throw new \UnexpectedValueException('jwk is empty');
        }
        if (!isset($jwk['kty']) || !in_array($jwk['kty'], self::SUPPORT_KEY_TYPES, true)) {
            throw new \UnexpectedValueException('unsupported key type');
        }
        $this->valid($jwk);
        $this->jwk = $jwk;
    }

    /**
     * Jwk の KeyId を取得する
     *
     * @return string
     */
    public function getKeyId(): string
    {
        return (string) $this->jwk['kid'];
    }

    /**
     * Jwk を 公開鍵に変換する
     *
     * @return PublicKey
     * @throws Base64Exception
     */
    public function toPublicKey(): PublicKey
    {
        $e = base64_decode($this->jwk['e']);
        if (!$e) {
            throw new Base64Exception('base64 decode of jwk[e] failed');
        }
        $n = Base64Url::decode($this->jwk['n']);
        if (!$n) {
            throw new Base64Exception('base64 decode of jwk[n] failed');
        }
        return PublicKeyLoader::loadPublicKey([
            'e' => new BigInteger($e, 256),
            'n' => new BigInteger($n, 256)
        ]);
    }

    /**
     * 公開鍵の検証
     *
     * @param array<string, mixed> $jwk
     * @return void
     */
    private function valid(array $jwk)
    {
        if ($jwk['kty'] === 'RSA') {
            $this->validRSA($jwk);
        } else {
            throw new \UnexpectedValueException('unsupported key type');
        }
    }

    /**
     * RSA 公開鍵の検証
     *
     * @param array<string, mixed> $jwk
     * @return void
     */
    private function validRSA(array $jwk)
    {
        if (!isset($jwk['e'], $jwk['n'])) {
            throw new \UnexpectedValueException('invalid jwk format');
        }
    }
}
