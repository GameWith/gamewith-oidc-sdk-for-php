<?php declare(strict_types=1);

namespace GameWith\Oidc\Jwx;

use GameWith\Oidc\Exception\Base64Exception;
use GameWith\Oidc\Exception\InvalidTokenException;
use GameWith\Oidc\Exception\JsonErrorException;
use GameWith\Oidc\Util\Base64Url;
use GameWith\Oidc\Util\Json;
use phpseclib3\Crypt\Common\PublicKey;
use phpseclib3\Crypt\RSA;

/**
 * Class Jws
 * @package GameWith\Oidc\Jwx
 */
class Jws
{
    const SUPPORTED_ALGORITHMS = ['RS256'];

    const VERIFY_METHODS = [
        'RS256' => 'verifyPkcs1',
    ];

    /**
     * 発行日時許容秒数
     *
     * @var int
     */
    private $allowableIatSec = 0;

    /**
     * 発行日時許容秒数の指定
     *
     * @param int $sec
     * @return void
     */
    public function setAllowableIatSec(int $sec)
    {
        $this->allowableIatSec = $sec;
    }

    /**
     * JWT トークンの検証
     *
     * @param string $token
     * @param PublicKey $publicKey
     * @return bool
     * @throws JsonErrorException
     * @throws InvalidTokenException
     * @throws Base64Exception
     */
    public function verify(string $token, PublicKey $publicKey): bool
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            throw new \UnexpectedValueException('invalid token format');
        }

        return $this->verifyBySplitToken($parts, $publicKey);
    }

    /**
     * JWT トークンの検証(引数がトークン分割済)
     *
     * @param array<string> $parts
     * @param PublicKey $publicKey
     * @return bool
     * @throws InvalidTokenException
     * @throws JsonErrorException
     * @throws Base64Exception
     */
    public function verifyBySplitToken(array $parts, PublicKey $publicKey): bool
    {
        $decodeBase64Header = Base64Url::decode($parts[0]);
        if (!$decodeBase64Header) {
            throw new Base64Exception('base64 url decode of header failed');
        }

        $header = Json::decode($decodeBase64Header, true);

        if (!isset($header['alg'])) {
            throw new \UnexpectedValueException('undefined alg');
        }

        if (!in_array($header['alg'], self::SUPPORTED_ALGORITHMS, true)) {
            throw new \UnexpectedValueException('unsupported alg');
        }

        $decodeBase64Payload = base64_decode($parts[1]);
        if (!$decodeBase64Payload) {
            throw new Base64Exception('base64 decode of payload failed');
        }

        $payload = Json::decode(base64_decode($parts[1]), true);

        $now = time();
        if (!$this->verifyExpiresAt($payload, $now)) {
            throw new InvalidTokenException('token is expired');
        }

        if (!$this->verifyIssuedAt($payload, $now)) {
            throw new InvalidTokenException('token used before issued');
        }

        if (!$this->verifyNotBefore($payload, $now)) {
            throw new InvalidTokenException('token is not valid yet');
        }

        $signature = Base64Url::decode($parts[2]);
        if (!$signature) {
            throw new Base64Exception('base64 decode of payload failed');
        }

        $message = $parts[0] . '.' . $parts[1];
        $verifyMethod = self::VERIFY_METHODS[$header['alg']];
        return $this->$verifyMethod($message, $signature, $publicKey);
    }

    /**
     * JWT トークンの失効日時を検証
     *
     * @param array<string, mixed> $payload JWT Payload
     * @param int $now Unix timestamp
     * @return bool
     */
    private function verifyExpiresAt(array $payload, int $now): bool
    {
        if (!isset($payload['exp'])) {
            return true;
        }
        if (!is_int($payload['exp'])) {
            return false;
        }
        return $now <= $payload['exp'];
    }

    /**
     * JWT トークンの発行日時を検証
     *
     * @param array<string, mixed> $payload JWT Payload
     * @param int $now Unix timestamp
     * @return bool
     */
    private function verifyIssuedAt(array $payload, int $now): bool
    {
        if (!isset($payload['iat'])) {
            return true;
        }
        if (!is_int($payload['iat'])) {
            return false;
        }
        return $now >= ($payload['iat'] - $this->allowableIatSec);
    }

    /**
     * JWT トークンの有効開始日時を検証
     *
     * @param array<string, mixed> $payload JWT Payload
     * @param int $now Unix timestamp
     * @return bool
     */
    private function verifyNotBefore(array $payload, int $now): bool
    {
        if (!isset($payload['nbf'])) {
            return true;
        }
        if (!is_int($payload['nbf'])) {
            return false;
        }
        return $now >= $payload['nbf'];
    }

    /**
     * RSA(PKCS1) のトークン検証
     *
     * @param string $message
     * @param string $signature
     * @param PublicKey $publicKey
     * @return bool
     * @throws InvalidTokenException
     */
    private function verifyPkcs1(
        string $message,
        string $signature,
        PublicKey $publicKey
    ): bool {
        if (!$publicKey instanceof RSA\PublicKey) {
            throw new \UnexpectedValueException('public key must be RSA');
        }
        return $publicKey
            ->withPadding(RSA::SIGNATURE_PKCS1 | RSA::ENCRYPTION_PKCS1)
            ->verify($message, $signature);
    }
}
