-- MySQL dump 10.13  Distrib 8.4.9, for Win64 (x86_64)
--
-- Host: localhost    Database: jps_db1
-- ------------------------------------------------------
-- Server version	8.4.9
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `trg_before_insert` BEFORE INSERT ON `attdata` FOR EACH ROW begin
declare v_dtm datetime;
declare v_inout varchar(7);
declare v_shift varchar(2);
declare v_pdt date;
declare v_diff,v_late integer;
declare v_nshift varchar(2);
declare v_autoshift integer default 0;
declare v_sc varchar(2);
declare v_sintime time;
declare v_sotime time;
declare v_mxsec integer default 60;
if (NEW.minout is null and NEW.dtime is not null) then
	select autoshift,ifnull(ifnull(es.ShiftCode,cs.ShiftCode),e.shcode) sc,sm.intime,sm.OutTime into v_autoshift,v_sc,v_sintime,v_sotime from empmast e
             left join shiftchange es on e.empno=es.empid and es.scdate=date(NEW.dtime) 
             left join catshiftchange cs on e.CATCODE=cs.catcode and date(NEW.dtime) between cs.scdate and cs.scedate 
             left join shiftmaster sm on sm.shiftcode=ifnull(ifnull(es.ShiftCode,cs.ShiftCode),e.shcode) 
             where cardno=NEW.uid limit 1;
	if(v_autoshift=1) then
		select dtime,minout,shift,dt into v_dtm,v_inout,v_shift,v_pdt from attdata 
                  where uid=NEW.uid and minout<>'discard' and dtime<NEW.dtime order by dtime desc limit 1;
        set NEW.minout='in';
        set NEW.dt=date(NEW.dtime);
		select shiftcode into v_nshift from shiftmaster where intime is not null 
			order by abs(TIME_TO_SEC(TIMEDIFF(intime,time(NEW.dtime)))) limit 1;
		set NEW.shift=v_nshift;
		if (v_dtm is not null) then
			set v_diff=TIMESTAMPDIFF(MINUTE, v_dtm,NEW.dtime);
			set NEW.minout='in';
			set NEW.dt=date(NEW.dtime);
			if (v_diff<5) then
				set NEW.minout='discard';
			else
				if ((v_inout='in') and (v_diff<840)) then
					set NEW.minout='out';
					set NEW.dt=v_pdt;
					select shiftcode into v_nshift from shiftmaster where intime is not null 
						order by abs(TIME_TO_SEC(TIMEDIFF(intime,time(v_dtm)))),
						abs(TIME_TO_SEC(TIMEDIFF(outtime,time(NEW.dtime)))) limit 1;
					set NEW.shift=v_nshift;
				end if;
			end if;
		end if;
	else
		select dtime,minout,shift,dt into v_dtm,v_inout,v_shift,v_pdt from attdata 
                                            where uid=NEW.uid and minout<>'discard' and dtime<NEW.dtime order by dtime desc limit 1;
                                     set NEW.minout='in';
                                     set NEW.dt=date(NEW.dtime);
		if (v_sc is null) then
			select shiftcode,intime,outtime into v_nshift,v_sintime,v_sotime from shiftmaster 
				where intime is not null order by abs(TIME_TO_SEC(TIMEDIFF(intime,time(NEW.dtime)))) limit 1;
		else
			set v_nshift=v_sc;
                                     end if;
			set NEW.shift=v_nshift;
			if (v_dtm is not null) then
				set v_diff=TIMESTAMPDIFF(MINUTE, v_dtm,NEW.dtime);
				set NEW.minout='in';
				set NEW.dt=date(NEW.dtime);
				if (v_diff<5) then
					set NEW.minout='discard';
				else
					if ((v_inout='in') and (v_diff<840)) then
						set NEW.minout='out';
						set NEW.dt=v_pdt;
						set NEW.shift=v_nshift;
					end if;
				end if;
			end if;
			if (NEW.minout='in') then
				set v_late=TIME_TO_SEC(TIMEDIFF(time(NEW.dtime),v_sintime));
				if (v_late>60) then
					set NEW.cmnt='LATE9';
					if (v_late>599) then
						set NEW.cmnt='LATE10';
					end if;
				end if;
			else if (NEW.minout='out') then
					set v_late=TIME_TO_SEC(TIMEDIFF(v_sotime,time(NEW.dtime)));
					set v_mxsec=60;
					if (dayofweek(NEW.dtime)=7) then
						set v_mxsec=8400;
					end if;
					if (v_late>v_mxsec) then
						set NEW.cmnt='EARLYO';
					end if;
				end if;
			end if;
	end if;
