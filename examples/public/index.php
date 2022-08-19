<?php declare(strict_types=1);

use GameWith\Oidc\Client;
use GameWith\Oidc\ClientMetadata;
use GameWith\Oidc\Property\AuthenticationRequestProperty;
use GameWith\Oidc\Property\ExchangeProperty;
use GameWith\Oidc\Property\RefreshProperty;
use GameWith\Oidc\Provider;
use GameWith\Oidc\Util\Pkce;
use GameWith\Oidc\Util\Random;

require __DIR__ . '/../../vendor/autoload.php';

$settings = require __DIR__ . '/../settings.php';

$routes = [
    // 機能: ホーム画面
    '/' => [
        'GET' => function () {
            render('index');
        }
    ],
    // 機能: 認証リクエスト必要なパラメータをセットして認証または同意ページに遷移します
    '/authorize' => [
        'GET' => function () use ($settings) {
            $client = getClient($settings);
            $property = new AuthenticationRequestProperty();
            $codeVerifier = Pkce::generateCodeVerifier();
            $property->addScope('openid', 'profile')
                ->setMaxAge(600)
                ->setState(Random::str())
                ->setNonce(Random::str())
                ->setCodeChallenge(
                    Pkce::createCodeChallenge($codeVerifier)
                );
            $_SESSION['code_verifier'] = $codeVerifier;
            $_SESSION['state'] = $property->getState();
            $_SESSION['nonce'] = $property->getNonce();
            $client->sendAuthenticationRequest($property)->redirect();
        },
    ],
    // 機能: 認証リクエストの結果を受け取り、トークン発行
    '/callback' => [
        'GET' => function () use ($settings) {
            $state = $_SESSION['state'] ?? null;
            $codeVerifier = $_SESSION['code_verifier'] ?? null;
            $nonce = $_SESSION['nonce'] ?? null;

            foreach (['state', 'code_verifier', 'nonce'] as $key) {
                if (array_key_exists($key, $_SESSION)) {
                    unset($_SESSION[$key]);
                }
            }

            $client = getClient($settings);

            $code = $client->receiveAuthenticationRequest($state);

            $property = new ExchangeProperty($code);
            $property->addScope('openid', 'profile');
            if (is_string($codeVerifier)) {
                $property->setCodeVerifier($codeVerifier);
            }
            $token = $client->exchange($property);

            $_SESSION['access_token'] = $token->getAccessToken();
            $_SESSION['refresh_token'] = $token->getRefreshToken();

            $idToken = null;
            if (!is_null($token->getIdToken())) {
                $idToken = $token->parseIdToken($nonce);
            }
            render('token', [
                'title'    => 'コールバック&トークン発行',
                'token'    => $token,
                'idToken'  => $idToken
            ]);
        }
    ],
    // 機能: ユーザー情報リクエストAPI にリクエストし、レスポンスを表示する
    '/userinfo' => [
        'GET' => function () use ($settings) {
            if (!isset($_SESSION['access_token'])) {
                throw new \OutOfBoundsException('access_token is invalid');
            }
            $client = getClient($settings);
            $userinfo = $client->userInfoRequest($_SESSION['access_token']);
            render('userinfo', [
                'userinfo' => $userinfo
            ]);
        },
    ],
    // 機能: リフレッシュリクエストAPI にリクエストし、トークンを更新する
    '/refresh' => [
        'GET' => function () use ($settings) {
            if (!isset($_SESSION['refresh_token'])) {
                throw new \OutOfBoundsException('refresh_token is invalid');
            }
            $client = getClient($settings);
            $property = new RefreshProperty($_SESSION['refresh_token']);
            $property->addScope('openid', 'profile');
            $token = $client->refresh($property);
            $idToken = null;
            if (!is_null($token->getIdToken())) {
                $idToken = $token->parseIdToken();
            }
            $_SESSION['access_token'] = $token->getAccessToken();
            $_SESSION['refresh_token'] = $token->getRefreshToken();

            render('token', [
                'title'    => 'トークン更新',
                'token'    => $token,
                'idToken'  => $idToken
            ]);
        }
    ]
];


try {
    session_start();
    date_default_timezone_set('Asia/Tokyo');
    $url = parse_url($_SERVER['REQUEST_URI']);
    if (!$url) {
        throw new \Exception('Invalid uri');
    }
    $path = $url['path'];
    $method = $_SERVER['REQUEST_METHOD'];
    if (!isset($routes[$path][$method])) {
        throw new \Exception('Invalid page');
    }
    $routes[$path][$method]();
} catch (\Throwable $e) {
    http_response_code(500);
    header('Content-Type: text/html');
    render('error', [
        'trace'   => $e->getTraceAsString(),
        'message' => $e->getMessage(),
    ]);
}

/**
 * @param array $settings
 * @return Client
 */
function getClient(array $settings): Client
{
    $metadata = new ClientMetadata(
        $settings['client']['client_id'],
        $settings['client']['client_secret'],
        $settings['client']['redirect_uri']
    );
    $provider = new Provider($settings['provider']);
    return new Client($metadata, $provider);
}

function h(string $v): string
{
    return htmlspecialchars($v, ENT_QUOTES | ENT_HTML5, "UTF-8");
}

function render(string $fileName, array $data = [])
{
    extract($data, EXTR_REFS);
    ob_start();
    include __DIR__ .
        DIRECTORY_SEPARATOR .
        '..' .
        DIRECTORY_SEPARATOR .
        'views' .
        DIRECTORY_SEPARATOR .
        basename($fileName) .
        '.php';
    echo ob_get_clean();
}
