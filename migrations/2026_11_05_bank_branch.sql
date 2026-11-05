-- Bank branch per employee. The bank itself comes from bankmast (BID); employees of the same
-- bank can bank at different branches, so the branch is kept on the employee record.
-- Entered in Employee Master > Finance and printed on both payslips.

ALTER TABLE empmast ADD COLUMN BANK_BRANCH varchar(80) DEFAULT NULL AFTER ACCNO;
