<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width">
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css">

  <link href="css/bootstrap.min.css" rel="stylesheet">
  <title>ユーザー登録</title>
</head>

<body>
<div class="pageTitle">
<h2>新規登録</h2>
</div>

  <form id="signup-form" action="register.php" method="post">
    <label for="">ユーザー名*</label> <br />
    <input type="text" name="name" id="name" /> <br />
    <label for="email">メールアドレス*</label> <br />
    <input type="email" name="email" id="email" /> <br />
    <label for="password">パスワード*</label> <br />
    <input type="password" name="password" id="pass" /> <br />
    <input class="action-button" type="submit" value="登録" />
  </form>

  <nav class="bottom-nav">
    <a href="index.php" class="nav-item">
      <i class="fas fa-home"></i>
      <span>ホーム</span>
    </a>
    <a href="works.php" class="nav-item">
      <i class="fas fa-film"></i>
      <span>作品</span>
    </a>
    <a href="spot.php" class="nav-item">
      <i class="fas fa-map-marker-alt"></i>
      <span>スポット</span>
    </a>
    <a href="mylist.php" class="nav-item">
      <i class="fas fa-heart"></i>
      <span>マイリスト</span>
    </a>
  </nav>

</body>

</html>