end if;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `trg_before_insert_copy1` BEFORE INSERT ON `attdata_org` FOR EACH ROW begin
declare v_dtm datetime;
declare v_inout varchar(7);
declare v_shift varchar(2);
declare v_pdt date;
declare v_diff,v_late integer;
declare v_nshift varchar(2);
declare v_autoshift integer default 0;
declare v_sc varchar(2);
declare v_sintime time;
declare v_sotime time;
declare v_mxsec integer default 60;
if (NEW.minout is null and NEW.dtime is not null) then
	select autoshift,ifnull(ifnull(es.ShiftCode,cs.ShiftCode),e.shcode) sc,sm.intime,sm.OutTime into v_autoshift,v_sc,v_sintime,v_sotime from empmast e
             left join shiftchange es on e.empno=es.empid and es.scdate=date(NEW.dtime) 
             left join catshiftchange cs on e.CATCODE=cs.catcode and date(NEW.dtime) between cs.scdate and cs.scedate 
             left join shiftmaster sm on sm.shiftcode=ifnull(ifnull(es.ShiftCode,cs.ShiftCode),e.shcode) 
             where cardno=NEW.uid limit 1;
	if(v_autoshift=1) then
		select dtime,minout,shift,dt into v_dtm,v_inout,v_shift,v_pdt from attdata 
                  where uid=NEW.uid and minout<>'discard' and dtime<NEW.dtime order by dtime desc limit 1;
        set NEW.minout='in';
        set NEW.dt=date(NEW.dtime);
		select shiftcode into v_nshift from shiftmaster where intime is not null 
			order by abs(TIME_TO_SEC(TIMEDIFF(intime,time(NEW.dtime)))) limit 1;
		set NEW.shift=v_nshift;
		if (v_dtm is not null) then
			set v_diff=TIMESTAMPDIFF(MINUTE, v_dtm,NEW.dtime);
			set NEW.minout='in';
			set NEW.dt=date(NEW.dtime);
			if (v_diff<5) then
				set NEW.minout='discard';
			else
				if ((v_inout='in') and (v_diff<840)) then
					set NEW.minout='out';
					set NEW.dt=v_pdt;
					select shiftcode into v_nshift from shiftmaster where intime is not null 
						order by abs(TIME_TO_SEC(TIMEDIFF(intime,time(v_dtm)))),
						abs(TIME_TO_SEC(TIMEDIFF(outtime,time(NEW.dtime)))) limit 1;
					set NEW.shift=v_nshift;
				end if;
			end if;
		end if;
	else
		select dtime,minout,shift,dt into v_dtm,v_inout,v_shift,v_pdt from attdata 
                                            where uid=NEW.uid and minout<>'discard' and dtime<NEW.dtime order by dtime desc limit 1;
                                     set NEW.minout='in';
                                     set NEW.dt=date(NEW.dtime);
		if (v_sc is null) then
			select shiftcode,intime,outtime into v_nshift,v_sintime,v_sotime from shiftmaster 
				where intime is not null order by abs(TIME_TO_SEC(TIMEDIFF(intime,time(NEW.dtime)))) limit 1;
		else
			set v_nshift=v_sc;
                                     end if;
			set NEW.shift=v_nshift;
			if (v_dtm is not null) then
				set v_diff=TIMESTAMPDIFF(MINUTE, v_dtm,NEW.dtime);
				set NEW.minout='in';
				set NEW.dt=date(NEW.dtime);
				if (v_diff<5) then
					set NEW.minout='discard';
				else
					if ((v_inout='in') and (v_diff<840)) then
						set NEW.minout='out';
						set NEW.dt=v_pdt;
						set NEW.shift=v_nshift;
					end if;
				end if;
			end if;
			if (NEW.minout='in') then
				set v_late=TIME_TO_SEC(TIMEDIFF(time(NEW.dtime),v_sintime));
				if (v_late>60) then
					set NEW.cmnt='LATE9';
					if (v_late>599) then
						set NEW.cmnt='LATE10';
					end if;
				end if;
			else if (NEW.minout='out') then
					set v_late=TIME_TO_SEC(TIMEDIFF(v_sotime,time(NEW.dtime)));
					set v_mxsec=60;
					if (dayofweek(NEW.dtime)=7) then
						set v_mxsec=8400;
					end if;
					if (v_late>v_mxsec) then
						set NEW.cmnt='EARLYO';
					end if;
				end if;
			end if;
	end if;
end if;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `late_insert` AFTER INSERT ON `latemuster` FOR EACH ROW BEGIN
    DECLARE emp_id INT;
    DECLARE cl INT;
    DECLARE wp INT;
    DECLARE cl_balance INT; 
    DECLARE last_late_date DATE;
    DECLARE insert_date DATE;

    -- Get employee ID and late data from latemuster
    SET emp_id = NEW.EmpCode;
    SET cl = NEW.CL;
    SET wp = NEW.WP;

    -- Fetch last late date for employee
    SET last_late_date = (SELECT MAX(dt) 
                          FROM attnddet 
                          WHERE empno = emp_id AND late LIKE '%late%' 
                          AND MONTH(dt) = NEW.month1 
                          AND YEAR(dt) = NEW.year1);

    -- Fetch leave balance
    SELECT accleave INTO cl_balance FROM empmast WHERE empno = emp_id;

 --  CALL empLeave(emp_id, cl);
    -- Condition 1: If CL is not 0 and WP is 0
    IF cl != 0 AND wp = 0 THEN
        INSERT INTO leavedet (empno, dte, descr, nol, lwop, noofdays, ltype)
        VALUES (emp_id, last_late_date, 'Late Deduction', cl, 0, cl, 'CL');
        UPDATE empmast 
        SET accleave = accleave - cl 
        WHERE empno = emp_id;
    -- Condition 2: If CL is 0 and WP is not 0
    ELSEIF cl = 0 AND wp != 0 THEN
        INSERT INTO leavedet (empno, dte, descr, nol, lwop, noofdays, ltype)
        VALUES (emp_id, last_late_date, 'Late Deduction', wp, wp, wp, 'WP');

    -- Condition 3: If both CL and WP are not 0
    ELSEIF cl != 0 AND wp != 0 THEN
        INSERT INTO leavedet (empno, dte, descr, nol, lwop, noofdays, ltype)
        VALUES (emp_id, last_late_date, 'Late Deduction', cl + wp, wp, cl + wp, 'WP');
        UPDATE empmast 
        SET accleave = accleave - cl 
        WHERE empno = emp_id;
    END IF;

END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `leave_det_ins_trg` BEFORE INSERT ON `leaveapply` FOR EACH ROW begin
declare v_cl,v_ml,v_el double default 0;
declare v_sex char(1) default 'M';
declare v_sanc,v_wp double default 0;
declare v_elinyear int default 0;
declare v_msg varchar(30) default '';
set v_sanc=NEW.nol-NEW.lwop;
select ACCLEAVE,MEDICAL_LEAVE,el,sex into v_cl,v_ml,v_el,v_sex from empmast where empno=NEW.empno;
if (new.ltype='ML') then
    if (v_sex='M') then
                signal sqlstate '45000' set message_text = 'Invalid gender for the leave';
   else
        if (v_sanc>90) then
               signal sqlstate '45000' set message_text = 'Only 90 days allowed for ML';
        end if;
   end if;
elseif (new.ltype='CL') then
         if (v_sanc>v_cl) then
              set v_msg=concat('No sufficient balance for CL. ',v_cl);
              signal sqlstate '45000' set message_text = v_msg;
        elseif (v_sanc>3) then
               signal sqlstate '45000' set message_text ='Only 1-3 is allowed for CL';
         else
              update empmast set accleave=(v_cl-v_sanc) where empno=NEW.empno;
        end if;
elseif (new.ltype='MED') then
         if (v_sanc>v_ml) then
              set v_msg=concat('No sufficient balance for MED. ',v_ml);
              signal sqlstate '45000' set message_text = v_msg;
        else
              update empmast set MEDICAL_LEAVE=(v_ml-v_sanc) where empno=NEW.empno;
              set NEW.LWOP=v_sanc/2;
        end if;
