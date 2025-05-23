INSERT INTO `agent` (`id`, `email`, `roles`, `password`, `first_name`, `last_name`, `enabled`, `username`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted`) VALUES (NULL, 'admin@gmail.com', '[]', '$2y$13$LLphpfW94JQN0Rjr7gvwYOV4cvZTq7Vok4Uh.M8CdxPakgdlUcjaK', 'admin', 'admin', '1', 'admin', '2024-12-28 15:28:05', 'admin', NULL, NULL, '0');
INSERT INTO `agent_type` (`id`, `name`) VALUES (NULL, 'Administrateur'), (NULL, 'Professeur') , (NULL, 'Utilisateur');
INSERT INTO `role` (`id`, `name`, `label`, `description`) VALUES (NULL, 'Professeur', 'Professeur', 'Professeur');
INSERT INTO `formula_type` (`id`, `name`) VALUES (NULL, 'Normal'), (NULL, 'intensif');
INSERT INTO `module` (`id`, `name`) VALUES ('1', 'ARABE'), ('2', 'CORAN');

INSERT INTO `formula` (type_id, hours_per_week, total_hours, price, module_id) VALUES
(1, 1, 4, 25, 1),  -- Forfait 4h (1h/semaine), 25 €
(1, 2, 8, 50, 1),  -- Forfait 8h (2h/semaine), 50 €
(1, 3, 12, 75, 1), -- Forfait 12h (3h/semaine), 75 €
(1, 4, 16, 100, 1), -- Forfait 16h (4h/semaine), 100 €
(1, 5, 20, 120, 1); -- Forfait 20h (5h/semaine), 120 €

INSERT INTO `formula` (type_id, hours_per_week, total_hours, price, module_id) VALUES
(1, 1, 4, 22, 2),  -- Forfait 4h (1h/semaine), 22 €
(1, 2, 8, 44, 2),  -- Forfait 8h (2h/semaine), 44 €
(1, 3, 12, 64, 2), -- Forfait 12h (3h/semaine), 64 €
(1, 4, 16, 85, 2), -- Forfait 16h (4h/semaine), 85 €
(1, 5, 20, 100, 2); -- Forfait 20h (5h/semaine), 100 €

INSERT INTO `formula` (type_id, hours_per_week, total_hours, price, module_id) VALUES
(2, 10, 40, 210, 1), -- Forfait 40h, 210 €
(2, 15, 60, 300, 1); -- Forfait 60h, 300 €


INSERT INTO `session_type` (`id`, `name`) VALUES (NULL, 'Particuliers'), (NULL, 'Binôme') , (NULL, 'Groupe');


INSERT INTO `formula_session_type` (formula_id, type_id, price) VALUES
(1, 1, 25),  -- Formula 1 (4h, 1h/week), Particuliers
(1, 2, 16),  -- Formula 1 (4h, 1h/week), Binôme
(1, 3, 14),  -- Formula 1 (4h, 1h/week), Groupe
(2, 1, 50),  -- Formula 2 (8h, 2h/week), Particuliers
(2, 2, 32),  -- Formula 2 (8h, 2h/week), Binôme
(2, 3, 28),  -- Formula 2 (8h, 2h/week), Groupe
(3, 1, 75),  -- Formula 3 (12h, 3h/week), Particuliers
(3, 2, 48),  -- Formula 3 (12h, 3h/week), Binôme
(3, 3, 42),  -- Formula 3 (12h, 3h/week), Groupe
(4, 1, 100), -- Formula 4 (16h, 4h/week), Particuliers
(4, 2, 64),  -- Formula 4 (16h, 4h/week), Binôme
(4, 3, 52),  -- Formula 4 (16h, 4h/week), Groupe
(5, 1, 120), -- Formula 4 (16h, 4h/week), Particuliers
(5, 2, 80),  -- Formula 4 (16h, 4h/week), Binôme
(5, 3, 64);  -- Formula 4 (16h, 4h/week), Groupe


INSERT INTO `formula_session_type` (formula_id, type_id, price) VALUES
(12, 1, 210), -- Formula 5 (40h, 10h/week), Particuliers
(12, 2, 150), -- Formula 5 (40h, 10h/week), Binôme
(12, 3, 120), -- Formula 5 (40h, 10h/week), Groupe
(13, 1, 300), -- Formula 6 (60h, 15h/week), Particuliers
(13, 2, 230), -- Formula 6 (60h, 15h/week), Binôme
(13, 3, 170); -- Formula 6 (60h, 15h/week), Groupe



INSERT INTO `formula_session_type` (formula_id, type_id, price) VALUES
-- Formula 1 (4h, 1h/week)
(6, 1, 22),  -- Particuliers
(6, 2, 16),  -- Binôme
(6, 3, 12),  -- Groupe

-- Formula 2 (8h, 2h/week)
(7, 1, 44),  -- Particuliers
(7, 2, 30),  -- Binôme
(7, 3, 25),  -- Groupe

-- Formula 3 (12h, 3h/week)
(8, 1, 64),  -- Particuliers
(8, 2, 45),  -- Binôme
(8, 3, 35),  -- Groupe

-- Formula 4 (16h, 4h/week)
(9, 1, 85),  -- Particuliers
(9, 2, 64),  -- Binôme
(9, 3, 48),  -- Groupe

-- Formula 5 (20h, 5h/week)
(10, 1, 100), -- Particuliers
(10, 2, 75),  -- Binôme
(10, 3, 58);  -- Groupe


INSERT INTO `session_request_status` (`id`, `name`) VALUES (NULL, 'En attente de paiement');
INSERT INTO `session_request_status` (`id`, `name`) VALUES (NULL, 'Acceptée'), (NULL, 'Refusée');

INSERT INTO `agent_type` (`id`, `name`) VALUES
(1, 'Administrateur'),
(2, 'Professeur'),
(3, 'Utilisateur');

INSERT INTO `payment_type` (`id`, `name`) VALUES ('1', 'Versement bancaire'), ('2', 'Paiement en ligne (PayPal)');

INSERT INTO counter (id, count, prefix, module) VALUES ('1', '0', 'CLASSE', 'group');

INSERT INTO `group_request_type` (`id`, `name`) VALUES ('1', 'Groupe personnalisé'), ('2', 'Groupe existant');
INSERT INTO `session_line_status` (`id`, `name`) VALUES ('2', 'Annulée');


UPDATE `formula` SET `avg_session` = '1' WHERE `formula`.`id` = 1;
UPDATE `formula` SET `avg_session` = '1.5' WHERE `formula`.`id` = 2;
UPDATE `formula` SET `avg_session` = '1' WHERE `formula`.`id` = 2;
UPDATE `formula` SET `avg_session` = '1.5' WHERE `formula`.`id` = 3;
UPDATE `formula` SET `avg_session` = '1.5' WHERE `formula`.`id` = 4;
UPDATE `formula` SET `avg_session` = '1.50' WHERE `formula`.`id` = 5;
UPDATE `formula` SET `avg_session` = '2' WHERE `formula`.`id` = 12;
UPDATE `formula` SET `avg_session` = '2' WHERE `formula`.`id` = 13;


UPDATE `formula` SET `avg_session` = '1' WHERE `formula`.`id` = 6;
UPDATE `formula` SET `avg_session` = '1' WHERE `formula`.`id` = 7;
UPDATE `formula` SET `avg_session` = '1.5' WHERE `formula`.`id` = 8;
UPDATE `formula` SET `avg_session` = '1.5' WHERE `formula`.`id` = 9;
UPDATE `formula` SET `avg_session` = '1.5' WHERE `formula`.`id` = 10;

UPDATE `formula` SET `avg_session` = '2' WHERE `formula`.`id` = 4;
UPDATE `formula` SET `avg_session` = '2' WHERE `formula`.`id` = 5;
UPDATE `formula` SET `avg_session` = '2' WHERE `formula`.`id` = 9;
UPDATE `formula` SET `avg_session` = '2' WHERE `formula`.`id` = 10;

UPDATE `formula` SET `days_per_week` = '1' WHERE `formula`.`id` = 1;
UPDATE `formula` SET `days_per_week` = '2' WHERE `formula`.`id` = 2;
UPDATE `formula` SET `days_per_week` = '2' WHERE `formula`.`id` = 3;
UPDATE `formula` SET `days_per_week` = '2' WHERE `formula`.`id` = 4;
UPDATE `formula` SET `days_per_week` = '3' WHERE `formula`.`id` = 5;
UPDATE `formula` SET `days_per_week` = '5' WHERE `formula`.`id` = 13;
UPDATE `formula` SET `days_per_week` = '6' WHERE `formula`.`id` = 13;

UPDATE `formula` SET `days_per_week` = '1' WHERE `formula`.`id` = 6;
UPDATE `formula` SET `days_per_week` = '2' WHERE `formula`.`id` = 7;
UPDATE `formula` SET `days_per_week` = '2' WHERE `formula`.`id` = 8;
UPDATE `formula` SET `days_per_week` = '2' WHERE `formula`.`id` = 9;
UPDATE `formula` SET `days_per_week` = '3' WHERE `formula`.`id` = 10;

