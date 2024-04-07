// ユーザーの現在位置を取得
navigator.geolocation.getCurrentPosition(function(position) {
    var latitude = position.coords.latitude;
    var longitude = position.coords.longitude;
    
    // 現在位置をフォームに追加
    var form = document.querySelector('form');
    var latitudeInput = document.createElement('input');
    latitudeInput.type = 'hidden';
    latitudeInput.name = 'latitude';
    latitudeInput.value = latitude;
    form.appendChild(latitudeInput);
    
    var longitudeInput = document.createElement('input');
    longitudeInput.type = 'hidden';
    longitudeInput.name = 'longitude';
    longitudeInput.value = longitude;
    form.appendChild(longitudeInput);
});
