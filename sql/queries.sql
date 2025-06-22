INSERT INTO `formula` (`id`, `price`, `name`) VALUES ('1', '7', 'Individuel'), ('2', '6', 'Binôme'), ('3', '5', 'Groupe');
INSERT INTO `formula_type` (`id`, `name`) VALUES (NULL, 'Forfait/Cours'), (NULL, 'Autre');
INSERT INTO `formula` (`id`, `price`, `name`, `type_id`) VALUES (NULL, '8', 'Alphabétisation PDF', '2');
INSERT INTO `language` (`id`, `name`) VALUES (NULL, 'Français'), (NULL, 'Arabe');

UPDATE `formula` SET `per` = 'H' WHERE `formula`.`id` = 1;
UPDATE `formula` SET `per` = 'H' WHERE `formula`.`id` = 2;
UPDATE `formula` SET `per` = 'H' WHERE `formula`.`id` = 3;
INSERT INTO `formula` (`id`, `price`, `name`, `type_id`, `per`) VALUES (NULL, '20', 'Différé standard', '1', 'M'), (NULL, '15', 'Tilawah & Coran en différé ', '1', 'M');

UPDATE `formula` SET `price_prof` = '7' WHERE `formula`.`id` = 2;
UPDATE `formula` SET `price_prof` = '8' WHERE `formula`.`id` = 3;
UPDATE `formula` SET `price_prof` = '2.5' WHERE `formula`.`id` = 7;
UPDATE `formula` SET `price_prof` = '1' WHERE `formula`.`id` = 8;