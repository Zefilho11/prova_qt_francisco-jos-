<?php
session_start();
session_destroy();
header("Location: login.php?message=Você saiu da plataforma.");
?>
