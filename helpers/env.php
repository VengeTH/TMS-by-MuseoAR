<?php
// * Centralized environment loader using vlucas/phpdotenv.

$autoloadPath = dirname(__DIR__) . "/vendor/autoload.php";
if (is_file($autoloadPath)) {
    require_once $autoloadPath;
}

if (class_exists(Dotenv\Dotenv::class)) {
    // * Load .env from the project root once per request.
    $dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
    $dotenv->safeLoad();
}

if (!function_exists("safeEnv")) {
    /**
     * * Reads an environment variable from $_ENV / getenv with an optional default.
     *
     * @param string      $key       Variable name.
     * @param string|null $default   Default value if not set.
     * @param bool        $required  When true, throws if the variable is missing.
     * @return string
     */
    function safeEnv(string $key, ?string $default = null, bool $required = false): string
    {
        $value = null;
        if (isset($_ENV[$key]) && is_string($_ENV[$key])) {
            $value = $_ENV[$key];
        } else {
            $env = getenv($key);
            if (is_string($env)) {
                $value = $env;
            }
        }

        if ($value !== null) {
            return $value;
        }

        if ($default !== null) {
            return $default;
        }

        if ($required) {
            throw new RuntimeException("Missing required environment variable: " . $key);
        }

        return "";
    }
}

?>
