INSERT INTO `formula` (`id`, `price`, `name`) VALUES ('1', '7', 'Individuel'), ('2', '6', 'Binôme'), ('3', '5', 'Groupe');
INSERT INTO `formula_type` (`id`, `name`) VALUES (NULL, 'Forfait/Cours'), (NULL, 'Autre');
INSERT INTO `formula` (`id`, `price`, `name`, `type_id`) VALUES (NULL, '8', 'Alphabétisation PDF', '2');
INSERT INTO `language` (`id`, `name`) VALUES (NULL, 'Français'), (NULL, 'Arabe');