-- Creating a monthly attendance sheet failed under strict SQL mode with
-- "Field 'wdays' doesn't have a default value": sp_gensheet never filled sheet_det.wdays.
-- The procedure now sets it (working days = days in month; offs and holidays are 0 on a new
-- sheet), and the column gets a default so other inserts can't hit the same error.

ALTER TABLE sheet_det MODIFY wdays smallint NOT NULL DEFAULT 0;

DROP PROCEDURE IF EXISTS sp_gensheet;

DELIMITER $$
CREATE PROCEDURE sp_gensheet(v_mnth SMALLINT, v_yr SMALLINT)
BEGIN
	declare sht_id int(11) UNSIGNED DEFAULT NULL;
	declare mdays smallint;
	set mdays=DAY(LAST_DAY(concat(v_yr,'-',v_mnth,'-1')));
	insert into sheet(mnth,yr) values(v_mnth,v_yr);
	select LAST_INSERT_ID() into sht_id;
	if (sht_id is not null) THEN
		insert into sheet_det(sheet_id,empno,attnd,lwop,wdays)
			select sht_id,EMPNO,mdays,0,mdays from empmast
				where state='Permanent' OR state='Probation';
	end IF;
END$$
DELIMITER ;
