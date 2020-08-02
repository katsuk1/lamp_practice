-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- ホスト: mysql
-- サーバのバージョン： 5.7.31
-- PHP のバージョン: 7.2.32

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

--
-- データベース: `sample`
--

-- --------------------------------------------

--
-- テーブルの構造 `purchase_histories`
--

CREATE TABLE `purchase_histories` (
  `history_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_price` int(11) NOT NULL,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- テーブルの構造 `purchase_details`
--

CREATE TABLE `purchase_details` (
  `detail_id` int(11) NOT NULL,
  `history_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `amount` int(11) NOT NULL,
  `price` int(11) NOT NULL,
  `sub_total` int(11) NOT NULL,
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- ダンプしたテーブルのインデックス
--

--
-- テーブルのインデックス `purchase_histories`
--
ALTER TABLE `purchase_histories`
  ADD PRIMARY KEY (`history_id`),
  ADD KEY `user_id` (`user_id`);

--
-- テーブルのインデックス `purchase_details`
--
ALTER TABLE `purchase_details`
  ADD PRIMARY KEY (`detail_id`);
  ADD KEY `history_id` (`history_id`);
  ADD KEY `item_id` (`item_id`);

--
-- ダンプしたテーブルのAUTO_INCREMENT
--

--
-- テーブルのAUTO_INCREMENT `purchase_histories`
--
ALTER TABLE `purchase_histories`
  MODIFY `history_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- テーブルのAUTO_INCREMENT `purchase_details`
--
ALTER TABLE `purchase_details`
  MODIFY `detail_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;


--
-- ダンプしたテーブルの制約
--

--
-- テーブルの制約 `purchase_histories`
--
ALTER TABLE `purchase_histories`
  ADD CONSTRAINT `purchase_histories_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`),

--
-- テーブルの制約 `purchase_details`
--
ALTER TABLE `purchase_details`
  ADD CONSTRAINT `purchase_details_ibfk_1` FOREIGN KEY (`history_id`) REFERENCES `purchase_histories` (`history_id`),
  ADD CONSTRAINT `purchase_details_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `items` (`item_id`);
COMMIT;
