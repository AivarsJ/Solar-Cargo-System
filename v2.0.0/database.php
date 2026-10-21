<?php
/**
 * Solar Cargo System kopīgais datubāzes savienojums.
 *
 * @return PDO Aktīvs PDO savienojums ar solar_cargo datubāzi.
 * @throws PDOException Ja savienojumu neizdodas izveidot.
 */
function getDatabaseConnection(): PDO
{
    $host = getenv('DB_HOST') ?: 'localhost';
    $database = getenv('DB_NAME') ?: 'solar_cargo';
    $username = getenv('DB_USER') ?: 'root';
    $password = getenv('DB_PASSWORD');
    if ($password === false) {
        $password = '';
    }

    $dsn = "mysql:host={$host};dbname={$database};charset=utf8mb4";

    return new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
}
