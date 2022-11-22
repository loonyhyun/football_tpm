<?php

session_start();

session_unset(); //session reset

session_destroy();

echo "<script>location.href='./index.php'</script>";
  
?>