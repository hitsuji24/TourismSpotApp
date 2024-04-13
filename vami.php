<?php
include("funcs.php");

if (isset($_GET['id'])) {
    $spot_id = $_GET['id'];

    try {
        $pdo = db_conn();
        $stmt = $pdo->prepare("SELECT * FROM spots WHERE id = :id");
        $stmt->bindValue(':id', $spot_id, PDO::PARAM_INT);
        $stmt->execute();
        $spot = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$spot) {
            echo "指定されたスポットが見つかりません。";
            exit;
        }
    } catch (PDOException $e) {
        echo "データベースエラー: " . $e->getMessage();
        exit;
    }
} else {
    echo "スポットIDが指定されていません。";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>バミる - <?= h($spot['name']) ?></title>
    <script src="https://aframe.io/releases/1.3.0/aframe.min.js"></script>
    <script src="https://raw.githack.com/AR-js-org/AR.js/master/aframe/build/aframe-ar.js"></script>
</head>
<body style="margin: 0; overflow: hidden;">
    <a-scene embedded arjs>
        <a-camera id="camera" gps-camera rotation-reader></a-camera>
        <a-image id="spot-image" src="<?= h($spot['ar_image_url']) ?>" scale="5 5 5" transparent="true"></a-image>
    </a-scene>
    <button id="vami-button" style="position: absolute; bottom: 20px; left: 50%; transform: translateX(-50%);">バミる</button>

    <script>
        const camera = document.getElementById('camera');
        const vamiButton = document.getElementById('vami-button');

        vamiButton.addEventListener('click', function() {
            navigator.geolocation.getCurrentPosition(function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;

                const xhr = new XMLHttpRequest();
                xhr.open('POST', 'spot_view_update.php');
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        console.log('視点の位置情報を更新しました。');
                        alert('バミりました！');
                        window.location.href = 'ar_view.php?id=<?= $spot['id'] ?>';
                    } else {
                        console.log('視点の位置情報の更新に失敗しました。');
                    }
                };
                xhr.send(`spot_id=<?= $spot['id'] ?>&lat=${lat}&lng=${lng}`);
            }, function() {
                console.log('現在地の取得に失敗しました。');
            });
        });
    </script>
</body>
</html>
