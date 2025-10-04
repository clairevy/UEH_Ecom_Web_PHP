<?php

const _HOST = 'localhost';
const _DB = 'db_ecom';
const _USER = 'root';
const _PASSWORD = '';
const _DRIVER = 'mysql';

// Base URL configuration
define('BASE_URL', '/Ecom_website');
define('FULL_BASE_URL', 'http://' . $_SERVER['HTTP_HOST'] . BASE_URL);

// Include URL helper functions
require_once __DIR__ . '/../helpers/url_helper.php';