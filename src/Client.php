<?php declare(strict_types=1);

namespace GameWith\Oidc;

use GameWith\Oidc\Exception\InvalidResponseException;
use GameWith\Oidc\Property\AuthenticationRequestProperty;
use GameWith\Oidc\Property\ExchangeProperty;
use GameWith\Oidc\Property\RefreshProperty;
use GameWith\Oidc\Util\FilterInput;
use GameWith\Oidc\Util\Json;
use GameWith\Oidc\Util\RedirectResponse;
use GuzzleHttp\ClientInterface;

/**
 * Class Client
 * @package GameWith\Oidc
 */
class Client
{
    const VERSION = '1.0.0';

    /**
     * @var ClientMetadata
     */
    private $metadata;
    /**
     * @var ClientInterface
     */
    private $client;
    /**
     * @var Provider
     */
    private $provider;

    /**
     * Client constructor.
     *
     * @param ClientMetadata $metadata
     * @param Provider $provider
     */
    public function __construct(
        ClientMetadata $metadata,
        Provider $provider
    ) {
        $this->metadata = $metadata;
        $this->provider = $provider;
        $this->client = new \GuzzleHttp\Client([
            'timeout' => 5,
            'verify'  => true,
            'headers' => [
                'User-Agent' => sprintf('GameWithOidcSDK/%s PHP/%s', self::VERSION, PHP_VERSION),
            ],
        ]);
    }

    /**
     * HttpClient をセットする
     *
     * @param ClientInterface $client
     * @return void
     */
    public function setHttpClient(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * 認証リクエストを行う
     *
     * @param AuthenticationRequestProperty $property
     * @return RedirectResponse
     */
    public function sendAuthenticationRequest(
        AuthenticationRequestProperty $property
    ): RedirectResponse {
        $property->setMetadata($this->metadata);
        $property->valid();
        $url = sprintf(
            "%s?%s",
            $this->provider->getAuthorizationEndpoint(),
            http_build_query($property->params(), '', '&', PHP_QUERY_RFC3986)
        );
        return new RedirectResponse($url, 302);
    }

    /**
     * 認可コードを受け取る
     *
     * @param string|null $state
     * @return string
     * @throws InvalidResponseException
     */
    public function receiveAuthenticationRequest($state = null): string
    {
        $filterInput = new FilterInput([
            'code'              => FILTER_DEFAULT,
            'state'             => FILTER_DEFAULT,
            'error'             => FILTER_DEFAULT,
            'error_description' => FILTER_DEFAULT,
        ]);

        $params = $filterInput->values(INPUT_GET);

        if (empty($params)) {
            throw new InvalidResponseException('empty query strings');
        }

        if (isset($params['error'])) {
            $message = sprintf(
                'error: %s, error_description: %s',
                $params['error'],
                $params['error_description'] ?? ''
            );
            throw new InvalidResponseException($message);
        }

        if (isset($params['state']) && $params['state'] !== $state) {
            throw new InvalidResponseException('invalid state');
        }

        if (!isset($params['code'])) {
            throw new InvalidResponseException('code is undefined');
        }

        return $params['code'];
    }

    /**
     * 認可コードを元にトークン発行リクエストをする
     *
     * @param ExchangeProperty $property
     * @return Token
     * @throws Exception\JsonErrorException
     * @throws Exception\OidcClientException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function exchange(ExchangeProperty $property): Token
    {
        $property->setMetadata($this->metadata);
        $property->valid();

        $jwks = $this->getJwks();

        $endpoint = $this->provider->getTokenEndpoint();
        $response = $this->client->request('POST', $endpoint, [
            'headers' => [
                'Content-Type'  => 'application/x-www-form-urlencoded',
                'Authorization' => $this->metadata->getAuthorization(),
            ],
            'form_params' => $property->params(),
        ]);

        $body = Json::decode($response->getBody()->getContents(), true);
        return new Token($body, $jwks, $this->provider, $this->metadata);
    }

    /**
     * トークンの更新をする
     *
     * @param RefreshProperty $property
     * @return Token
     * @throws Exception\JsonErrorException
     * @throws InvalidResponseException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function refresh(RefreshProperty $property): Token
    {
        $property->setMetadata($this->metadata);
        $property->valid();

        $jwks = $this->getJwks();

        $endpoint = $this->provider->getTokenEndpoint();
        $response = $this->client->request('POST', $endpoint, [
            'headers' => [
                'Content-Type'  => 'application/x-www-form-urlencoded',
            ],
            'form_params' => $property->params(),
        ]);

        $body = Json::decode($response->getBody()->getContents(), true);
        return new Token($body, $jwks, $this->provider, $this->metadata);
    }

    /**
     * Jwks を取得する
     *
     * @return array<string, array<string, mixed>>
     * @throws Exception\JsonErrorException
     * @throws Exception\InvalidResponseException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getJwks(): array
    {
        $endpoint = $this->provider->getJwksEndpoint();
        $response = $this->client->request('GET', $endpoint);
        $body = Json::decode($response->getBody()->getContents(), true);
        if (!isset($body['keys']) || !is_array($body['keys'])) {
            throw new InvalidResponseException('invalid jwks');
        }
        return $body;
    }

    /**
     * ユーザー情報取得リクエストをする
     *
     * @param string $accessToken
     * @return array<string, mixed>
     * @throws Exception\JsonErrorException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function userInfoRequest(string $accessToken): array
    {
        $endpoint = $this->provider->getUserInfoEndpoint();
        $response = $this->request('GET', $endpoint, $accessToken);
        return Json::decode($response->getBody()->getContents(), true);
    }

    /**
     * 認可情報を組み込んでリクエストをする
     *
     * @param string $method
     * @param string $url
     * @param string $accessToken
     * @param array<string, mixed> $options
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function request(
        string $method,
        string $url,
        string $accessToken,
        array $options = []
    ): \Psr\Http\Message\ResponseInterface {
        $options = array_merge_recursive([
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
            ],
        ], $options);
        return $this->client->request($method, $url, $options);
    }
}
