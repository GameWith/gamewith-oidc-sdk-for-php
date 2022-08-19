<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $title ?> | GameWith アカウント連携サンプル</title>
  <link rel="stylesheet" href="/style.css">
</head>
<body class="layout">
  <header class="l-header">
    <div class="c-container">
      <a href="/" class="c-title"><h1>GameWith アカウント連携サンプル</h1></a>
    </div>
  </header>
  <nav class="l-nav">
    <div class="c-container">
      <ul class="c-breadcrumb">
        <li class="c-breadcrumb__item"><a class="c-breadcrumb__item__link" href="/">ホーム</a></li>
        <li class="c-breadcrumb__item"><?= $title ?></li>
      </ul>
    </div>
  </nav>
  <main class="l-main">
    <div class="c-container">
      <div class="c-box u-mb">
        <h2 class="c-box__title">アクション</h2>
        <div class="c-box__desc">
          <a href="/userinfo" target="_blank" class="c-button u-inline-block">ユーザー情報</a>
          <a href="/refresh" class="c-button u-inline-block">トークン更新</a>
        </div>
      </div>
      <div class="c-box">
        <h2 class="c-box__title">トークン情報</h2>
        <div class="c-box__desc">
          <h3 class="c-box__sub-title">トークンレスポンス</h3>
          <table class="c-table">
            <tbody>
            <tr class="c-table__row">
              <th class="c-table__title">access_token</th>
              <td class="c-table__desc"><?= h($token->getAccessToken()) ?></td>
            </tr>
            <tr class="c-table__row">
              <th class="c-table__title">refresh_token</th>
              <td class="c-table__desc"><?= h($token->getRefreshToken()) ?></td>
            </tr>
            <tr class="c-table__row">
              <th class="c-table__title">id_token</th>
              <td class="c-table__desc"><?= h($token->getIdToken()) ?? 'なし' ?></td>
            </tr>
            <tr class="c-table__row">
              <th class="c-table__title">scope</th>
              <td class="c-table__desc"><?= h($token->getScope()) ?></td>
            </tr>
            <tr class="c-table__row">
              <th class="c-table__title">expires_in</th>
              <td class="c-table__desc"><?= h($token->getExpiresIn()) ?>&nbsp;(sec)</td>
            </tr>
            </tbody>
          </table>
        </div>
      </div>
      <?php if (isset($idToken)): ?>
        <div class="c-box u-mt">
          <h2 class="c-box__title">id_token&nbsp;の解析</h2>
          <div class="c-box__desc">
            <h3 class="c-box__sub-title">ヘッダー情報</h3>
            <table class="c-table">
              <tbody>
              <?php foreach ($idToken['header'] as $key => $val): ?>
                <tr class="c-table__row">
                  <th class="c-table__title"><?= h($key) ?></th>
                  <td class="c-table__desc"><?= h($val) ?></td>
                </tr>
              <?php endforeach ?>
              </tbody>
            </table>
            <h3 class="c-box__sub-title u-mt">ペイロード情報</h3>
            <table class="c-table">
              <tbody>
              <?php foreach ($idToken['payload'] as $key => $val): ?>
                <tr class="c-table__row">
                  <th class="c-table__title"><?= h($key) ?></th>
                  <td class="c-table__desc">
                      <?= h($val) ?>
                      <?php if (in_array($key, ['exp', 'iat', 'auth_time'], true)): ?>
                        (<?= date('Y-m-d H:i:s', $val) ?>)
                      <?php endif ?>
                  </td>
                </tr>
              <?php endforeach ?>
              </tbody>
            </table>
          </div>
        </div>
      <?php endif ?>
    </div>
  </main>
</body>
</html>