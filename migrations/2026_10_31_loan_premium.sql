-- Monthly premium on a loan. When set, it replaces amount / tenure as the monthly principal
-- (premium) deduction from the next payroll run on; installments already deducted stay as they are.
-- NULL = amount / tenure, as before. Run after 2026_10_30_loan_types.sql.

ALTER TABLE loanmast ADD COLUMN PREMIUM decimal(10,2) unsigned DEFAULT NULL AFTER NOINST;
