<?php
require_once dirname(__DIR__) . "/helpers/env.php";

if (!class_exists("CompatResult")) {
    class CompatResult
    {
        /** @var array<int, array<string, mixed>> */
        private array $rows;
        private int $index = 0;
        public int $num_rows = 0;

        /**
         * @param array<int, array<string, mixed>> $rows
         */
        public function __construct(array $rows)
        {
            $this->rows = $rows;
            $this->num_rows = count($rows);
        }

        /**
         * @return array<string, mixed>|null
         */
        public function fetch_assoc(): ?array
        {
            if ($this->index >= $this->num_rows) {
                return null;
            }

            $row = $this->rows[$this->index];
            $this->index += 1;
            return $row;
        }

        /**
         * @return array<int, array<string, mixed>>
         */
        public function fetch_all(int $mode = 1): array
        {
            return $this->rows;
        }
    }
}

if (!class_exists("CompatConnection")) {
    class CompatConnection
    {
        private PDO $pdo;
        private string $driver;

        public string $connect_error = "";
        /** @var int|string */
        public $insert_id = 0;

        public function __construct(PDO $pdo, string $driver)
        {
            $this->pdo = $pdo;
            $this->driver = $driver;
        }

        /**
         * @return CompatStatement|false
         */
        public function prepare(string $sql)
        {
            try {
                $stmt = $this->pdo->prepare($sql);
                if ($stmt === false) {
                    return false;
                }
                return new CompatStatement($this, $stmt, $this->driver);
            } catch (Throwable $error) {
                return false;
            }
        }

        /**
         * @return CompatResult|false
         */
        public function query(string $sql)
        {
            try {
                $stmt = $this->pdo->query($sql);
                if ($stmt === false) {
                    return false;
                }
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return new CompatResult($rows ?: []);
            } catch (Throwable $error) {
                return false;
            }
        }

        public function begin_transaction(): bool
        {
            if ($this->pdo->inTransaction()) {
                return true;
            }
            return $this->pdo->beginTransaction();
        }

        public function commit(): bool
        {
            if (!$this->pdo->inTransaction()) {
                return true;
            }
            return $this->pdo->commit();
        }

        public function rollback(): bool
        {
            if (!$this->pdo->inTransaction()) {
                return true;
            }
            return $this->pdo->rollBack();
        }

        public function close(): void
        {
            // No-op. PDO closes on object destruction.
        }

        public function refreshInsertId(): void
        {
            try {
                $id = $this->pdo->lastInsertId();
                if (is_string($id) && $id !== "") {
                    $this->insert_id = ctype_digit($id) ? (int) $id : $id;
                }
            } catch (Throwable $error) {
                $this->insert_id = 0;
            }
        }
    }
}

