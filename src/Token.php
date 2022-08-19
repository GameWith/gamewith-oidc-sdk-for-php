<?php declare(strict_types=1);

namespace GameWith\Oidc;

use GameWith\Oidc\Exception\Base64Exception;
use GameWith\Oidc\Exception\InvalidTokenException;
use GameWith\Oidc\Jwx\JwkSet;
use GameWith\Oidc\Jwx\Jws;
use GameWith\Oidc\Util\Base64Url;
use GameWith\Oidc\Util\Json;

/**
 * Class Token
 * @package GameWith\Oidc
 */
class Token
{
    /**
     * @var string
     */
    private $accessToken;
    /**
     * @var string
     */
    private $refreshToken;
    /**
     * @var int
     */
    private $expiresIn;
    /**
     * @var string
     */
    private $scope;
    /**
     * @var string|null
     */
    private $idToken;
    /**
     * @var array<string, mixed>
     */
    private $jwks;
    /**
     * @var Provider
     */
    private $provider;
    /**
     * @var ClientMetadata
     */
    private $metadata;

    /**
     * Token constructor.
     *
     * @param array{access_token: string, refresh_token: string, expires_in: int, scope: string, id_token: string|null} $body
     * @param array<string, mixed> $jwks
     * @param Provider $provider
     * @param ClientMetadata $metadata
     */
    public function __construct(
        array $body,
        array $jwks,
        Provider $provider,
        ClientMetadata $metadata
    ) {
        $this->accessToken = $body['access_token'];
        $this->refreshToken = $body['refresh_token'];
        $this->expiresIn = $body['expires_in'];
        $this->scope = $body['scope'];
        $this->idToken = $body['id_token'] ?? null;
        $this->jwks = $jwks;
        $this->provider = $provider;
        $this->metadata = $metadata;
    }

    /**
     * IDToken の検証
     *
     * @param string|null $nonce
     * @param int $allowableIatSec
     * @return array{header: array<string, string>, payload: array<string, mixed>}
     * @throws Exception\JsonErrorException
     * @throws Exception\NotFoundException
     * @throws InvalidTokenException
     * @throws Base64Exception
     */
    public function parseIdToken(string $nonce = null, $allowableIatSec = 5): array
    {
        if (is_null($this->idToken)) {
            throw new \UnexpectedValueException('empty id_token');
        }

        $parts = explode('.', $this->idToken);

        if (count($parts) !== 3) {
            throw new \UnexpectedValueException('invalid id_token format');
        }

        $decodeBase64Header = Base64Url::decode($parts[0]);
        if (!$decodeBase64Header) {
            throw new Base64Exception('base64 decode of header failed');
        }

        $header = Json::decode($decodeBase64Header, true);
        $jwk = JwkSet::find($this->jwks, $header);
        $publicKey = $jwk->toPublicKey();

        $jws = new Jws();
        $jws->setAllowableIatSec($allowableIatSec);
        if (!$jws->verifyBySplitToken($parts, $publicKey)) {
            throw new InvalidTokenException('failed to verify id_token');
        }

        $payload = Json::decode(base64_decode($parts[1]), true);

        // 発行者の検証
        if (!$this->verifyIssuer($payload)) {
            throw new InvalidTokenException('invalid issuer');
        }

        // 利用者の検証
        if (!$this->verifyAudience($payload)) {
            throw new InvalidTokenException('invalid audience');
        }

        // アクセストークンのハッシュ値を検証
        if (!$this->verifyAtHash($payload)) {
            throw new InvalidTokenException('invalid at_hash');
        }

        // ノンスの検証
        if (!$this->verifyNonce($payload, $nonce)) {
            throw new InvalidTokenException('invalid nonce');
        }

        // 認証時刻の検証
        if (!$this->verifyAuthTime($payload)) {
            throw new InvalidTokenException('invalid auth_time');
        }

        return [
            'header'  => $header,
            'payload' => $payload,
        ];
    }

    /**
     * Audience の検証
     *
     * @param array<string, mixed> $payload
     * @return bool
     */
    private function verifyAudience(array $payload): bool
    {
        if (!isset($payload['aud'])) {
            return false;
        }
        return $payload['aud'] === $this->metadata->getClientId();
    }

    /**
     * ユーザー認証時刻の検証
     *
     * @param array<string, mixed> $payload
     * @return bool
     */
    private function verifyAuthTime(array $payload): bool
    {
        if (!isset($payload['auth_time'])) {
            return true;
        }
        $authTime = $payload['auth_time'];
        $dt = new \DateTime();
        $dt->setTimestamp($authTime);
        $dt->modify(sprintf('+%d second', $this->getExpiresIn()));
        $now = new \DateTime();
        return $dt > $now;
    }

    /**
     * 発行者の検証
     *
     * @param array<string, mixed> $payload
     * @return bool
     */
    private function verifyIssuer(array $payload): bool
    {
        if (!isset($payload['iss'])) {
            return false;
        }
        return $payload['iss'] === $this->provider->getIssuer();
    }

    /**
     * アクセストークンのハッシュ値を検証
     *
     * @param array<string, mixed> $payload
     * @return bool
     */
    private function verifyAtHash(array $payload): bool
    {
        if (!isset($payload['at_hash'])) {
            return false;
        }
        // RS256 固定なので SHA256 でハッシュ
        $atHash = hash('sha256', $this->accessToken, true);
        // 左半分の 128bits を base64url エンコード (128 bits = 16 bytes)
        $atHash = Base64Url::encode(substr($atHash, 0, 16));
        return $atHash === $payload['at_hash'];
    }

    /**
     * リプレイアタックを防止するための検証
     *
     * @param array<string, mixed> $payload
     * @param string|null $nonce
     * @return bool
     */
    private function verifyNonce(array $payload, string $nonce = null): bool
    {
        if (!isset($payload['nonce'])) {
            return true;
        }
        return $nonce === $payload['nonce'];
    }

    /**
     * アクセストークンの取得
     *
     * @return string
     */
    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    /**
     * リフレッシュトークンの取得
     *
     * @return string
     */
    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }

    /**
     * トークン有効期限(秒)を取得
     *
     * @return int
     */
    public function getExpiresIn(): int
    {
        return $this->expiresIn;
    }

    /**
     * スコープの取得
     *
     * @return string
     */
    public function getScope(): string
    {
        return $this->scope;
    }

    /**
     * IDToken の取得
     *
     * スコープに openid が含まれていない場合は、含まれない
     *
     * @return string|null
     */
    public function getIdToken()
    {
        return $this->idToken;
    }
}
