<?php

session_start();


/* =====================================================
   DESTROY CURRENT LOGIN SESSION
===================================================== */

$_SESSION = array();

session_destroy();


/* =====================================================
   KEEP REMEMBER ME COOKIE
===================================================== */

/*
   We intentionally DO NOT delete:
   remember_token

   This allows Remember Me to work after logout.
*/


/* =====================================================
   RETURN TO HOME
===================================================== */

header("Location: index.php");
exit;

?>