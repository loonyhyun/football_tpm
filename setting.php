<?php

header("Progma:no-cache");
header("Content-Type:text/html;charset=utf-8");

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'loonyhyun' );

/** MySQL database username */
define( 'DB_USER', 'loonyhyun' );

/** MySQL database password */
define( 'DB_PASSWORD', 'bodigad0' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

$conn = mysqli_connect(
  'loonyhyun.cafe24.com',
  //'localhost',
  'loonyhyun',
  'bodigad0',
  'loonyhyun');
  
?>