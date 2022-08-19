<?php declare(strict_types=1);

namespace GameWith\Oidc\Util;

/**
 * Class ScopeBuilder
 * @package GameWith\Oidc\Util
 */
class ScopeBuilder
{
    /**
     * @var array<string>
     */
    private $scopes = [];

    /**
     * ScopeBuilder constructor.
     *
     * @param string ...$scopes
     */
    private function __construct(string ...$scopes)
    {
        $this->add(...$scopes);
    }

    /**
     * スコープインスタンスの生成
     *
     * @param string ...$scopes
     * @return ScopeBuilder
     */
    public static function make(string ...$scopes): self
    {
        return new self(...$scopes);
    }

    /**
     * 追加したスコープをビルドする
     *
     * @return string
     */
    public function build(): string
    {
        return implode(' ', $this->scopes);
    }

    /**
     * スコープの追加
     *
     * @param string ...$scopes
     * @return ScopeBuilder
     */
    public function add(string ...$scopes): self
    {
        foreach ($scopes as $scope) {
            if ($this->exists($scope)) {
                continue;
            }
            if (!$this->validate($scope)) {
                throw new \UnexpectedValueException(sprintf('%s is invalid scope', $scope));
            }
            $this->scopes[] = $scope;
        }
        return $this;
    }

    /**
     * スコープに不正な文字列が存在するか検証する
     *
     * @param string $scope
     * @return bool
     */
    public function validate(string $scope): bool
    {
        if (preg_match('/^[a-zA-Z0-9_\-\.]+$/', $scope)) {
            return true;
        }
        return false;
    }

    /**
     * 既に同じスコープが存在するかチェックする
     *
     * @param string $scope
     * @return bool
     */
    public function exists(string $scope): bool
    {
        return in_array($scope, $this->scopes, true);
    }

    /**
     * スコープが空か判定する
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->scopes);
    }
}
