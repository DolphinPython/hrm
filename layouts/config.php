<?php
/* ===============================
   Database Configuration (Hostinger)
   =============================== */
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'hrm');

/* ===============================
   Create Database Connection (Singleton)
   =============================== */
if (!isset($GLOBALS['con']) || $GLOBALS['con'] === null) {
    $GLOBALS['con'] = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

    if ($GLOBALS['con'] === false) {
        die("❌ ERROR: Could not connect. " . mysqli_connect_error());
    }

    // ✅ Set MySQL timezone
    mysqli_query($GLOBALS['con'], "SET time_zone = '+05:30'");
}

/* ===============================
   PHP Timezone
   =============================== */
date_default_timezone_set('Asia/Kolkata');

/* ===============================
   Gmail Credentials (for email sending)
   =============================== */
$gmailid       = '';   // Your Gmail address
$gmailpassword = '';   // Your Gmail app password (not the main Gmail password)
$gmailusername = '';   // Display name for email

/* ===============================
   Close Connection on Script End
   =============================== */
register_shutdown_function(function () {
    if (isset($GLOBALS['con']) && $GLOBALS['con'] !== null) {
        mysqli_close($GLOBALS['con']);
        $GLOBALS['con'] = null;
    }
});
?>