elseif (new.ltype='EL') then
         select count(*) into v_elinyear from leavedet where empno=new.empno and ltype='EL';
         if (v_elinyear<2) then
         if (v_sanc>v_el) then
             set v_msg=concat('No sufficient balance for EL. ',v_el); 
             signal sqlstate '45000' set message_text = v_msg;
        elseif (v_sanc<3) or (v_sanc>30) then
             signal sqlstate '45000' set message_text ='Only 3-30 is allowed';
        else
              update empmast set el=(v_el-v_sanc) where empno=NEW.empno;
        end if;
        else  signal sqlstate '45000' set message_text ='EL already taken in this year';
        end if;
end if;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `leave_aft_upd_trg` AFTER UPDATE ON `leaveapply` FOR EACH ROW BEGIN
    IF NEW.STATUS = 'Approve' THEN
        INSERT INTO leavedet (
            EMPNO, 
            DTE, 
            DESCR, 
            FDATE, 
            TDATE,
            NOL,
            LTYPE, 
            NOOFDAYS
        ) VALUES (
            NEW.EMPNO, 
            NEW.DTE, 
            NEW.DESCR, 
            NEW.FDATE, 
            NEW.TDATE, 
            NEW.NOL,
            NEW.LTYPE, 
            NEW.NOOFDAYS
        );
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `leave_det_upd_trg` BEFORE UPDATE ON `leavedet` FOR EACH ROW begin
               signal sqlstate '45000' set message_text = 'Updation not allowed for sanctioned leave. Delete and recreate';
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `leave_det_del_trg` BEFORE DELETE ON `leavedet` FOR EACH ROW begin
declare v_cl,v_ml,v_el double default 0;
declare v_sex char(1) default 'M';
declare v_sanc,v_wp double default 0;
declare v_msg varchar(30) default '';
set v_sanc=OLD.nol;
select ACCLEAVE,MEDICAL_LEAVE,el,sex into v_cl,v_ml,v_el,v_sex from empmast where empno=OLD.empno;
if (OLD.ltype='CL') then
              update empmast set accleave=(v_cl+OLD.nol-OLD.lwop) where empno=OLD.empno;
elseif (OLD.ltype='MED') then
              update empmast set MEDICAL_LEAVE=(v_ml+OLD.nol-OLD.lwop) where empno=OLD.empno;
elseif (OLD.ltype='EL') then
              update empmast set el=(v_el+OLD.nol-OLD.lwop) where empno=OLD.empno;
end if;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `leave_det_ins_trg_copy1` BEFORE INSERT ON `leavedet_copy1` FOR EACH ROW begin
declare v_cl,v_ml,v_el double default 0;
declare v_sex char(1) default 'M';
declare v_sanc,v_wp double default 0;
declare v_elinyear int default 0;
declare v_msg varchar(30) default '';
set v_sanc=NEW.nol-NEW.lwop;
select ACCLEAVE,MEDICAL_LEAVE,el,sex into v_cl,v_ml,v_el,v_sex from empmast where empno=NEW.empno;
if (new.ltype='ML') then
    if (v_sex='M') then
                signal sqlstate '45000' set message_text = 'Invalid gender for the leave';
   else
        if (v_sanc>90) then
               signal sqlstate '45000' set message_text = 'Only 90 days allowed for ML';
        end if;
   end if;
elseif (new.ltype='CL') then
         if (v_sanc>v_cl) then
              set v_msg=concat('No sufficient balance for CL. ',v_cl);
              signal sqlstate '45000' set message_text = v_msg;
        elseif (v_sanc>3) then
               signal sqlstate '45000' set message_text ='Only 1-3 is allowed for CL';
         else
              update empmast set accleave=(v_cl-v_sanc) where empno=NEW.empno;
        end if;
elseif (new.ltype='MED') then
         if (v_sanc>v_ml) then
              set v_msg=concat('No sufficient balance for MED. ',v_ml);
              signal sqlstate '45000' set message_text = v_msg;
        else
              update empmast set MEDICAL_LEAVE=(v_ml-v_sanc) where empno=NEW.empno;
              set NEW.LWOP=v_sanc/2;
        end if;
elseif (new.ltype='EL') then
         select count(*) into v_elinyear from leavedet where empno=new.empno and ltype='EL';
         if (v_elinyear<2) then
         if (v_sanc>v_el) then
             set v_msg=concat('No sufficient balance for EL. ',v_el); 
             signal sqlstate '45000' set message_text = v_msg;
        elseif (v_sanc<3) or (v_sanc>30) then
             signal sqlstate '45000' set message_text ='Only 3-30 is allowed';
        else
              update empmast set el=(v_el-v_sanc) where empno=NEW.empno;
        end if;
        else  signal sqlstate '45000' set message_text ='EL already taken in this year';
        end if;
end if;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `leave_det_upd_trg_copy1` BEFORE UPDATE ON `leavedet_copy1` FOR EACH ROW begin
               signal sqlstate '45000' set message_text = 'Updation not allowed for sanctioned leave. Delete and recreate';
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `leave_det_del_trg_copy1` BEFORE DELETE ON `leavedet_copy1` FOR EACH ROW begin
declare v_cl,v_ml,v_el double default 0;
declare v_sex char(1) default 'M';
declare v_sanc,v_wp double default 0;
declare v_msg varchar(30) default '';
set v_sanc=OLD.nol;
select ACCLEAVE,MEDICAL_LEAVE,el,sex into v_cl,v_ml,v_el,v_sex from empmast where empno=OLD.empno;
if (OLD.ltype='CL') then
              update empmast set accleave=(v_cl+OLD.nol-OLD.lwop) where empno=OLD.empno;
elseif (OLD.ltype='MED') then
              update empmast set MEDICAL_LEAVE=(v_ml+OLD.nol-OLD.lwop) where empno=OLD.empno;
elseif (OLD.ltype='EL') then
              update empmast set el=(v_el+OLD.nol-OLD.lwop) where empno=OLD.empno;
