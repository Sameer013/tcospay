<?php
/*
 * Organisation details printed on the salary reports. Change them here only.
 */
define('ORG_NAME', 'TELCO CO-OPERATIVE SOCIETY LTD');
define('ORG_ADDRESS', 'Tata Motors, Telco Colony, Jamshedpur - 831004');

// Bank covering letter (letter.php)
define('ORG_LETTER_REF_PREFIX', 'TCS/BANK/');
define('ORG_BANK_NAME', 'State Bank of India');
define('ORG_BANK_BRANCH', 'Golmuri Branch');
define('ORG_BANK_CITY', 'Jamshedpur');
define('ORG_CONTACT_MOBILE', '9430371428');

// Signatories at the foot of the bank reports
define('ORG_SIGNATORIES', 'ADMIN|CHAIRPERSON|SECRETARY|TREASURER');

// Financial year (April - March) a month falls in, e.g. 2026-27
function orgFinancialYear($month, $year)
{
    $start = $month >= 4 ? $year : $year - 1;
    return $start . '-' . substr($start + 1, -2);
}
