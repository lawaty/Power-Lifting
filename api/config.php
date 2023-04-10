<?php

date_default_timezone_set('Africa/Cairo');
ini_set("precision", 3);

// Locations
const LOCAL = ".";
const API_DIR = "API%20Framework";
const LOG_DIR = LOCAL."/core/logs";

const DEBUG = false;

// Sqlite DB
define("DB_PATH", LOCAL."/core/database/db.db");

// JWT Secret
const SECRET = 'lol';
