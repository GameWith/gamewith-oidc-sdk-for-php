<?php declare(strict_types=1);

namespace GameWith\Oidc;

use GameWith\Oidc\Constant\TokenType;

/**
 * Class ClientMetadata
 * @package GameWith\Oidc
 */
class ClientMetadata
{
    /**
     * @var string
     */
    private $clientId;
    /**
     * @var string
     */
    private $clientSecret;
    /**
     * @var string
     */
    private $redirectUri;

    /**
     * ClientMetadata constructor.
     *
     * @param string $clientId
     * @param string $clientSecret
     * @param string $redirectUri
     */
    public function __construct(string $clientId, string $clientSecret, string $redirectUri)
    {
        if (empty($clientId)) {
            throw new \UnexpectedValueException('client_id is required');
        }

        if (empty($clientSecret)) {
            throw new \UnexpectedValueException('client_secret is required');
        }

        if (empty($redirectUri)) {
            throw new \UnexpectedValueException('redirect_uri is required');
        }

        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->redirectUri = $redirectUri;
    }

    /**
     * client_id の取得
     *
     * @return string
     */
    public function getClientId(): string
    {
        return $this->clientId;
    }

    /**
     * client_secret の取得
     *
     * @return string
     */
    public function getClientSecret(): string
    {
        return $this->clientSecret;
    }

    /**
     * redirect_uri の取得
     *
     * @return string
     */
    public function getRedirectUri(): string
    {
        return $this->redirectUri;
    }

    /**
     * トークンリクエスト時 Authorization Header にセットする値を取得する
     *
     * @return string
     */
    public function getAuthorization(): string
    {
        return sprintf("%s %s", TokenType::BASIC, $this->clientSecret);
    }
}
