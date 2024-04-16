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
    <title>AR View - <?= h($spot['name']) ?></title>
    <link rel="stylesheet" href="style.css">
    <script src="https://aframe.io/releases/1.3.0/aframe.min.js"></script>
    <script src="https://raw.githack.com/AR-js-org/AR.js/master/aframe/build/aframe-ar.js"></script>
</head>

<!-- GPS強度の表示
<div id="gps-strength" class="gps-strength-icon">GPS強度</div> -->



<body style="margin: 0; overflow: hidden;">
    <a-scene embedded arjs>
        <a-image id="ar-image" src="<?= h($spot['ar_image_url']) ?>" look-at="[gps-camera]" scale="5 5 5" gps-entity-place="latitude: <?= $spot['main_latitude'] ?>; longitude: <?= $spot['main_longitude'] ?>;" visible="false" cursor-listener></a-image> <a-camera gps-camera rotation-reader></a-camera> </a-scene>
    <div id="description" style="display: none; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background-color: white; padding: 10px;">
        <?= h($spot['description']) ?>
    </div>



    <script>
        const arImage = document.querySelector('#ar-image');
        // const description = document.querySelector('#description');

        // arImage.addEventListener('click', () => {
        //     if (description.style.display === 'none') { // 'none'がtrue（非表示）の場合            
        //         description.style.display = 'block'; // 'block'に変更（表示）
        //     } else { // 'none'がfalse（表示）の場合
        //         description.style.display = 'none'; // 'none'に変更（非表示）
        //     }
        // });


        const spotPoint = {
            lat: <?= $spot['main_latitude'] ?>,
            lon: <?= $spot['main_longitude'] ?>
        };

        const viewPoint = {
            lat: <?= $spot['view_latitude'] ?>,
            lon: <?= $spot['view_longitude'] ?>
        };

        const distance = getDistance(spotPoint, viewPoint);
        const bearing = getBearing(spotPoint, viewPoint);

        arImage.setAttribute('look-at', `[gps-camera]`);
        arImage.setAttribute('gps-entity-place', `latitude: ${spotPoint.lat}; longitude: ${spotPoint.lon}`);
        arImage.setAttribute('visible', 'false');


        // 2点間の距離を計算する関数
        // 参考: https://www.movable-type.co.uk/scripts/latlong.html
        function getDistance(pointA, pointB) {
            const R = 6371e3; // metres
            const φ1 = pointA.lat * Math.PI / 180; // φ, λ in radians
            const φ2 = pointB.lat * Math.PI / 180;
            const Δφ = (pointB.lat - pointA.lat) * Math.PI / 180;
            const Δλ = (pointB.lon - pointA.lon) * Math.PI / 180;

            const a = Math.sin(Δφ / 2) * Math.sin(Δφ / 2) +
                Math.cos(φ1) * Math.cos(φ2) *
                Math.sin(Δλ / 2) * Math.sin(Δλ / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

            const d = R * c; // in metres
            return d;
        }


        // 2点間の方位角を計算する関数
        // 参考: https://www.movable-type.co.uk/scripts/latlong.html
        function getBearing(pointA, pointB) {
            const φ1 = pointA.lat * Math.PI / 180; // φ, λ in radians
            const φ2 = pointB.lat * Math.PI / 180;
            const λ1 = pointA.lon * Math.PI / 180;
            const λ2 = pointB.lon * Math.PI / 180;

            const y = Math.sin(λ2 - λ1) * Math.cos(φ2);
            const x = Math.cos(φ1) * Math.sin(φ2) -
                Math.sin(φ1) * Math.cos(φ2) * Math.cos(λ2 - λ1);
            const θ = Math.atan2(y, x);
            const brng = (θ * 180 / Math.PI + 360) % 360; // in degrees
            return brng;
        }


        // GPSの強度を表示する関数
        const gpsStrengthIcon = document.querySelector('#gps-strength');

        function updateGpsStrength(accuracy) {
            let strength = 0;
            if (accuracy < 30) {
                strength = 2;
            } else if (accuracy < 50) {
                strength = 1;
            }
            gpsStrengthIcon.setAttribute('data-strength', strength);
        }

        window.addEventListener('gps-camera-update-position', (event) => {
            const distance = getDistance(viewPoint, {
                lat: event.detail.position.latitude,
                lon: event.detail.position.longitude
            });

            if (distance > 10) {
                // 立ち位置から10m以上離れている場合は非表示
                arImage.setAttribute('visible', 'false');
            } else {
                // 立ち位置から10m以内の場合は表示
                arImage.setAttribute('visible', 'true');
            }
        });
    </script>
</body>

</html>