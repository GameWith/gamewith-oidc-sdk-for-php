<?php declare(strict_types=1);

namespace GameWith\Oidc\Property;

use GameWith\Oidc\ClientMetadata;
use GameWith\Oidc\Constant\ResponseType;
use GameWith\Oidc\Util\ScopeBuilder;

/**
 * Class AuthenticationRequestProperty
 * @package GameWith\Oidc\Property
 */
class AuthenticationRequestProperty
{
    /**
     * 対応しているレスポンスタイプ
     */
    const SUPPORT_RESPONSE_TYPES = [ResponseType::CODE];

    /**
     * @var string
     */
    private $responseType;
    /**
     * @var ScopeBuilder
     */
    private $scopeBuilder;
    /**
     * @var string | null
     */
    private $state;
    /**
     * @var string | null
     */
    private $nonce;
    /**
     * @var string | null
     */
    private $codeChallenge;
    /**
     * @var int | null
     */
    private $maxAge;
    /**
     * @var ClientMetadata | null
     */
    private $metadata;

    /**
     * AuthenticationRequestProperty constructor.
     *
     * @param string $responseType
     */
    public function __construct(string $responseType = ResponseType::CODE)
    {
        $this->responseType = $responseType;
        $this->scopeBuilder = ScopeBuilder::make();
    }

    /**
     * プロパティの設定が正しいか検証する
     *
     * @return void
     */
    public function valid()
    {
        if (empty($this->responseType)) {
            throw new \UnexpectedValueException('response_type is empty');
        }

        if (
            !in_array($this->responseType, self::SUPPORT_RESPONSE_TYPES, true)
        ) {
            throw new \UnexpectedValueException(
                'response_type is not supported'
            );
        }

        if (is_null($this->metadata)) {
            throw new \UnexpectedValueException('metadata is empty');
        }

        if ($this->scopeBuilder->isEmpty()) {
            throw new \UnexpectedValueException('scope is empty');
        }
    }

    /**
     * 認証リクエストで利用するパラメータを取得する
     *
     * @return array<string, int|string>
     */
    public function params(): array
    {
        if (is_null($this->metadata)) {
            throw new \UnexpectedValueException('metadata is empty');
        }

        $params = [
            'redirect_uri'  => $this->metadata->getRedirectUri(),
            'response_type' => $this->responseType,
            'scope'         => $this->getScope(),
            'client_id'     => $this->metadata->getClientId(),
        ];

        if (!is_null($this->state)) {
            $params['state'] = $this->state;
        }
        if (!is_null($this->maxAge)) {
            $params['max_age'] = $this->maxAge;
        }
        if (!is_null($this->nonce)) {
            $params['nonce'] = $this->nonce;
        }
        if (!is_null($this->codeChallenge)) {
            $params['code_challenge'] = $this->codeChallenge;
        }

        return $params;
    }

    /**
     * クライアントのメタ情報を設定する
     *
     * @param ClientMetadata $metadata
     * @return AuthenticationRequestProperty
     */
    public function setMetadata(ClientMetadata $metadata): self
    {
        $this->metadata = $metadata;
        return $this;
    }

    /**
     * レスポンスタイプを取得する
     *
     * @return string
     */
    public function getResponseType(): string
    {
        return $this->responseType;
    }

    /**
     * ステートを取得する
     *
     * @return string|null
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * CSRF対策のために利用し、ランダム文字列を設定する
     *
     * @param string $state
     * @return AuthenticationRequestProperty
     */
    public function setState(string $state): self
    {
        $this->state = $state;
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
     * @return AuthenticationRequestProperty
     */
    public function setNonce(string $nonce): self
    {
        $this->nonce = $nonce;
        return $this;
    }

    /**
     * codeChallenge を取得する
     *
     * @return null|string
     */
    public function getCodeChallenge()
    {
        return $this->codeChallenge;
    }

    /**
     * PKCE 対策のために利用し、codeVerifier を元に計算した値を設定する
     *
     * @param string $codeChallenge
     * @return AuthenticationRequestProperty
     */
    public function setCodeChallenge(string $codeChallenge): self
    {
        $this->codeChallenge = $codeChallenge;
        return $this;
    }

    /**
     * トークン有効秒数を取得する
     *
     * @return int|null
     */
    public function getMaxAge()
    {
        return $this->maxAge;
    }

    /**
     * トークン有効秒数を設定する
     *
     * @param int $maxAge
     * @return AuthenticationRequestProperty
     */
    public function setMaxAge(int $maxAge): self
    {
        $this->maxAge = $maxAge;
        return $this;
    }

    /**
     * スコープを追加する
     *
     * @param string ...$scope
     * @return AuthenticationRequestProperty
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
