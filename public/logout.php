<?php
session_start();
require_once '../app/controllers/Auth.php';

// Log the user out
Auth::logout();

// Redirect to homepage or login
header('Location: login.php');
exit();