end if;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `late_leave_trg` AFTER INSERT ON `sheet_det` FOR EACH ROW BEGIN
    DECLARE late_count INT DEFAULT 0;
    DECLARE cl_balance INT DEFAULT 0;
    DECLARE leave_deductions INT DEFAULT 0;
    DECLARE remaining_deductions INT DEFAULT 0;
    DECLARE total_deductions INT DEFAULT 0;
    DECLARE emp_id INT;
    DECLARE sheetId INT;
    DECLARE last_late_date DATE;

    -- Initialize variables
    SET emp_id = NEW.empno;
    SET sheetId = NEW.sheet_id;

    -- Count total number of "late" entries for employee
    SELECT COUNT(*), MAX(dt) 
    INTO late_count, last_late_date
    FROM attnddet
    WHERE empno = emp_id
      AND late LIKE '%late%'
      AND MONTH(dt) = get_mnth(sheetId)
      AND YEAR(dt) = get_yr(sheetId);

    -- Get current CL balance for employee
    SELECT accleave INTO cl_balance FROM empmast
    WHERE empno = emp_id;
 
    -- Calculate total number of leave deductions required
    SET leave_deductions = FLOOR(late_count / 3);
    SET total_deductions = leave_deductions;

    -- Handle deductions and update relevant tables
    IF total_deductions > 0 THEN
        -- Check if CL balance is sufficient for total deductions
        IF cl_balance >= total_deductions THEN
            SET remaining_deductions = 0;

            -- Insert record into latemuster when total_deductions > 0
            INSERT INTO latemuster (EmpCode, year1, month1, TotalLate, CL, WP)
            VALUES (emp_id, get_yr(sheetId), get_mnth(sheetId), late_count, total_deductions, remaining_deductions);
       -- UPDATE empmast SET accleave = cl_balance -  -- total_deductions WHERE empno = emp_id;
        ELSE
            SET remaining_deductions = total_deductions - cl_balance;

            -- Insert record into latemuster when total_deductions > 0
            INSERT INTO latemuster (EmpCode, year1, month1, TotalLate, CL, WP)
            VALUES (emp_id, get_yr(sheetId), get_mnth(sheetId), late_count, cl_balance, remaining_deductions);
    --  UPDATE empmast SET accleave = 0 WHERE empno = emp_id;
        END IF;
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Dumping routines for database 'jps_db1'
--
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `dsg_empcount`(`p_dsgcode` INT) RETURNS int
BEGIN
	declare tmp_count int default 0;
	select count(*) into tmp_count from empmast where dsgcode=p_dsgcode;
	RETURN tmp_count;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `get_advInst`(`empno_param` INT(3)) RETURNS int
    DETERMINISTIC
BEGIN
    DECLARE advAmt DECIMAL(10, 2);
    SELECT IFNULL((l.Amt / l.noinst), 0) INTO advAmt
FROM loanmast l
left JOIN loandet ld ON ld.lno = l.lno
WHERE l.flag != 1 
  AND l.EMPNO = empno_param
GROUP BY l.lno, l.noinst
HAVING COUNT(ld.lno) <= l.noinst;
    RETURN advAmt;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `get_arrearAmt`(`empno_param` INT(4), `sheet_id` INT(2)) RETURNS decimal(10,2)
    DETERMINISTIC
BEGIN
    DECLARE arrearAmt DECIMAL(10, 2); 

    SELECT COALESCE(
        SUM(a.amount),
        0
    ) INTO arrearAmt
    FROM arrear a
    JOIN empmast e ON (
        (a.categ = e.DSGCODE AND e.EMPNO = empno_param)
        OR (empno_param = a.indv AND a.indv = e.EMPNO)
        OR (empno_param = e.EMPNO AND a.categ = '0' AND a.indv = '0')
    )
    GROUP BY empno_param;

    RETURN arrearAmt;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`%` FUNCTION `get_gross`(`basic` DECIMAL, `grdpay` DECIMAL, `da` DECIMAL, `hra` DECIMAL, `spl` DECIMAL) RETURNS decimal(10,2)
BEGIN
	
	declare gross decimal(10,2) default 0;
	declare daval,hraval,bastmp decimal(10,2) default 0;
  set bastmp=basic+ifnull(grdpay,0);
  set daval=bastmp*da/100;
  set hraval=bastmp*hra/100;
  set gross=bastmp+daval+hraval+ifnull(spl,0);
	RETURN gross;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `get_grossAmt`(`trnsno` INT(3), `daysinmonth` INT(2)) RETURNS decimal(10,2)
BEGIN
    DECLARE grossAmt DECIMAL(10, 2);

    SELECT (tm.basic + IFNULL(SUM(t1.AMOUNT), 0)) INTO grossAmt
    FROM trnsmst tm
    LEFT JOIN trnsdet1 t1 ON tm.TRNSNO = t1.TRNSNO
    WHERE tm.TRNSNO = trnsno and t1.DESCR = 'DA';
    RETURN grossAmt;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `get_lsaAmt`(`empno` INT(5)) RETURNS decimal(10,2)
BEGIN
    DECLARE LSA DECIMAL(10,2) DEFAULT 0;
    
    -- Calculate LSA based on years of service
    SELECT 
        CASE 
           WHEN TIMESTAMPDIFF(YEAR, e.DOC, DATE(CONCAT(YEAR(CURDATE()), '-04-01'))) > 6 THEN 
            50 * (TIMESTAMPDIFF(YEAR, e.DOC, DATE(CONCAT(YEAR(CURDATE()), '-04-01'))) - 6) 
            ELSE 0
        END 
    INTO LSA
    FROM empmast e
    WHERE e.EMPNO = empno;
    
    -- Return the calculated LSA
    RETURN LSA;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `get_lwopAmt`(`trnsno` INT(3), `daysinmonth` INT(2)) RETURNS decimal(10,2)
    DETERMINISTIC
BEGIN
    DECLARE lwopAmt DECIMAL(10, 2);

    SELECT (tm.lwop / daysInMonth) * ((CASE WHEN t1.DESCR = 'DA' THEN t1.amount ELSE 0 END) + tm.basic) INTO lwopAmt
    FROM trnsmst tm
    LEFT JOIN trnsdet1 t1 ON tm.TRNSNO = t1.TRNSNO
    WHERE tm.TRNSNO = trnsno GROUP BY tm.TRNSNO;
    RETURN lwopAmt; 
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `get_mnth`(`sheetId` INT(2)) RETURNS int
    DETERMINISTIC
BEGIN
    DECLARE Month INT;

    SELECT mnth INTO Month
    FROM sheet
    WHERE sheet_id = sheetId;

    RETURN Month;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `get_sheetid`(`selectedMonth` INT(2), `selectedYear` INT(4)) RETURNS int
