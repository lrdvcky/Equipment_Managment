-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Май 16 2025 г., 21:04
-- Версия сервера: 8.0.30
-- Версия PHP: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `equipment_management`
--

-- --------------------------------------------------------

--
-- Структура таблицы `Consumable`
--

CREATE TABLE `Consumable` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text,
  `arrival_date` date DEFAULT NULL,
  `image` longblob,
  `quantity` int DEFAULT NULL,
  `responsible_user_id` int DEFAULT NULL,
  `temporary_responsible_user_id` int DEFAULT NULL,
  `consumable_type_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `Consumable`
--

INSERT INTO `Consumable` (`id`, `name`, `description`, `arrival_date`, `image`, `quantity`, `responsible_user_id`, `temporary_responsible_user_id`, `consumable_type_id`) VALUES
(1, 'Картридж HP 106A', 'Черный картридж для лазерного принтера', '2025-04-01', NULL, 5, 1, 2, 1),
(2, 'Беспроводная мышь Logitech', 'Мышь с USB-адаптером', '2025-04-03', NULL, 10, 2, 3, 2),
(3, 'Клавиатура A4Tech', 'Проводная клавиатура USB', '2025-04-05', NULL, 7, 3, 1, 3);

-- --------------------------------------------------------

--
-- Структура таблицы `ConsumableProperty`
--

CREATE TABLE `ConsumableProperty` (
  `id` int NOT NULL,
  `consumable_id` int NOT NULL,
  `property_name` varchar(100) NOT NULL,
  `property_value` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `ConsumableProperty`
--

INSERT INTO `ConsumableProperty` (`id`, `consumable_id`, `property_name`, `property_value`) VALUES
(1, 1, 'Цвет', 'Черный'),
(2, 1, 'Объём', '1500 страниц'),
(3, 1, 'Тип', 'Лазерный'),
(4, 2, 'Тип соединения', 'Беспроводная'),
(5, 2, 'Цвет', 'Чёрный'),
(6, 2, 'Сенсор', 'Оптический'),
(7, 3, 'Тип подключения', 'Проводная'),
(8, 3, 'Интерфейс', 'USB'),
(9, 3, 'Цвет', 'Чёрный');

-- --------------------------------------------------------

--
-- Структура таблицы `ConsumableType`
--

CREATE TABLE `ConsumableType` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `ConsumableType`
--

INSERT INTO `ConsumableType` (`id`, `name`) VALUES
(1, 'Картридж'),
(2, 'Мышь'),
(3, 'Клавиатура');

-- --------------------------------------------------------

--
-- Структура таблицы `Equipment`
--

CREATE TABLE `Equipment` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `photo` longblob,
  `inventory_number` varchar(50) NOT NULL,
  `room_id` int DEFAULT NULL,
  `responsible_user_id` int DEFAULT NULL,
  `temporary_responsible_user_id` int DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `model_id` int DEFAULT NULL,
  `comment` text,
  `direction_name` varchar(100) DEFAULT NULL,
  `status` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `Equipment`
--

INSERT INTO `Equipment` (`id`, `name`, `photo`, `inventory_number`, `room_id`, `responsible_user_id`, `temporary_responsible_user_id`, `price`, `model_id`, `comment`, `direction_name`, `status`) VALUES
(4, 'Монитор Samsung', NULL, 'INV001', 1, 1, 2, '8500.00', 1, 'Основной монитор в лаборатории', 'Веб-разработка', NULL),
(5, 'ПК Lenovo', NULL, 'INV002', 2, 2, 3, '35000.00', 2, 'Рабочий компьютер преподавателя', NULL, NULL),
(6, 'Принтер HP', NULL, 'INV003', 3, 3, 1, '17000.00', 3, 'На ремонте после поломки картриджа', NULL, NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `EquipmentInventoryCheck`
--

CREATE TABLE `EquipmentInventoryCheck` (
  `equipment_id` int NOT NULL,
  `inventory_check_id` int NOT NULL,
  `checked_by_user_id` int DEFAULT NULL,
  `comment` text,
  `check` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `EquipmentInventoryCheck`
--

INSERT INTO `EquipmentInventoryCheck` (`equipment_id`, `inventory_check_id`, `checked_by_user_id`, `comment`, `check`) VALUES
(4, 1, 1, 'Оборудование в порядке', 0),
(5, 1, 2, 'Проведена проверка, всё функционирует', 0),
(6, 1, 3, 'Устройство на ремонте', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `EquipmentSoftware`
--

CREATE TABLE `EquipmentSoftware` (
  `equipment_id` int NOT NULL,
  `software_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `EquipmentSoftware`
--

INSERT INTO `EquipmentSoftware` (`equipment_id`, `software_id`) VALUES
(5, 1),
(5, 2),
(6, 3);

-- --------------------------------------------------------

--
-- Структура таблицы `InventoryCheck`
--

CREATE TABLE `InventoryCheck` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `InventoryCheck`
--

