<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ユーザー情報取得 | GameWith アカウント連携サンプル</title>
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
      <li class="c-breadcrumb__item">ユーザー情報取得</li>
    </ul>
  </div>
</nav>
<main class="l-main">
  <div class="c-container">
    <div class="c-box">
      <h2 class="c-box__title">ユーザー情報</h2>
      <div class="c-box__desc">
        <h3 class="c-box__sub-title">レスポンス</h3>
        <table class="c-table">
          <tbody>
          <?php foreach ($userinfo as $key => $val): ?>
            <tr class="c-table__row">
              <th class="c-table__title"><?= h($key) ?></th>
              <td class="c-table__desc"><?= h($val) ?></td>
            </tr>
          <?php endforeach ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</main>
</body>
</html>