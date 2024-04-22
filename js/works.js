$(function() {
    // 検索フォームの送信イベント
    $('#search-form').submit(function(event) {
      event.preventDefault(); // フォームのデフォルトの送信を防止
      
      // 検索条件の取得
      var keyword = $('#keyword').val();
      var sort = $('#sort').val();  
      var category = $('#category').val();
      
      // Ajaxリクエストの送信 
      $.ajax({
        url: 'search_works.php',
        type: 'GET',
        data: {
          keyword: keyword,
          sort: sort,
          category: category
        },
        dataType: 'json'
      })
      .done(function(data) {
        $('#works-list').html(data.list);
      })
      .fail(function(jqXHR, textStatus, errorThrown) {
        console.error('検索に失敗しました:', textStatus, errorThrown);
        alert('検索に失敗しました');  
      });
    });
    
    // 並び順とカテゴリの変更イベント
    $('#sort, #category').change(function() {
      $('#search-form').submit();
    });
    
    // 初期表示時に検索を実行
    $('#search-form').submit();
  });