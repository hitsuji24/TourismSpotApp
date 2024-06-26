-- spotsテーブル
INSERT INTO spots (name, description, main_latitude, main_longitude, address, creator, view_latitude, view_longitude, created_at)
VALUES
  ('東京タワー', '東京のシンボル的な電波塔。', 35.65857, 139.74545, '東京都港区芝公園4-2-8', '東京都', 35.65857, 139.74545, '2023-06-08 10:00:00'),
  ('浅草寺', '東京都内最古の寺院。', 35.71475, 139.79661, '東京都台東区浅草2-3-1', '東京都', 35.71475, 139.79661, '2023-06-08 11:00:00'),
  ('国会議事堂', '日本の立法機関である国会の建物。', 35.67584, 139.74477, '東京都千代田区永田町1-7-1', '東京都', 35.67584, 139.74477, '2023-06-08 12:00:00');

-- worksテーブル 
INSERT INTO works (title, description, release_date, created_at)
VALUES
  ('鬼滅の刃', '大正時代を舞台に、主人公・竈門炭治郎が家族を殺した鬼を討伐する物語。', '2016-02-15', '2023-06-08 13:00:00'),
  ('君の名は。', '千年ぶりとなる彗星の来訪を一か月後に控えた日本。山深い田舎町に暮らす女子高校生の三葉は、東京で暮らす男子高校生、瀧と奇妙な夢を見る。', '2016-08-26', '2023-06-08 14:00:00'),
  ('天気の子', '離島から家出し、東京にやってきた帆高。しかし生活はすぐに困窮し、孤独な日々の果てにようやく見つけた仕事は、怪しげなオカルト雑誌のライター業だった。', '2019-07-19', '2023-06-08 15:00:00');

-- spot_workテーブル
INSERT INTO spot_work (spot_id, work_id, created_at) 
VALUES
  (1, 1, '2023-06-08 16:00:00'),
  (1, 2, '2023-06-08 16:00:00'),
  (2, 1, '2023-06-08 17:00:00'),
  (3, 3, '2023-06-08 18:00:00');

-- imagesテーブル
INSERT INTO images (spot_id, url, created_at)
VALUES 
  (1, 'https://example.com/tokyo_tower_1.jpg', '2023-06-08 19:00:00'),
  (1, 'https://example.com/tokyo_tower_2.jpg', '2023-06-08 19:00:00'),
  (2, 'https://example.com/sensoji_temple_1.jpg', '2023-06-08 20:00:00'),
  (3, 'https://example.com/diet_building_1.jpg', '2023-06-08 21:00:00');
