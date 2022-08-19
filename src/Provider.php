<?php declare(strict_types=1);

namespace GameWith\Oidc;

/**
 * Class Provider
 * @package GameWith\Oidc
 */
class Provider
{
    /**
     * @var array<string>
     */
    private $requiredAttributes = [
        'issuer',
        'authorization_endpoint',
        'token_endpoint',
        'userinfo_endpoint',
        'jwks_endpoint',
    ];

    /**
     * @var array<string, string>
     */
    private $attributes = [];

    /**
     * Provider constructor.
     *
     * @param array<string, string> $attributes
     */
    public function __construct(array $attributes)
    {
        if (empty($attributes)) {
            throw new \UnexpectedValueException('Attributes are required');
        }
        foreach ($attributes as $key => $value) {
            if (!in_array($key, $this->requiredAttributes, true)) {
                continue;
            }
            if (empty($value)) {
                throw new \UnexpectedValueException('Attribute ' . $key . ' is empty');
            }
            $this->attributes[$key] = $value;
        }
        $includedKeys = array_keys($this->attributes);
        $missingKeys = array_diff($this->requiredAttributes, $includedKeys);
        if (!empty($missingKeys)) {
            throw new \UnexpectedValueException('Missing attributes: ' . implode(', ', $missingKeys));
        }
    }

    /**
     * 発行者の取得をする
     *
     * @return string
     */
    public function getIssuer(): string
    {
        return $this->attributes['issuer'];
    }

    /**
     * 認証(認可)リクエストのエンドポイントを取得する
     *
     * @return string
     */
    public function getAuthorizationEndpoint(): string
    {
        return $this->attributes['authorization_endpoint'];
    }

    /**
     * トークンリクエストのエンドポイントを取得する
     *
     * @return string
     */
    public function getTokenEndpoint(): string
    {
        return $this->attributes['token_endpoint'];
    }

    /**
     * ユーザー情報リクエストのエンドポイントを取得する
     *
     * @return string
     */
    public function getUserinfoEndpoint(): string
    {
        return $this->attributes['userinfo_endpoint'];
    }

    /**
     * Jwks エンドポイントを取得する
     *
     * @return string
     */
    public function getJwksEndpoint(): string
    {
        return $this->attributes['jwks_endpoint'];
    }
}