BEGIN
    DECLARE sheetid INT;
    
    SELECT sheet_id INTO sheetid
    FROM sheet
    WHERE mnth = selectedMonth AND yr = selectedYear;

    IF sheetid IS NULL THEN
        INSERT INTO sheet (mnth, yr) VALUES (selectedMonth, selectedYear);
        SET sheetid = LAST_INSERT_ID();
    END IF;

    RETURN sheetid;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `get_taAmt`(`empno_param` INT(5), `sheet_id_param` INT) RETURNS decimal(10,2)
BEGIN
    DECLARE taAmt DECIMAL(10, 2);

    SELECT IFNULL(SUM(ta.amount), 0) INTO taAmt
    FROM emptamast ta
    WHERE ta.EMPNO = empno_param AND ta.sheetid = sheet_id_param;

    RETURN taAmt;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `get_yr`(`sheetId` INT(5)) RETURNS int
BEGIN
    DECLARE Year INT;

    SELECT yr INTO Year
    FROM sheet
    WHERE sheet_id = sheetId;

    RETURN Year;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`192.168.%` FUNCTION `mnthndays`(`mnthyr` VARCHAR(8)) RETURNS int
BEGIN
	declare yr int default 0;
	declare mnth int default 0;
	declare mstr varchar(3);
	set yr=SUBSTR(mnthyr,5,4);
  set mstr=substr(mnthyr,1,3);
	CASE mstr
    WHEN 'JAN' THEN set mnth=1;
    WHEN 'FEB' THEN set mnth=2;
    WHEN 'MAR' THEN set mnth=3;
    WHEN 'APR' THEN set mnth=4;
    WHEN 'MAY' THEN set mnth=5;
    WHEN 'JUN' THEN set mnth=6;
    WHEN 'JUL' THEN set mnth=7;
    WHEN 'AUG' THEN set mnth=8;
    WHEN 'SEP' THEN set mnth=9;
    WHEN 'OCT' THEN set mnth=10;
    WHEN 'NOV' THEN set mnth=11;
    ELSE set mnth=12;
	END CASE;
	return day(LAST_DAY(concat(yr,'-',mnth,'-01')));
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`192.168.%` FUNCTION `myfunc`(`p_tmax` TIME, `p_tmin` TIME) RETURNS varchar(10) CHARSET latin1
BEGIN	










	RETURN 'cccc';
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`192.168.%` FUNCTION `ndays`(`mnth` INT, `yr` INT) RETURNS int
BEGIN
	return day(LAST_DAY(concat(yr,'-',mnth,'-01')));
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `dup_handler`()
BEGIN
	declare dup_count int default 0;
	declare dup_pk condition for SQLSTATE '23000';
	declare CONTINUE HANDLER for dup_pk SET dup_count=dup_count+1;
	insert into dummy values(1,'AA');
	insert into dummy values(2,'AA');
	insert into dummy values(1,'AA');
	insert into dummy values(3,'AA');
	insert into dummy values(2,'AA');
	insert into dummy values(4,'AA');
select concat('No. of duplicates :',dup_count);
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `empLeave`(IN `employee_id` INT(5), IN `leave_to_deduct` INT(2))
BEGIN
    DECLARE current_balance INT;

    -- Fetch the current leave balance
    SELECT accleave INTO current_balance
    FROM empmast
    WHERE empno = employee_id;

    -- Update the leave balance
    UPDATE empmast
    SET accleave = current_balance - leave_to_deduct
    WHERE empno = employee_id;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `empLeaveBal`()
BEGIN
    -- Update accleave for LeaveCode = 3
    UPDATE empmast e
    JOIN (
        SELECT 
            lg.LeaveGroupCode, 
            lg.LeaveCode, 
            LEAST(
                lg.AnnQuota + CASE 
                                WHEN lg.LeaveCode = 5 THEN e.EL 
                                ELSE 0 
                             END,
                CASE 
                    WHEN lg.LeaveGroupCode = 1 THEN 300
                    WHEN lg.LeaveGroupCode = 3 THEN 200
                    WHEN lg.LeaveGroupCode = 2 THEN 60
                    WHEN lg.LeaveGroupCode = 4 THEN 60
                    ELSE lg.AnnQuota + e.EL
                END
            ) AS TotalQuota, 
            e.empno
        FROM leavegroup lg
        JOIN empmast e ON e.LeaveGroup = lg.LeaveGroupCode
        WHERE lg.CreditYearly = 1
    ) t 
    ON e.empno = t.empno
    SET e.accleave = t.TotalQuota,  e.ocl = t.TotalQuota
    WHERE t.LeaveCode = 3;

    -- Update medical_leave for LeaveCode = 4
    UPDATE empmast e
    JOIN (
        SELECT 
            lg.LeaveGroupCode, 
            lg.LeaveCode, 
            LEAST(
                lg.AnnQuota + CASE 
                                WHEN lg.LeaveCode = 5 THEN e.EL 
                                ELSE 0 
                             END,
                CASE 
                    WHEN lg.LeaveGroupCode = 1 THEN 300
                    WHEN lg.LeaveGroupCode = 3 THEN 200
                    WHEN lg.LeaveGroupCode = 2 THEN 60
                    WHEN lg.LeaveGroupCode = 4 THEN 60
                    ELSE lg.AnnQuota + e.EL
                END
            ) AS TotalQuota, 
            e.empno
        FROM leavegroup lg
        JOIN empmast e ON e.LeaveGroup = lg.LeaveGroupCode
        WHERE lg.CreditYearly = 1
    ) t 
    ON e.empno = t.empno
    SET e.medical_leave = t.TotalQuota, e.oml = t.TotalQuota
    WHERE t.LeaveCode = 4;

    -- Update EL for LeaveCode = 5
    UPDATE empmast e
    JOIN (
        SELECT 
            lg.LeaveGroupCode, 
            lg.LeaveCode, 
            LEAST(
                lg.AnnQuota + CASE 
                                WHEN lg.LeaveCode = 5 THEN e.EL 
                                ELSE 0 
                             END,
                CASE 
                    WHEN lg.LeaveGroupCode = 1 THEN 300
                    WHEN lg.LeaveGroupCode = 3 THEN 200
                    WHEN lg.LeaveGroupCode = 2 THEN 60
                    WHEN lg.LeaveGroupCode = 4 THEN 60
                    ELSE lg.AnnQuota + e.EL
                END
            ) AS TotalQuota, 
            e.empno
        FROM leavegroup lg
        JOIN empmast e ON e.LeaveGroup = lg.LeaveGroupCode
        WHERE lg.CreditYearly = 1
    ) t 
    ON e.empno = t.empno
    SET e.EL = t.TotalQuota, e.oel = t.TotalQuota
    WHERE t.LeaveCode = 5;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `emptree`(IN `p_empno` INT)
