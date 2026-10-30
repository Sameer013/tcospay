-- Salary component codes up to 50 characters (indallmast.code is varchar(50)).
-- A component's code is copied as the head name into indall / gblall and onto every payslip line
-- (trnsdet1 / trnsdet2), so all of them must be able to hold it. Widening keeps existing data.
-- Run after 2026_10_30_loan_types.sql.

ALTER TABLE trnsdet1 MODIFY DESCR varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;
ALTER TABLE trnsdet2 MODIFY DESCR varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;
ALTER TABLE indall MODIFY DESCR varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL;
ALTER TABLE gblall MODIFY DESCR varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL;
ALTER TABLE loantype
  MODIFY PRIN_HEAD varchar(50) NOT NULL,
  MODIFY INT_HEAD varchar(50) DEFAULT NULL;
