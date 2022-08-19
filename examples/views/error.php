<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>エラーが発生しました | GameWith アカウント連携サンプル</title>
  <link rel="stylesheet" href="/style.css">
</head>
<body class="layout">
  <header class="l-header">
    <div class="c-container">
      <a href="/" class="c-title"><h1>GameWith アカウント連携サンプル</h1></a>
    </div>
  </header>
  <main class="l-main">
    <div class="c-container">
      <div class="c-box">
        <h2 class="c-box__title">エラーが発生しました</h2>
        <div class="c-box__desc">
          <table class="c-table">
            <tbody>
            <tr class="c-table__row">
              <th class="c-table__title">メッセージ</th>
              <td class="c-table__desc"><?= h($message) ?></td>
            </tr>
            <tr class="c-table__row">
              <th class="c-table__title">トレース</th>
              <td class="c-table__desc"><?= nl2br(h($trace)) ?></td>
            </tr>
          </table>
        </div>
      </div>
      <div class="u-text-center u-mt">
        <a class="c-button c-button--medium u-inline-block" href="/">ホームへ戻る</a>
      </div>
    </div>
  </main>
</body>
</html>