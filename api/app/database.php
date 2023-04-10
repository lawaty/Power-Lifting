<?php

$creations = [
  'users' => 'CREATE TABLE IF NOT EXISTS users (
    id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    username TEXT NOT NULL,
    password TEXT NOT NULL,
    email TEXT NOT NULL,
    CONSTRAINT users_UN_1 UNIQUE (username),
    CONSTRAINT users_UN_2 UNIQUE (email)
  )',
];

$insertions = [];
