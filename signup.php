<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width">
<link rel="stylesheet" href="css/main.css" />
<link href="css/bootstrap.min.css" rel="stylesheet">
<style>div{padding: 10px;font-size:16px;}</style>
<title>ユーザー登録</title>
</head>
<body>

<header>
  <nav class="navbar navbar-default">ユーザー登録</nav>
</header>

<form name="form1" action="register.php" method="post">
名前：<input type="text" name="name" />
メールアドレス：<input type="email" name="email" />
パスワード：<input type="password" name="password" />
<input type="submit" value="登録" />
</form>

</body>
</html>