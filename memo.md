私は今、アニメの聖地を探せるウェブサイトを作成しています。登録したスポットの一覧表示をするために、下記の要件を満たすページをHTML、CSS、Javascript、PHPを使って作成してください。
コードを管理しやすいよう、複数のファイルを作成することも可能です。

前提：
* スポットは「gs_tourismspot」というデータベースの中の、「spots」というテーブルに保存されています
* テーブルのカラムは、id, name, description, category, main_latitude, main_longitude, address, creator, view_latitude, view_longitude, created_atです

要件：
* データベースに登録されているスポットを一覧表示する
* スポットはキーワード検索が可能
* キーワード検索の検索対象範囲は、name, description, addressです
* スポットは並び替えが可能です
* 並び替えの条件は、登録日順か距離順です
* 距離順を選んだ場合は、ユーザーの現在地から近いものを表示します
* スポットはカテゴリーで絞り込みが可能です
* カテゴリーは、アニメ、漫画、映画、アート、歴史、その他 です