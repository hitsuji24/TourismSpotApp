<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>アニメ聖地スポット一覧</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>アニメ聖地スポット一覧</h1>
    
    <form action="search2.php" method="get">
        <input type="text" name="keyword" placeholder="キーワードを入力">
        <select name="sort">
            <option value="created_at">登録日順</option>
            <option value="distance">距離順</option>
        </select>
        <select name="category">
            <option value="">すべて</option>
            <option value="アニメ">アニメ</option>
            <option value="漫画">漫画</option>
            <option value="映画">映画</option>
            <option value="アート">アート</option>
            <option value="歴史">歴史</option>
            <option value="その他">その他</option>
        </select>
        <button type="submit">検索</button>
    </form>
    
    <div id="spot-list">
        <?php include 'search2.php'; ?>
    </div>
    
    <script src="script2.js"></script>
</body>
</html>