if (!class_exists("CompatStatement")) {
    class CompatStatement
    {
        private CompatConnection $connection;
        private PDOStatement $statement;

        /** @var array<int, mixed> */
        private array $boundValues = [];
        /** @var array<int, array<string, mixed>>|null */
        private ?array $bufferedRows = null;
        private int $fetchIndex = 0;

        /** @var array<int, mixed> */
        private array $boundResultReferences = [];

        public int $num_rows = 0;
        public int $affected_rows = 0;
        public string $error = "";

        public function __construct(CompatConnection $connection, PDOStatement $statement, string $driver)
        {
            $this->connection = $connection;
            $this->statement = $statement;
        }

        public function bind_param(string $types, ...$vars): bool
        {
            $this->boundValues = [];
            $length = strlen($types);

            foreach ($vars as $index => $value) {
                $type = $index < $length ? $types[$index] : "s";
                if ($value === null) {
                    $this->boundValues[] = null;
                    continue;
                }

                if ($type === "i") {
                    $this->boundValues[] = (int) $value;
                } elseif ($type === "d") {
                    $this->boundValues[] = (float) $value;
                } else {
                    $this->boundValues[] = (string) $value;
                }
            }

            return true;
        }

        public function execute(): bool
        {
            try {
                foreach ($this->boundValues as $idx => $value) {
                    $paramIndex = $idx + 1;
                    if ($value === null) {
                        $this->statement->bindValue($paramIndex, null, PDO::PARAM_NULL);
                    } elseif (is_int($value)) {
                        $this->statement->bindValue($paramIndex, $value, PDO::PARAM_INT);
                    } else {
                        $this->statement->bindValue($paramIndex, $value, PDO::PARAM_STR);
                    }
                }

                $ok = $this->statement->execute();
                if (!$ok) {
                    $this->error = "Statement execution failed.";
                    return false;
                }

                $this->affected_rows = $this->statement->rowCount();
                $this->bufferedRows = null;
                $this->fetchIndex = 0;
                $this->connection->refreshInsertId();
                return true;
            } catch (Throwable $error) {
                $this->error = $error->getMessage();
                return false;
            }
        }

        /**
         * @return CompatResult|false
         */
        public function get_result()
        {
            try {
                $rows = $this->readAllRows();
                return new CompatResult($rows);
            } catch (Throwable $error) {
                $this->error = $error->getMessage();
                return false;
            }
        }

        public function store_result(): bool
        {
            try {
                $rows = $this->readAllRows();
                $this->num_rows = count($rows);
                return true;
            } catch (Throwable $error) {
                $this->error = $error->getMessage();
                return false;
            }
        }

        public function bind_result(&...$vars): bool
        {
            $this->boundResultReferences = [];
            foreach ($vars as &$value) {
                $this->boundResultReferences[] = &$value;
            }
            return true;
        }

        public function fetch(): bool
        {
            $rows = $this->readAllRows();
            if ($this->fetchIndex >= count($rows)) {
                return false;
            }

            $values = array_values($rows[$this->fetchIndex]);
            $this->fetchIndex += 1;

            foreach ($this->boundResultReferences as $i => &$ref) {
                $ref = $values[$i] ?? null;
            }

            return true;
        }

        public function close(): bool
        {
            $this->statement->closeCursor();
            return true;
        }

        /**
         * @return array<int, array<string, mixed>>
         */
        private function readAllRows(): array
        {
            if ($this->bufferedRows !== null) {
                $this->num_rows = count($this->bufferedRows);
                return $this->bufferedRows;
            }

            $rows = $this->statement->fetchAll(PDO::FETCH_ASSOC);
            $this->bufferedRows = $rows ?: [];
            $this->num_rows = count($this->bufferedRows);
            return $this->bufferedRows;
        }
    }
}

if (!function_exists("create_db_connection")) {
    function create_db_connection(): CompatConnection
    {
        $databaseUrl = safeEnv("DATABASE_URL", "");

        $driver = safeEnv("DB_DRIVER", "mysql");
        $host = safeEnv("DB_HOST", "localhost");
        $dbName = safeEnv("DB_NAME", "TaskManagementDB");
        $user = safeEnv("DB_USER", "root");
        $pass = safeEnv("DB_PASS", "");

        if ($databaseUrl !== "") {
            $parts = parse_url($databaseUrl);
            if (is_array($parts)) {
                $scheme = isset($parts["scheme"]) ? strtolower((string) $parts["scheme"]) : "";
                if ($scheme === "postgres" || $scheme === "postgresql") {
                    $driver = "pgsql";
                } elseif ($scheme === "mysql") {
                    $driver = "mysql";
                }

                if (isset($parts["host"])) {
                    $host = (string) $parts["host"];
                }
                if (isset($parts["path"])) {
                    $dbName = ltrim((string) $parts["path"], "/");
                }
                if (isset($parts["user"])) {
                    $user = rawurldecode((string) $parts["user"]);
                }
                if (isset($parts["pass"])) {
                    $pass = rawurldecode((string) $parts["pass"]);
                }
                $port = isset($parts["port"]) ? (int) $parts["port"] : 0;
            }
        }

        if (!isset($port) || $port <= 0) {
            $defaultPort = strtolower($driver) === "pgsql" ? "5432" : "3306";
            $port = (int) safeEnv("DB_PORT", $defaultPort);
        }

        $driver = strtolower($driver);
        if ($driver === "postgres" || $driver === "postgresql") {
            $driver = "pgsql";
        }

        $dsn = "";
        if ($driver === "pgsql") {
            $dsn = "pgsql:host={$host};port={$port};dbname={$dbName}";
        } else {
            $driver = "mysql";
            $dsn = "mysql:host={$host};port={$port};dbname={$dbName};charset=utf8mb4";
        }

        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => true,
        ]);

        return new CompatConnection($pdo, $driver);
    }
}

?>
