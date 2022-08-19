<?php declare(strict_types=1);

namespace GameWith\Oidc\Property;

use GameWith\Oidc\ClientMetadata;
use GameWith\Oidc\Constant\GrantType;
use GameWith\Oidc\Util\ScopeBuilder;

/**
 * Class ExchangeProperty
 * @package GameWith\Oidc\Property
 */
class ExchangeProperty
{
    /**
     * 対応しているグラントタイプ
     */
    const SUPPORT_GRANT_TYPES = [GrantType::AUTHORIZATION_CODE];

    /**
     * @var string
     */
    private $grantType;
    /**
     * @var string
     */
    private $code;
    /**
     * @var ClientMetadata | null
     */
    private $metadata;
    /**
     * @var string | null
     */
    private $codeVerifier;
    /**
     * @var ScopeBuilder
     */
    private $scopeBuilder;

    /**
     * ExchangeProperty constructor.
     *
     * @param string $code
     * @param string $grantType
     */
    public function __construct(
        string $code,
        string $grantType = GrantType::AUTHORIZATION_CODE
    ) {
        $this->code = $code;
        $this->grantType = $grantType;
        $this->scopeBuilder = ScopeBuilder::make();
    }

    /**
     * プロパティの設定が正しいか検証する
     *
     * @return void
     */
    public function valid()
    {
        if (empty($this->code)) {
            throw new \UnexpectedValueException('code is empty');
        }

        if (empty($this->grantType)) {
            throw new \UnexpectedValueException('grant_type is required');
        }

        if (!in_array($this->grantType, self::SUPPORT_GRANT_TYPES, true)) {
            throw new \UnexpectedValueException('grant_type is not supported');
        }

        if (is_null($this->metadata)) {
            throw new \UnexpectedValueException('metadata is required');
        }

        if ($this->scopeBuilder->isEmpty()) {
            throw new \UnexpectedValueException('scope is required');
        }
    }

    /**
     * トークンリクエストで利用するパラメータを取得する
     *
     * @return array<string, string>
     */
    public function params(): array
    {
        if (is_null($this->metadata)) {
            throw new \UnexpectedValueException('metadata is required');
        }

        $params = [
            'code'         => $this->code,
            'client_id'    => $this->metadata->getClientId(),
            'redirect_uri' => $this->metadata->getRedirectUri(),
            'grant_type'   => $this->grantType,
            'scope'        => $this->getScope(),
        ];

        if (!is_null($this->codeVerifier)) {
            $params['code_verifier'] = $this->codeVerifier;
        }

        return $params;
    }

    /**
     * クライアントのメタ情報を設定する
     *
     * @param ClientMetadata $metadata
     * @return ExchangeProperty
     */
    public function setMetadata(ClientMetadata $metadata): self
    {
        $this->metadata = $metadata;
        return $this;
    }

    /**
     * グラントタイプを取得する
     *
     * @return string
     */
    public function getGrantType(): string
    {
        return $this->grantType;
    }

    /**
     * 認可コードを取得する
     *
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * 認証リクエスト時に設定した codeChallenge に一致する codeVerifier を設定する
     *
     * @param string $codeVerifier
     * @return ExchangeProperty
     */
    public function setCodeVerifier(string $codeVerifier): self
    {
        $this->codeVerifier = $codeVerifier;
        return $this;
    }

    /**
     * codeVerifier を取得する
     *
     * @return null|string
     */
    public function getCodeVerifier()
    {
        return $this->codeVerifier;
    }

    /**
     * スコープを追加する
     *
     * @param string ...$scope
     * @return ExchangeProperty
     */
    public function addScope(string ...$scope): self
    {
        $this->scopeBuilder->add(...$scope);
        return $this;
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
}
