<?php

function connectDB()
{
  $host = getenv('DB_HOST') ?: 'db';
  $port = getenv('DB_PORT') ?: '3306';
  $user = getenv('MYSQL_USER') ?: 'root';
  $password = getenv('MYSQL_PASSWORD') ?: '';
  $database = getenv('MYSQL_DATABASE') ?: '';

  $conn = new mysqli("$host:$port", $user, $password, $database);

  if ($conn->connect_error) {
    die("La connexion à la base de données a échoué : " . $conn->connect_error);
  }
  return $conn;
}