BEGIN
DECLARE v_plist text default '';
DECLARE v_empno,v_curempno,v_prnt int;	
DECLARE nullparent bool default false;
set v_curempno=p_empno;
	REPEAT
		select EMPNO,REPORTS_TO into v_empno,v_prnt from empmast where empno=v_curempno;
	set nullparent=v_prnt is null;
	set v_curempno=v_prnt;
	set v_plist=concat(v_plist,'->',IFNULL(v_curempno,''));
	UNTIL nullparent
	END REPEAT;
select concat(p_empno,v_plist);
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `lateDeducation`(IN `EmpCode` INT(5), IN `CL` INT(2), IN `WP` INT(2), IN `Month1` INT(2), IN `Year1` INT(4))
BEGIN
    DECLARE emp_id INT;
    DECLARE cl_balance INT; -- Current leave balance
    DECLARE insert_date DATE;
    DECLARE last_late_date DATE;

    -- Assign input parameters to local variables
    SET emp_id = EmpCode;

    -- Fetch the current leave balance for the employee from empmast
    SELECT accleave INTO cl_balance FROM empmast WHERE empno = emp_id;

    -- Fetch the last late date for the employee from attndet
    SELECT MAX(dt)
    INTO last_late_date
    FROM attnddet
    WHERE empno = emp_id
      AND late LIKE '%late%'
      AND MONTH(dt) = Month1
      AND YEAR(dt) = Year1;

    -- Calculate the insert date (1st day of the month in question)
    SET insert_date = STR_TO_DATE(CONCAT('1-', Month1, '-', Year1), '%d-%m-%Y');

    -- Insert the leave deduction into leavedet
    -- Condition 1: If CL is not 0 and WP is 0
    IF CL != 0 AND WP = 0 THEN
        INSERT INTO leavedet (empno, dte, descr, nol, lwop, noofdays, ltype)
        VALUES (emp_id, last_late_date, 'Late Deduction', CL, 0, CL, 'CL');

    -- Condition 2: If CL is 0 and WP is not 0
    ELSEIF CL = 0 AND WP != 0 THEN
        INSERT INTO leavedet (empno, dte, descr, nol, lwop, noofdays, ltype)
        VALUES (emp_id, last_late_date, 'Late Deduction', WP, WP, WP, 'WP');

    -- Condition 3: If both CL and WP are not 0
    ELSEIF CL != 0 AND WP != 0 THEN
        INSERT INTO leavedet (empno, dte, descr, nol, lwop, noofdays, ltype)
        VALUES (emp_id, last_late_date, 'Late Deduction', CL + WP, WP, CL + WP, 'WP');
    END IF;

    -- Update the leave balance in empmast (subtract the CL deducted)
    UPDATE empmast 
    SET accleave = cl_balance - CL
    WHERE empno = emp_id;

END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `monthSal`(IN `p_trnsno` INT(4), IN `p_sheet_id` INT(3), IN `p_daysInMonth` INT(2), IN `p_end_id` INT(3), IN `p_empno` INT(5))
BEGIN 
    DECLARE v_gross DECIMAL(10, 2);
    DECLARE v_lwop DECIMAL(10, 2);
    DECLARE v_esisal DECIMAL(10, 2);
    DECLARE v_pf DECIMAL(10, 2);
  --  DECLARE v_arrearAmt DECIMAL(10,2);
 DECLARE v_lsaAmt DECIMAL(10,2);

    -- Update arrear
   -- UPDATE trnsmst t
    -- SET arrear = get_arrearAmt(t.EMPNO, p_sheet_id)
   -- WHERE t.TRNSNO = p_trnsno AND t.sheet_id = p_end_id;

    -- Update ta
    -- UPDATE trnsmst t
    -- SET ta = get_taAmt(t.EMPNO, t.sheet_id)
    -- WHERE t.TRNSNO = p_trnsno;
-- Update arrear
-- Select  sd.arrear, sd.iTax 
 --   INTO  v_arrearAmt, v_iTaxAmt
