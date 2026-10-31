-- Payslip redesign.
-- 1. Aadhaar number on the employee record (shown on the payslip, entered in Employee Master).
-- 2. Telco Co-operative Society staff are all "Staff": rename the leftover "Principal" post and grade.

ALTER TABLE empmast ADD COLUMN AADHAR varchar(12) DEFAULT NULL AFTER PAN;

UPDATE dsgmast SET DESCR = 'Staff' WHERE DESCR = 'Principal';
UPDATE designationmaster SET designation = 'Staff' WHERE designation = 'Principal';
UPDATE grademaster SET Grade = 'Staff' WHERE Grade = 'Principal';