INSERT INTO `InventoryCheck` (`id`, `name`, `start_date`, `end_date`) VALUES
(1, 'Инвентаризация весна 2025', '2025-03-01', '2025-03-10');

-- --------------------------------------------------------

--
-- Структура таблицы `Model`
--

CREATE TABLE `Model` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `equipment_type` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `Model`
--

INSERT INTO `Model` (`id`, `name`, `equipment_type`) VALUES
(1, 'Samsung S24F350FHI', NULL),
(2, 'Lenovo ThinkCentre M720', NULL),
(3, 'HP LaserJet Pro M404', NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `NetworkSettings`
--

CREATE TABLE `NetworkSettings` (
  `id` int NOT NULL,
  `ip_address` varchar(15) NOT NULL,
  `subnet_mask` varchar(15) DEFAULT NULL,
  `gateway` varchar(15) DEFAULT NULL,
  `dns_servers` varchar(255) DEFAULT NULL,
  `equipment_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `NetworkSettings`
--

INSERT INTO `NetworkSettings` (`id`, `ip_address`, `subnet_mask`, `gateway`, `dns_servers`, `equipment_id`) VALUES
(1, '192.168.0.101', '255.255.255.0', '192.168.0.1', '8.8.8.8,8.8.4.4', 4),
(2, '192.168.0.102', '255.255.255.0', '192.168.0.1', '8.8.8.8', 5),
(3, '192.168.0.103', '255.255.255.0', '192.168.0.1', '1.1.1.1', 6);

-- --------------------------------------------------------

--
-- Структура таблицы `Room`
--

CREATE TABLE `Room` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `short_name` varchar(50) DEFAULT NULL,
  `responsible_user_id` int DEFAULT NULL,
  `temporary_responsible_user_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `Room`
--

INSERT INTO `Room` (`id`, `name`, `short_name`, `responsible_user_id`, `temporary_responsible_user_id`) VALUES
(1, 'Аудитория 422', 'Ауд.422', 1, 2),
(2, 'Аудитория 502', 'Ауд.502', 2, 3),
(3, 'Аудитория 418', 'Ауд.418', 3, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `Software`
--

CREATE TABLE `Software` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `version` varchar(50) DEFAULT NULL,
  `developer_name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `Software`
--

INSERT INTO `Software` (`id`, `name`, `version`, `developer_name`) VALUES
(1, 'Windows 10 Pro', '20H2', NULL),
(2, 'PyCharm', '2022.3', NULL),
(3, 'Adobe Reader', '2023.1', NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `User`
--

CREATE TABLE `User` (
  `id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','teacher','staff') NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `last_name` varchar(50) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `middle_name` varchar(50) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `User`
--

INSERT INTO `User` (`id`, `username`, `password`, `role`, `email`, `last_name`, `first_name`, `middle_name`, `phone`, `address`) VALUES
(1, 'basalaev', '123123', 'admin', 'ivanov@mail.ru', 'Басалаев', 'Александр', 'Иванович', '89123456789', 'г. Пермь, ул. Пушкина, д.1'),
(2, 'suslonova', '123123', 'teacher', 'petrova@mail.ru', 'Суслонова', 'Мария', 'Лазаревна', '89234567890', 'г. Пермь, ул. Ленина, д.10'),
(3, 'subbotina', '123123', 'teacher', 'smirnov@mail.ru', 'Субботина', 'Юлия', 'Александровна', '89345678901', 'г. Пермь, ул. Чехова, д.3');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `Consumable`
--
ALTER TABLE `Consumable`
  ADD PRIMARY KEY (`id`),
  ADD KEY `responsible_user_id` (`responsible_user_id`),
  ADD KEY `temporary_responsible_user_id` (`temporary_responsible_user_id`),
  ADD KEY `consumable_type_id` (`consumable_type_id`);

--
-- Индексы таблицы `ConsumableProperty`
--
ALTER TABLE `ConsumableProperty`
  ADD PRIMARY KEY (`id`),
  ADD KEY `consumable_id` (`consumable_id`);

--
-- Индексы таблицы `ConsumableType`
--
ALTER TABLE `ConsumableType`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `Equipment`
--
ALTER TABLE `Equipment`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `inventory_number` (`inventory_number`),
  ADD KEY `room_id` (`room_id`),
  ADD KEY `responsible_user_id` (`responsible_user_id`),
  ADD KEY `temporary_responsible_user_id` (`temporary_responsible_user_id`),
  ADD KEY `model_id` (`model_id`),
  ADD KEY `idx_equipment_name` (`name`),
  ADD KEY `idx_equipment_inventory_number` (`inventory_number`);

--
-- Индексы таблицы `EquipmentInventoryCheck`
--
ALTER TABLE `EquipmentInventoryCheck`
  ADD PRIMARY KEY (`equipment_id`,`inventory_check_id`),
  ADD KEY `inventory_check_id` (`inventory_check_id`),
  ADD KEY `checked_by_user_id` (`checked_by_user_id`);

--
-- Индексы таблицы `EquipmentSoftware`
--
ALTER TABLE `EquipmentSoftware`
  ADD PRIMARY KEY (`equipment_id`,`software_id`),
  ADD KEY `software_id` (`software_id`);

--
-- Индексы таблицы `InventoryCheck`
--
ALTER TABLE `InventoryCheck`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `Model`
--
ALTER TABLE `Model`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `NetworkSettings`
--
ALTER TABLE `NetworkSettings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ip_address` (`ip_address`),
  ADD KEY `equipment_id` (`equipment_id`),
  ADD KEY `idx_network_ip` (`ip_address`);

--
-- Индексы таблицы `Room`
--
ALTER TABLE `Room`
  ADD PRIMARY KEY (`id`),
  ADD KEY `responsible_user_id` (`responsible_user_id`),
  ADD KEY `temporary_responsible_user_id` (`temporary_responsible_user_id`);

--
-- Индексы таблицы `Software`
--
ALTER TABLE `Software`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `User`
--
ALTER TABLE `User`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `Consumable`
--
ALTER TABLE `Consumable`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `ConsumableProperty`
--
ALTER TABLE `ConsumableProperty`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT для таблицы `ConsumableType`
--
ALTER TABLE `ConsumableType`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `Equipment`
--
ALTER TABLE `Equipment`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT для таблицы `InventoryCheck`
--
ALTER TABLE `InventoryCheck`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `Model`
--
ALTER TABLE `Model`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `NetworkSettings`
--
ALTER TABLE `NetworkSettings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `Room`
--
ALTER TABLE `Room`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `Software`
--
ALTER TABLE `Software`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `User`
--
ALTER TABLE `User`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `Consumable`
--
ALTER TABLE `Consumable`
  ADD CONSTRAINT `consumable_ibfk_1` FOREIGN KEY (`responsible_user_id`) REFERENCES `User` (`id`),
  ADD CONSTRAINT `consumable_ibfk_2` FOREIGN KEY (`temporary_responsible_user_id`) REFERENCES `User` (`id`),
  ADD CONSTRAINT `consumable_ibfk_3` FOREIGN KEY (`consumable_type_id`) REFERENCES `ConsumableType` (`id`);

--
-- Ограничения внешнего ключа таблицы `ConsumableProperty`
--
ALTER TABLE `ConsumableProperty`
  ADD CONSTRAINT `consumableproperty_ibfk_1` FOREIGN KEY (`consumable_id`) REFERENCES `Consumable` (`id`);

--
-- Ограничения внешнего ключа таблицы `Equipment`
--
ALTER TABLE `Equipment`
  ADD CONSTRAINT `equipment_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `Room` (`id`),
  ADD CONSTRAINT `equipment_ibfk_2` FOREIGN KEY (`responsible_user_id`) REFERENCES `User` (`id`),
  ADD CONSTRAINT `equipment_ibfk_3` FOREIGN KEY (`temporary_responsible_user_id`) REFERENCES `User` (`id`),
  ADD CONSTRAINT `equipment_ibfk_6` FOREIGN KEY (`model_id`) REFERENCES `Model` (`id`);

--
-- Ограничения внешнего ключа таблицы `EquipmentInventoryCheck`
--
ALTER TABLE `EquipmentInventoryCheck`
  ADD CONSTRAINT `equipmentinventorycheck_ibfk_1` FOREIGN KEY (`equipment_id`) REFERENCES `Equipment` (`id`),
  ADD CONSTRAINT `equipmentinventorycheck_ibfk_2` FOREIGN KEY (`inventory_check_id`) REFERENCES `InventoryCheck` (`id`),
  ADD CONSTRAINT `equipmentinventorycheck_ibfk_3` FOREIGN KEY (`checked_by_user_id`) REFERENCES `User` (`id`);

--
-- Ограничения внешнего ключа таблицы `EquipmentSoftware`
--
ALTER TABLE `EquipmentSoftware`
  ADD CONSTRAINT `equipmentsoftware_ibfk_1` FOREIGN KEY (`equipment_id`) REFERENCES `Equipment` (`id`),
  ADD CONSTRAINT `equipmentsoftware_ibfk_2` FOREIGN KEY (`software_id`) REFERENCES `Software` (`id`);

--
-- Ограничения внешнего ключа таблицы `NetworkSettings`
--
ALTER TABLE `NetworkSettings`
  ADD CONSTRAINT `networksettings_ibfk_1` FOREIGN KEY (`equipment_id`) REFERENCES `Equipment` (`id`);

--
-- Ограничения внешнего ключа таблицы `Room`
--
ALTER TABLE `Room`
  ADD CONSTRAINT `room_ibfk_1` FOREIGN KEY (`responsible_user_id`) REFERENCES `User` (`id`),
  ADD CONSTRAINT `room_ibfk_2` FOREIGN KEY (`temporary_responsible_user_id`) REFERENCES `User` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