-- from sheet_det sd 
-- JOIN trnsmst t on t.EMPNO=sd.empno and t.sheet_id=sd.sheet_id
-- where t.TRNSNO=  p_trnsno and sd.sheet_id = p_sheet_id LIMIT 1;
    -- Calculate gross using the get_grossAmt function
    SELECT get_grossAmt(p_trnsno, p_daysInMonth) INTO v_gross;
    SELECT get_lwopAmt(p_trnsno, p_daysInMonth) INTO v_lwop;
    SELECT get_lsaAmt(p_empno) INTO v_lsaAmt;
    -- Calculate esisal 
    SELECT  CASE 
            WHEN e.state = 'Probation' THEN COALESCE(g.allowance, 0) * v_gross / 100
            ELSE 0 END AS esisal_value
    INTO v_esisal
    FROM  trnsmst t
    JOIN empmast e ON e.EMPNO = t.EMPNO
    LEFT JOIN  gblall g ON g.descr = 'ESIC'
    WHERE t.TRNSNO = p_trnsno
        AND t.sheet_id = p_sheet_id LIMIT 1;
  -- Select PF amount from trnsdet2 table
    SELECT COALESCE(SUM(t2.AMOUNT), 0) INTO v_pf
    FROM trnsdet2 t2
    WHERE t2.Descr = 'PF' AND t2.TRNSNO = p_trnsno;
    -- Update trnsmst with calculated values
    UPDATE trnsmst t
    SET
        t.gross = COALESCE(v_gross + v_lsaAmt, 0),
        t.esisal = v_esisal, t.pfsal = v_pf, t.MNTHSAL = COALESCE(
        (v_gross + v_lsaAmt - COALESCE((SELECT SUM(t2.AMOUNT) FROM trnsdet2 t2 WHERE t.TRNSNO = t2.TRNSNO), 0) - COALESCE(v_lwop, 0)), 0)
    WHERE t.TRNSNO = p_trnsno AND t.sheet_id = p_sheet_id;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_advPayment`(IN `p_empNo` INT)
BEGIN
    INSERT INTO loandet (LNO, SLNO, DTE, INSTAMT, FLAG)
    SELECT 
        l.lno, COALESCE(MAX(ld.SLNO), 0) + 1 AS SLNO,CURDATE() AS DTE,                      
        COALESCE(get_advInst(l.EMPNO), 0) AS INSTAMT, 1 AS FLAG               
    FROM loanmast l
    LEFT JOIN loandet ld ON l.lno = ld.lno 
    WHERE l.EMPNO = p_empNo AND l.FLAG = 0                            
        AND NOT EXISTS (SELECT 1 FROM loandet WHERE loandet.LNO = l.lno 
        AND MONTH(loandet.DTE) = MONTH(CURDATE()) AND YEAR(loandet.DTE) = YEAR(CURDATE()))
    GROUP BY l.lno
    ON DUPLICATE KEY UPDATE INSTAMT = COALESCE(get_advInst(p_empNo), loandet.INSTAMT),
        DTE = COALESCE(VALUES(DTE), loandet.DTE);             
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`%` PROCEDURE `sp_annual_leave`()
BEGIN
declare da int default 45;
declare hra int default 10;
declare spl int default 0;
select ifnull(allowance,da) into da from gblall where descr='DA';
select ifnull(allowance,hra) into da from gblall where descr='HRA';
update empmast set ACCLEAVE=8,ocl=8 where state='Permanent';
update empmast e
inner join category c on e.CATCODE=c.catcode
set oel=el+elcredit,el=el+elcredit where state<>'Retired';
update empmast e 
left join indall i on e.EMPNO=i.empno and i.descr='SPL'
set oml=if(get_gross(basic,grdpay,da,hra,i.allowance)<=21000,0,12),
MEDICAL_LEAVE=if(get_gross(basic,grdpay,da,hra,i.allowance)<=21000,0,12) 
where state<>'Retired';
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`%` PROCEDURE `sp_applyleave`(IN `p_mnth` INT, IN `p_yr` INT)
BEGIN
	DECLARE finished INTEGER DEFAULT 0;
	DECLARE v_empno INTEGER default 0;
	DECLARE v_fdate date;
	DECLARE v_days,v_dcnt INTEGER default 0;
	DECLARE v_ltype varchar(5);
	DEClARE curLeave CURSOR FOR 
			select empno,fdate,noofdays,ltype from leavedet 
			where month(dte)=p_mnth and year(dte)=p_yr
			order by empno,dte;
	DECLARE CONTINUE HANDLER 
        FOR NOT FOUND SET finished = 1;

	OPEN curLeave;
	
	getLeave: LOOP

		FETCH curLeave INTO v_empno,v_fdate,v_days,v_ltype;
		IF finished = 1 THEN 
			LEAVE getLeave;
		END IF;
		
	  update attnddet set dflag=v_ltype,present=0
						where empno=v_empno and dt=v_fdate;
		if (v_days>0) THEN
			set v_dcnt:=1;
			while(v_dcnt<=(v_days-1)) 
			do
				update attnddet set dflag=v_ltype,present=0
						where empno=v_empno and dt=DATE_ADD(v_fdate,INTERVAL v_dcnt DAY);
			  
				
				set v_dcnt:=v_dcnt+1;
			end while;
		END IF;
	END LOOP getLeave;
	CLOSE curLeave;

END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_curempcount`()
BEGIN
	declare v_count int default 0;
	declare v_amt float;
	declare last_rec BOOL default false;
	declare cur_tmp CURSOR for select basic from empmast;
	declare CONTINUE handler for NOT FOUND set last_rec=TRUE;
	open cur_tmp;
	fetch cur_tmp into v_amt;
	while not last_rec DO
		if (v_amt>5000) THEN
			set v_count=v_count+1;
		end if;
	fetch cur_tmp into v_amt;
	end while;
close cur_tmp;
select v_count;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_gensheet`(`v_mnth` SMALLINT, `v_yr` SMALLINT)
BEGIN
	declare sht_id int(11) UNSIGNED DEFAULT NULL;
	declare mdays smallint;
	set mdays=DAY(LAST_DAY(concat(v_yr,'-',v_mnth,'-1')));
	insert into sheet(mnth,yr) values(v_mnth,v_yr);
	select LAST_INSERT_ID() into sht_id;
	if (sht_id is not null) THEN
		insert into sheet_det(sheet_id,empno,attnd,lwop)
			select sht_id,EMPNO,mdays,0 from empmast 
				where state='Permanent' OR state='Probation';
	end IF;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_gensheetdaily`(IN `v_mnth` SMALLINT, IN `v_yr` SMALLINT)
BEGIN
declare vdy int;
delete from attnddet where month(dt)=v_mnth and year(dt)=v_yr;
set vdy:=day(LAST_DAY(concat(v_yr,'-',v_mnth,'-01')));


insert into attnddet(empno,dt,dflag,present)
select t.uid,date(concat(v_yr,'-',v_mnth,'-',dy)) dt,
if(DAYOFWEEK(date(concat(v_yr,'-',v_mnth,'-',dy)))=woff,'WO',null) dflag,0 from mdy,
(select distinct(uid),woff from attdata a 
	left join empmast e on e.cardno=a.uid
	where month(dtime)=v_mnth and year(dtime)=v_yr) t
where dy<=vdy and date(concat(v_yr,'-',v_mnth,'-',dy)) is not null; 
update attnddet a
	inner join empmast e on a.empno=e.empno
	inner join holiday h on a.dt=h.dte AND e.CATCODE=h.catcode
 set dflag='HO',present=1
	where month(dt)=v_mnth and year(dt)=v_yr; 
update attnddet a
	inner join empmast e on a.empno=e.empno
	inner join holidayrange h on a.dt between h.dte and h.edte AND e.CATCODE=h.catcode
 set dflag='HR',present=1
	where month(dt)=v_mnth and year(dt)=v_yr;
update attnddet a inner join workday h on a.dt=h.dte set dflag=null
 where month(dt)=v_mnth and year(dt)=v_yr;
