-- Loans & advances: loan types with an interest method, and principal / interest per installment.
-- Run once on each database (local and server).

CREATE TABLE loantype (
  CODE varchar(10) NOT NULL,
  NAME varchar(40) NOT NULL,
  PRIN_HEAD varchar(15) NOT NULL,          -- deduction head for the principal (must match the indall / report name)
  INT_HEAD varchar(15) DEFAULT NULL,       -- deduction head for the interest; NULL = no interest
  METHOD varchar(10) NOT NULL DEFAULT 'REDUCING', -- NONE | REDUCING | FLAT | PRINFIRST
  RATE decimal(6,2) NOT NULL DEFAULT 0,    -- default yearly rate %
  INT_MONTHS smallint unsigned DEFAULT NULL, -- PRINFIRST: months to recover the interest over
  SORT smallint NOT NULL DEFAULT 0,
  PRIMARY KEY (CODE)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ff

INSERT INTO loantype (CODE, NAME, PRIN_HEAD, INT_HEAD, METHOD, RATE, INT_MONTHS, SORT) VALUES
  ('ADV', 'Advance', 'Advance', NULL, 'NONE', 0, NULL, 1),
  ('PF', 'PF Loan', 'PF Loan', 'PF Int', 'REDUCING', 0, NULL, 2),
  ('SPL', 'Special Loan', 'SPL Loan', 'SPL Int', 'REDUCING', 0, NULL, 3),
  ('COM', 'Commodity Loan', 'Com Loan', 'Com Int', 'REDUCING', 0, NULL, 4);

-- Existing entries are plain advances; the old code ignored their rate, so they stay interest free.
ALTER TABLE loanmast
  ADD COLUMN LTYPE varchar(10) NOT NULL DEFAULT 'ADV' AFTER EMPNO,
  ADD COLUMN METHOD varchar(10) NOT NULL DEFAULT 'NONE' AFTER RATE,
  ADD COLUMN INT_MONTHS smallint unsigned DEFAULT NULL AFTER NOINST;

ALTER TABLE loandet
  ADD COLUMN PRIN decimal(10,2) DEFAULT NULL AFTER INSTAMT,
  ADD COLUMN INTAMT decimal(10,2) DEFAULT NULL AFTER PRIN,
  ADD COLUMN SHEET_ID int DEFAULT NULL AFTER DTE,
  ADD COLUMN IS_MANUAL tinyint(1) NOT NULL DEFAULT 0, -- 1 = a hand-typed head was deducted instead of the calculated amount
  ADD KEY idx_loandet_sheet (SHEET_ID);

UPDATE loandet SET PRIN = INSTAMT, INTAMT = 0 WHERE PRIN IS NULL;

-- Link old installments to their payroll month (only where a loan has one installment in that month),
-- so re-processing that month replaces the installment instead of adding a second one.
UPDATE loandet ld
JOIN (
  SELECT LNO, YEAR(DTE) AS yr, MONTH(DTE) AS mn
  FROM loandet
  WHERE SHEET_ID IS NULL AND DTE IS NOT NULL
  GROUP BY LNO, YEAR(DTE), MONTH(DTE)
  HAVING COUNT(*) = 1
) one ON one.LNO = ld.LNO AND one.yr = YEAR(ld.DTE) AND one.mn = MONTH(ld.DTE)
JOIN sheet s ON s.yr = one.yr AND s.mnth = one.mn
SET ld.SHEET_ID = s.sheet_id
WHERE ld.SHEET_ID IS NULL;
