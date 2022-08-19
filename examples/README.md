# 実装サンプル

GameWith アカウント連携の PHP SDK を利用した実装サンプルです。

## ディレクトリ構成

```
├── public # ローカルサーバの起動対象ディレクトリです。
│   ├── index.php # 各機能のルーティングおよび機能実装をしています。
│   └── style.css
├── settings.php # クライアント情報・接続情報などの設定をします。
└── views # 各機能の view を定義しています。
    ├── error.php
    ├── index.php
    ├── token.php
    └── userinfo.php
```

## セットアップ

**settings.php** ファイルの設定項目を変更してください。

```php
return [
    'client' => [
        'client_id'     => '[提供された client_id を入力してください]',
        'client_secret' => '[提供された client_secret を入力してください]',
        'redirect_uri'  => '[ご登録された redirect_uri を入力してください]'
    ],
    'provider' => [
        // トークン発行者
        'issuer'                 => '[提供された issuer を入力してください]',
        // 認証リクエストのエンドポイント
        'authorization_endpoint' => '[提供された authorization_endpoint を入力してください]',
        // トークンリクエストのエンドポイント
        'token_endpoint'         => '[提供された token_endpoint を入力してください]',
        // ユーザー情報リクエストのエンドポイント
        'userinfo_endpoint'      => '[提供された userinfo_endpoint を入力してください]',
        // Jwks 取得のエンドポイント
        'jwks_endpoint'          => '[提供された jwks_endpoint を入力してください]',
    ]
];
```

## 起動方法

```console
$ cd ./public
$ php -S localhost:8863
```