update attnddet a inner join
(select i.uid,i.dt,IFNULL(o.shift,i.shift) shift,time(i.dtime) tin,time(o.dtime) tout,
        timediff(o.dtime,i.dtime) dur,if(i.dtime is null,0,1) present,i.cmnt from attdata i 
        left join attdata o on i.uid=o.uid and i.dt=o.dt and o.minout='out' 
        where i.minout='in' and TIMESTAMPDIFF(MINUTE,i.dtime,o.dtime)>0 
					and month(i.dt)=v_mnth and year(i.dt)=v_yr) t
					on a.empno=t.uid and a.dt=t.dt
set a.shift=t.shift,a.intime=t.tin,a.outtime=t.tout,a.workDur=t.dur,a.present=t.present,a.late=t.cmnt;

	
call sp_applyleave(v_mnth,v_yr);
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`192.168.%` PROCEDURE `sp_gensheetdailyOld`(`v_mnth` SMALLINT, `v_yr` SMALLINT)
BEGIN
declare vdy int;
delete from attnddet where month(dt)=v_mnth and year(dt)=v_yr;
set vdy:=day(LAST_DAY(concat(v_yr,'-',v_mnth,'-01')));


insert into attnddet(empno,dt,dflag,present)
select t.uid,date(concat(v_yr,'-',v_mnth,'-',dy)) dt,
if(DAYOFWEEK(date(concat(v_yr,'-',v_mnth,'-',dy)))=woff,'WO',null) dflag,0 from mdy,
(select distinct(uid),woff from attdata a 
	left join empmast e on e.cardno=a.uid
	where month(dtime)=v_mnth and year(dtime)=v_yr) t
where dy<=vdy and date(concat(v_yr,'-',v_mnth,'-',dy)) is not null; 
update attnddet a
	inner join empmast e on a.empno=e.empno
	inner join holiday h on a.dt=h.dte AND e.CATCODE=h.catcode
 set dflag='HO',present=1
	where month(dt)=v_mnth and year(dt)=v_yr; 
update attnddet a
	inner join empmast e on a.empno=e.empno
	inner join holidayrange h on a.dt between h.dte and h.edte AND e.CATCODE=h.catcode
 set dflag='HR',present=1
	where month(dt)=v_mnth and year(dt)=v_yr;
update attnddet a inner join workday h on a.dt=h.dte set dflag=null
 where month(dt)=v_mnth and year(dt)=v_yr;
update attnddet a inner join
(select i.uid,i.dt,IFNULL(o.shift,i.shift) shift,time(i.dtime) tin,time(o.dtime) tout,
        timediff(o.dtime,i.dtime) dur,if(i.dtime is null,0,1) present,i.cmnt from attdata i 
        left join attdata o on i.uid=o.uid and i.dt=o.dt and o.minout='out' 
        where i.minout='in' and TIMESTAMPDIFF(MINUTE,i.dtime,o.dtime)>0 
					and month(i.dt)=v_mnth and year(i.dt)=v_yr) t
					on a.empno=t.uid and a.dt=t.dt
set a.shift=t.shift,a.intime=t.tin,a.outtime=t.tout,a.workDur=t.dur,a.present=t.present,a.late=t.cmnt;

	
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`192.168.%` PROCEDURE `sp_gensheetwkly`(`v_week` SMALLINT, `v_mnth` SMALLINT, `v_yr` SMALLINT)
BEGIN
	declare sht_id int(11) UNSIGNED DEFAULT NULL;
	delete from wklysheet where wk=v_week and mnth=v_mnth and yr=v_yr;
	insert into wklysheet(mnth,yr,wk) values(v_mnth,v_yr,v_week);
	select LAST_INSERT_ID() into sht_id;
	if (sht_id is not null) THEN
		insert into wklysheet_det(sheet_id,empno,attnd,whr,rate)
			select sht_id,EMPNO,6,71.50,174 from empmast e 
				where weekly=1;
	end IF;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`192.168.%` PROCEDURE `sp_gensheet_copy`(`v_mnth` SMALLINT, `v_yr` SMALLINT)
BEGIN
	declare sht_id int(11) UNSIGNED DEFAULT NULL;
	declare mdays,v_wdays smallint;
	declare wo_cnt int default 0;
  declare ho_cnt int default 0;
	select count(*) into wo_cnt from mdy where WEEKDAY(date(concat(v_yr,'-',v_mnth,'-',dy)))=6;
	select count(*) into ho_cnt from holiday where month(dte)=v_mnth and year(dte)=v_yr;
	set mdays=DAY(LAST_DAY(concat(v_yr,'-',v_mnth,'-1')));
  set v_wdays=daycnt(v_mnth,v_yr);
	delete from sheet where mnth=v_mnth and yr=v_yr;
	insert into sheet(mnth,yr) values(v_mnth,v_yr);
	select LAST_INSERT_ID() into sht_id;
	if (sht_id is not null) THEN
		insert into sheet_det(sheet_id,empno,attnd,lwop,spleave,pay_mode,wdays,hdays,off_days)
			select sht_id,EMPNO,v_wdays,0,0,'Bank',v_wdays,ho_cnt,wo_cnt from empmast e
				inner join category cg on e.catcode=cg.catcode 
				where weekly=0 and cg.payment='Yes';
	end IF;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`192.168.%` PROCEDURE `sp_syncsheet`(`v_mnth` SMALLINT, `v_yr` SMALLINT)
BEGIN
update sheet_det sd 
  INNER JOIN sheet s on sd.sheet_id=s.sheet_id
  INNER JOIN (select empno,sum(present=1) ndy,
							sum(TIME_TO_SEC(if(dflag='WO',workDur,TIMEDIFF(workDur,'08:00:00')))/3600) othr from attnddet a
							where month(a.dt)=v_mnth and year(a.dt)=v_yr
							group by empno) t on t.empno=sd.empno
	set attnd=least(ndy,wdays),othour=ifnull(othr,0)
  where s.mnth=v_mnth and s.yr=v_yr;

END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`%` PROCEDURE `tttt`()
BEGIN
declare da int default 45;
declare hra int default 10;
declare spl int default 0;
select ifnull(allowance,da) into da from gblall where descr='DA';
select ifnull(allowance,hra) into da from gblall where descr='HRA';
update empmast e 
left join indall i on e.EMPNO=i.empno and i.descr='SPL'
set oml=if(get_gross(basic,grdpay,da,hra,i.allowance)<=21000,0,12),MEDICAL_LEAVE=if(get_gross(basic,grdpay,da,hra,i.allowance)<=21000,0,12) 
where state<>'Retired';

END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-05-20 19:15:42
