<?php declare(strict_types=1);

namespace GameWith\Oidc\Property;

use GameWith\Oidc\ClientMetadata;
use GameWith\Oidc\Constant\GrantType;
use GameWith\Oidc\Util\ScopeBuilder;

/**
 * Class RefreshProperty
 * @package GameWith\Oidc\Property
 */
class RefreshProperty
{
    /**
     * @var ClientMetadata | null
     */
    private $metadata;
    /**
     * @var string
     */
    private $refreshToken;
    /**
     * @var ScopeBuilder
     */
    private $scopeBuilder;
    /**
     * @var string|null
     */
    private $nonce;


    /**
     * RefreshProperty constructor.
     *
     * @param string $refreshToken
     */
    public function __construct(string $refreshToken)
    {
        $this->refreshToken = $refreshToken;
        $this->scopeBuilder = ScopeBuilder::make();
    }

    /**
     * プロパティの設定が正しいか検証する
     *
     * @return void
     */
    public function valid()
    {
        if (empty($this->refreshToken)) {
            throw new \UnexpectedValueException('refresh_token is required');
        }

        if (is_null($this->metadata)) {
            throw new \UnexpectedValueException('metadata is required');
        }

        if ($this->scopeBuilder->isEmpty()) {
            throw new \UnexpectedValueException('scope is required');
        }
    }

    /**
     * リフレッシュリクエストで利用するパラメータを取得する
     *
     * @return array<string, string>
     */
    public function params(): array
    {
        if (is_null($this->metadata)) {
            throw new \UnexpectedValueException('metadata is required');
        }

        $params = [
            'client_id'     => $this->metadata->getClientId(),
            'client_secret' => $this->metadata->getClientSecret(),
            'grant_type'    => GrantType::REFRESH_TOKEN,
            'refresh_token' => $this->refreshToken,
            'scope'         => $this->getScope(),
        ];

        if (!empty($this->nonce)) {
            $params['nonce'] = $this->nonce;
        }

        return $params;
    }

    /**
     * クライアントのメタ情報を設定する
     *
     * @param ClientMetadata $metadata
     * @return RefreshProperty
     */
    public function setMetadata(ClientMetadata $metadata): self
    {
        $this->metadata = $metadata;
        return $this;
    }

    /**
     * リフレッシュトークンを取得する
     *
     * @return string
     */
    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }

    /**
     * スコープを取得する
     *
     * @return string
     */
    public function getScope(): string
    {
        return $this->scopeBuilder->build();
    }

    /**
     * スコープを追加する
     *
     * @param string ...$scope
     * @return RefreshProperty
     */
    public function addScope(string ...$scope): self
    {
        $this->scopeBuilder->add(...$scope);
        return $this;
    }

    /**
     * ノンスを取得する
     *
     * @return string|null
     */
    public function getNonce()
    {
        return $this->nonce;
    }

    /**
     * リプレイアタック対策のために利用し、ランダム文字列を設定する
     *
     * @param string $nonce
     * @return RefreshProperty
     */
    public function setNonce($nonce): self
    {
        $this->nonce = $nonce;
        return $this;
    }
}
