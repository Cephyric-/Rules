DELIMITER $$
	CREATE PROCEDURE DeleteCategory( IN catorder INT( 32 ) )
		BEGIN
			DECLARE cat_num, cat_count, full_complete, full_count, part_num INT DEFAULT 0;
			
			SELECT `id` FROM `categories` WHERE `order` = catorder INTO cat_num;
			
			WHILE full_complete = 0 DO
				SELECT `id` FROM `rules` WHERE `cat_id` = cat_num LIMIT 1 INTO part_num;
				
				DELETE FROM `rules_text` WHERE `rule_id` = part_num;
				DELETE FROM `rules` WHERE `id` = part_num;
				
				SELECT COUNT( * ) FROM `rules` WHERE `cat_id` = cat_num INTO full_count;
				IF full_count = 0 THEN
					SET full_complete = 1;
				END IF;
			END WHILE;
			
			DELETE FROM `categories_text` WHERE `cat_id` = cat_num;
			DELETE FROM `categories` WHERE `id` = cat_num;
			
			SELECT COUNT( * ) FROM `rules` WHERE `order` > catorder INTO cat_count;
			WHILE catorder < cat_count DO
				UPDATE `categories` SET `order` = catorder WHERE `order` = catorder + 1;
				SET catorder = catorder + 1;
			END WHILE;
		END $$
DELIMITER ;

DELIMITER $$
	CREATE PROCEDURE DeleteRule( IN catorder INT( 32 ), ruleorder INT( 32 ) )
		BEGIN
			DECLARE rule_num, rule_count, cat_num INT DEFAULT 0;
			
			SELECT `id` FROM `categories` WHERE `order` = catorder INTO cat_num;
			SELECT `id` FROM `rules` WHERE `order` = ruleorder AND `cat_id` = cat_num INTO rule_num;
			DELETE FROM `rules_text` WHERE `rule_id` = rule_num;
			DELETE FROM `rules` WHERE `id` = rule_num;
			
			SELECT COUNT( * ) FROM `rules` WHERE `order` > ruleorder AND `cat_id` = cat_num INTO rule_count;
			WHILE ruleorder < rule_count DO
				UPDATE `rules` SET `order` = ruleorder WHERE `order` = ruleorder + 1 AND `cat_id` = cat_num;
				SET ruleorder = ruleorder + 1;
			END WHILE;
		END $$
DELIMITER ;

DELIMITER $$
	CREATE PROCEDURE EditCategory( IN catorder INT( 32 ), IN curlang VARCHAR( 32 ), IN newcat VARCHAR( 32 ) )
		BEGIN
			DECLARE cat_num INT DEFAULT 0;
			
			SELECT `id` FROM `categories` WHERE `order` = catorder INTO cat_num;
			
			UPDATE `categories_text` SET `name` = newcat WHERE `lang` = curlang AND `cat_id` = cat_num;
		END $$
DELIMITER ;

DELIMITER $$
	CREATE PROCEDURE EditRule( IN catorder INT( 32 ), IN ruleorder INT( 32 ), IN curlang VARCHAR( 32 ), IN newrule VARCHAR( 32 ) )
		BEGIN
			DECLARE cat_num, rule_num INT DEFAULT 0;
			
			SELECT `id` FROM `categories` WHERE `order` = catorder INTO cat_num;
			SELECT `id` FROM `rules` WHERE `order` = ruleorder AND `cat_id` = cat_num INTO rule_num;
			
			UPDATE `rules_text` SET `rule` = newrule WHERE `lang` = curlang AND `rule_id` = rule_num;
			UPDATE `rules` SET `last_edit` = NOW() WHERE `id` = rule_num;
		END $$
DELIMITER ;

DELIMITER $$
	CREATE PROCEDURE MoveRule( IN catorder INT( 32 ), IN ruleorder INT( 32 ), IN newpos INT( 32 ) )
		BEGIN
			DECLARE cat_num, rule_num, rule_count INT DEFAULT 0;
			
			SELECT `id` FROM `categories` WHERE `order` = catorder INTO cat_num;
			SELECT `id` FROM `rules` WHERE `order` = ruleorder AND `cat_id` = cat_num INTO rule_num;
			UPDATE `rules` SET `order` = newpos WHERE `order` = ruleorder AND `cat_id` = cat_num;
			
			SET rule_count = newpos;
			WHILE rule_count != ruleorder DO
				UPDATE `rules` SET `order` = rule_count - 1 WHERE `order` = rule_count AND `cat_id` = cat_num AND `id` != rule_num;
				SET rule_count = rule_count - 1;
			END WHILE;
		END $$
DELIMITER ;