<?php

session_start();
session_destroy();
header('Location: ../forms/authorize.php');
exit();
