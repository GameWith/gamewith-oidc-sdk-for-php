<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>GameWith アカウント連携サンプル</title>
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
        <li class="c-breadcrumb__item">ホーム</li>
      </ul>
    </div>
  </nav>
  <main class="l-main">
    <div class="c-container">
      <div class="c-box">
        <h3 class="c-box__title">処理の流れ</h3>
        <div class="c-box__desc">
          <ol class="c-order-list">
            <li class="c-order-list__item"><span class="c-badge">examples/settings.php</span>の各種項目を入力してください。</li>
            <li class="c-order-list__item">当画面から「GameWith ログイン」ボタンをクリックしてください。</li>
            <li class="c-order-list__item">GameWithログイン画面およびGameWith同意画面が表示されます。GameWith同意画面が表示されたら<span class="c-badge">許可する</span>または<span class="c-badge">キャンセル</span>を選択してください。</li>
            <li class="c-order-list__item">同意画面で<span class="c-badge">許可する</span>または<span class="c-badge">キャンセル</span>を選択すると、指定されたリダイレクト先に遷移します。成功時はクエリーパラメーターに<span class="c-badge">code&nbsp;(認可コード)</span>が付与されます。エラー時はクエリパラメーターに<span class="c-badge">error, error_description</span>が付与されます。クエリパラメーターに付与されている<span class="c-badge">state</span>はCSRF対策として、同一セッションかリダイレクト先で確認するために利用します。</li>
            <li class="c-order-list__item">GameWithから付与された認可コードを指定してトークンリクエストをします。トークンリクエストが成功するとトークン情報が返却されます。</li>
          </ol>
        </div>
      </div>
      <div class="u-text-center u-mt">
        <a href="/authorize" class="c-button c-button--medium u-inline-block">GameWith ログイン</a>
      </div>
    </div>
  </main>
</body>
</html>