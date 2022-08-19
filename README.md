<p align="center">
    <img src=".github/logo.png">
</p>

# GameWith OIDC SDK For PHP
[![ci](https://github.com/GameWith/gamewith-oidc-sdk-for-php/actions/workflows/ci.yml/badge.svg)](https://github.com/GameWith/gamewith-oidc-sdk-for-php/actions/workflows/ci.yml)

GameWith アカウント連携の PHP SDK です。[OpenID Connect Core 1.0](https://openid.net/specs/openid-connect-core-1_0.html) に基づいて実装しています。

現在は **Authorization Code Flow** のみ対応しています。

## インストール

事前に Composer をインストールしていただく必要が御座います。
以下のリンクからダウンロードおよびインストールのセットアップをしてください。

[Download Composer](https://getcomposer.org/download)

composer.json ファイルに以下の内容を定義してください。

```json
{
    "require": {
        "gamewith/gamewith-oidc-sdk": "^1.0"
    },
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/gamewith/gamewith-oidc-sdk-for-php"
        }
    ]
}
```

最後に以下のコマンドを実行すると GameWith OIDC SDK のインストールが完了いたします。

```console
$ composer install
```

## 利用方法

実装サンプルがありますので、[こちらのリンク](./examples)からご参照ください。

## PHP サポートバージョン

| PHP バージョン |
| --- |
| 7.0 |
| 7.1 |
| 7.2 |
| 7.3 |
| 7.4 |
| 8.0 |
| 8.1 |

## ライセンス

GameWith OIDC SDK For PHP は MIT ライセンスになります。詳細は [LICENSE](./LICENSE) をご参照ください。
