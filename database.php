<?php

date_default_timezone_set('Asia/Manila');

/**
 * Supabase Postgres connection (PDO).
 *
 * Reads the standard Postgres environment variables injected by the Vercel
 * Supabase integration (POSTGRES_URL / POSTGRES_HOST / ...). When those are not
 * present in the process environment (e.g. running locally under XAMPP), it
 * falls back to parsing a local .env file in this directory, so the same code
 * works both on Vercel and on a developer machine.
 *
 * Every endpoint gets both `$pdo` and `$conn` (an alias of the same PDO
 * instance) for backward compatibility with the old code.
 */

if (!function_exists('rotc_load_env_file')) {
    function rotc_load_env_file(string $path): void
    {
        if (!is_readable($path)) {
            return;
        }
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            return;
        }
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || $line[0] === '#') {
                continue;
            }
            $eq = strpos($line, '=');
            if ($eq === false) {
                continue;
            }
            $key = trim(substr($line, 0, $eq));
            $val = trim(substr($line, $eq + 1));
            if ($key === '') {
                continue;
            }
            // Strip surrounding single/double quotes.
            $len = strlen($val);
            if ($len >= 2 && ($val[0] === '"' || $val[0] === "'") && $val[$len - 1] === $val[0]) {
                $val = substr($val, 1, -1);
            }
            if (getenv($key) === false) {
                putenv("$key=$val");
                $_ENV[$key] = $val;
            }
        }
    }
}

if (!function_exists('rotc_env')) {
    function rotc_env(string $key): ?string
    {
        $v = getenv($key);
        if ($v === false || $v === '') {
            $v = $_ENV[$key] ?? $_SERVER[$key] ?? null;
        }
        return ($v === false || $v === '' || $v === null) ? null : $v;
    }
}

// Load local env files (no-op on Vercel where the variables already exist).
foreach (['.env.development.local', '.env.local', '.env'] as $envFile) {
    rotc_load_env_file(__DIR__ . '/' . $envFile);
}

if (!function_exists('rotc_pdo')) {
    function rotc_pdo(): PDO
    {
        static $pdo = null;
        if ($pdo instanceof PDO) {
            return $pdo;
        }

        // Prefer the direct (non-pooling) connection for full prepared-statement
        // support; fall back to the pooled URL, then discrete variables.
        $url = rotc_env('POSTGRES_URL_NON_POOLING')
            ?? rotc_env('POSTGRES_URL')
            ?? rotc_env('DATABASE_URL');

        $host = $port = $user = $pass = $db = null;

        if ($url !== null) {
            $parts = parse_url($url);
            if ($parts !== false) {
                $host = $parts['host'] ?? null;
                $port = isset($parts['port']) ? (string) $parts['port'] : null;
                $user = isset($parts['user']) ? urldecode($parts['user']) : null;
                $pass = isset($parts['pass']) ? urldecode($parts['pass']) : null;
                $db   = isset($parts['path']) ? ltrim($parts['path'], '/') : null;
            }
        }

        $host = $host ?: rotc_env('POSTGRES_HOST');
        $port = $port ?: (rotc_env('POSTGRES_PORT') ?? '5432');
        $user = $user ?: rotc_env('POSTGRES_USER');
        $pass = $pass ?? rotc_env('POSTGRES_PASSWORD');
        $db   = $db ?: (rotc_env('POSTGRES_DATABASE') ?? 'postgres');

        if (!$host || !$user || !$db) {
            http_response_code(500);
            die(json_encode([
                'success' => false,
                'error'   => 'Database not configured',
                'message' => 'Missing Supabase Postgres environment variables '
                    . '(POSTGRES_URL, or POSTGRES_HOST / POSTGRES_USER / POSTGRES_PASSWORD / POSTGRES_DATABASE).',
            ]));
        }

        $dsn = sprintf('pgsql:host=%s;port=%s;dbname=%s;sslmode=require', $host, $port, $db);

        try {
            $pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                // Emulated prepares keep Postgres happy with untyped literals and
                // work through the connection pooler.
                PDO::ATTR_EMULATE_PREPARES   => true,
            ]);
            $pdo->exec("SET TIME ZONE 'Asia/Manila'");
        } catch (PDOException $e) {
            http_response_code(500);
            die(json_encode([
                'success' => false,
                'error'   => 'Database connection failed',
                'message' => $e->getMessage(),
                'hint'    => 'Verify the Supabase Postgres credentials are available to PHP.',
            ]));
        }

        return $pdo;
    }
}

$pdo  = rotc_pdo();
$conn = $pdo; // Backward-compatible alias used across endpoints.
