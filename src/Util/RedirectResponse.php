<?php declare(strict_types=1);

namespace GameWith\Oidc\Util;

/**
 * Class RedirectResponse
 * @package GameWith\Oidc\Util
 */
class RedirectResponse
{
    /**
     * @var string
     */
    private $url;

    /**
     * @var int
     */
    private $status;

    /**
     * Redirect constructor.
     *
     * @param string $url
     * @param int $status
     */
    public function __construct(string $url, int $status = 302)
    {
        $this->url = $url;
        $this->status = $status;
    }

    /**
     * リダイレクト対象の URL を取得する
     *
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * リダイレクト時の HTTP Status を取得する
     *
     * @return int
     */
    public function getHttpStatus(): int
    {
        return $this->status;
    }

    /**
     * リダイレクト実行
     *
     * @return void
     */
    public function redirect()
    {
        header('Location: ' . $this->url, true, $this->status);
        exit();
    }
}
