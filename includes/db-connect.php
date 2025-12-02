<?php
/**
 * Fast and Fine Technical Services FZE - Database Connection Handler
 *
 * This file provides a singleton PDO database connection with:
 * - Secure prepared statement support
 * - Transaction management
 * - Error handling and logging
 * - Connection pooling
 * - Query debugging (development mode only)
 *
 * @package FastAndFine
 * @version 1.0.0
 * @author Fast and Fine Technical Services FZE
 */

// Prevent direct access
if(!defined('FAST_FINE_APP')) {
    die('Direct access not permitted');
}

// Require configuration
require_once __DIR__ . '/../config.php';

/**
 * Database Connection Class (Singleton Pattern)
 *
 * Provides secure database connectivity with PDO
 */
class Database {

    /**
     * @var Database Singleton instance
     */
    private static $instance = null;

    /**
     * @var PDO Database connection object
     */
    private $connection = null;

    /**
     * @var bool Transaction active flag
     */
    private $inTransaction = false;

    /**
     * @var array Query log for debugging
     */
    private $queryLog = [];

    /**
     * Private constructor to prevent direct instantiation
     */
    private function __construct() {
        $this->connect();
    }

    /**
     * Prevent cloning of singleton instance
     */
    private function __clone() {}

    /**
     * Prevent unserialization of singleton instance
     */
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }

    /**
     * Get singleton instance
     *
     * @return Database
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Establish database connection
     *
     * @throws PDOException
     */
    private function connect() {
        try {
            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=%s',
                DB_HOST,
                DB_NAME,
                DB_CHARSET
            );

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::ATTR_PERSISTENT         => false, // Connection pooling
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET . " COLLATE " . DB_COLLATION
            ];

            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);

            if (isDebugMode()) {
                error_log('[DB] Database connection established successfully');
            }

        } catch (PDOException $e) {
            $this->handleConnectionError($e);
        }
    }

    /**
     * Handle connection errors
     *
     * @param PDOException $e
     * @throws Exception
     */
    private function handleConnectionError($e) {
        $errorMessage = '[DB ERROR] Connection failed: ' . $e->getMessage();
        error_log($errorMessage);

        if (isDebugMode()) {
            throw new Exception('Database connection failed: ' . $e->getMessage());
        } else {
            throw new Exception('Database connection error. Please try again later.');
        }
    }

    /**
     * Get PDO connection object
     *
     * @return PDO
     */
    public function getConnection() {
        return $this->connection;
    }

    /**
     * Execute a prepared query
     *
     * @param string $query SQL query with placeholders
     * @param array $params Parameters to bind
     * @return PDOStatement
     * @throws Exception
     */
    public function query($query, $params = []) {
        try {
            $stmt = $this->connection->prepare($query);

            // Log query in debug mode
            if (isDebugMode()) {
                $this->logQuery($query, $params);
            }

            // Bind parameters
            foreach ($params as $key => $value) {
                $paramType = $this->getParamType($value);

                if (is_int($key)) {
                    $stmt->bindValue($key + 1, $value, $paramType);
                } else {
                    $stmt->bindValue($key, $value, $paramType);
                }
            }

            $stmt->execute();

            return $stmt;

        } catch (PDOException $e) {
            $this->handleQueryError($e, $query, $params);
        }
    }

    /**
     * Execute SELECT query and fetch all results
     *
     * @param string $query SQL SELECT query
     * @param array $params Parameters to bind
     * @return array
     */
    public function select($query, $params = []) {
        $stmt = $this->query($query, $params);
        return $stmt->fetchAll();
    }

    /**
     * Execute SELECT query and fetch single row
     *
     * @param string $query SQL SELECT query
     * @param array $params Parameters to bind
     * @return array|false
     */
    public function selectOne($query, $params = []) {
        $stmt = $this->query($query, $params);
        return $stmt->fetch();
    }

    /**
     * Execute INSERT query
     *
     * @param string $query SQL INSERT query
     * @param array $params Parameters to bind
     * @return int Last insert ID
     */
    public function insert($query, $params = []) {
        $this->query($query, $params);
        return (int) $this->connection->lastInsertId();
    }

    /**
     * Execute UPDATE query
     *
     * @param string $query SQL UPDATE query
     * @param array $params Parameters to bind
     * @return int Number of affected rows
     */
    public function update($query, $params = []) {
        $stmt = $this->query($query, $params);
        return $stmt->rowCount();
    }

    /**
     * Execute DELETE query
     *
     * @param string $query SQL DELETE query
     * @param array $params Parameters to bind
     * @return int Number of affected rows
     */
    public function delete($query, $params = []) {
        $stmt = $this->query($query, $params);
        return $stmt->rowCount();
    }

    /**
     * Begin database transaction
     *
     * @return bool
     */
    public function beginTransaction() {
        if (!$this->inTransaction) {
            $this->inTransaction = $this->connection->beginTransaction();

            if (isDebugMode()) {
                error_log('[DB] Transaction started');
            }

            return $this->inTransaction;
        }
        return false;
    }

    /**
     * Commit database transaction
     *
     * @return bool
     */
    public function commit() {
        if ($this->inTransaction) {
            $result = $this->connection->commit();
            $this->inTransaction = false;

            if (isDebugMode()) {
                error_log('[DB] Transaction committed');
            }

            return $result;
        }
        return false;
    }

    /**
     * Rollback database transaction
     *
     * @return bool
     */
    public function rollback() {
        if ($this->inTransaction) {
            $result = $this->connection->rollBack();
            $this->inTransaction = false;

            if (isDebugMode()) {
                error_log('[DB] Transaction rolled back');
            }

            return $result;
        }
        return false;
    }

    /**
     * Check if record exists
     *
     * @param string $table Table name
     * @param string $column Column name
     * @param mixed $value Value to check
     * @return bool
     */
    public function exists($table, $column, $value) {
        $query = "SELECT COUNT(*) as count FROM {$table} WHERE {$column} = :value LIMIT 1";
        $result = $this->selectOne($query, ['value' => $value]);
        return $result && $result['count'] > 0;
    }

    /**
     * Get total row count from table with optional conditions
     *
     * @param string $table Table name
     * @param string $where WHERE clause (optional)
     * @param array $params Parameters for WHERE clause
     * @return int
     */
    public function count($table, $where = '', $params = []) {
        $query = "SELECT COUNT(*) as count FROM {$table}";

        if (!empty($where)) {
            $query .= " WHERE {$where}";
        }

        $result = $this->selectOne($query, $params);
        return $result ? (int) $result['count'] : 0;
    }

    /**
     * Determine PDO parameter type based on value
     *
     * @param mixed $value
     * @return int PDO parameter type constant
     */
    private function getParamType($value) {
        if (is_int($value)) {
            return PDO::PARAM_INT;
        } elseif (is_bool($value)) {
            return PDO::PARAM_BOOL;
        } elseif (is_null($value)) {
            return PDO::PARAM_NULL;
        } else {
            return PDO::PARAM_STR;
        }
    }

    /**
     * Log query for debugging
     *
     * @param string $query
     * @param array $params
     */
    private function logQuery($query, $params) {
        $this->queryLog[] = [
            'query' => $query,
            'params' => $params,
            'timestamp' => microtime(true)
        ];

        error_log('[DB QUERY] ' . $query);
        if (!empty($params)) {
            error_log('[DB PARAMS] ' . json_encode($params));
        }
    }

    /**
     * Get query log (debug mode only)
     *
     * @return array
     */
    public function getQueryLog() {
        return $this->queryLog;
    }

    /**
     * Handle query errors
     *
     * @param PDOException $e
     * @param string $query
     * @param array $params
     * @throws Exception
     */
    private function handleQueryError($e, $query, $params) {
        $errorMessage = sprintf(
            '[DB ERROR] Query failed: %s | Query: %s | Params: %s',
            $e->getMessage(),
            $query,
            json_encode($params)
        );

        error_log($errorMessage);

        // Rollback transaction if active
        if ($this->inTransaction) {
            $this->rollback();
        }

        if (isDebugMode()) {
            throw new Exception('Database query error: ' . $e->getMessage() . ' | Query: ' . $query);
        } else {
            throw new Exception('Database error. Please try again later.');
        }
    }

    /**
     * Test database connection
     *
     * @return bool
     */
    public function testConnection() {
        try {
            $stmt = $this->connection->query('SELECT 1');
            return $stmt !== false;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Get database statistics
     *
     * @return array
     */
    public function getStats() {
        try {
            $stats = [
                'connected' => $this->testConnection(),
                'database' => DB_NAME,
                'host' => DB_HOST,
                'charset' => DB_CHARSET,
                'queries_executed' => count($this->queryLog)
            ];

            // Get database size
            $result = $this->selectOne("
                SELECT
                    SUM(data_length + index_length) as size
                FROM information_schema.TABLES
                WHERE table_schema = :dbname
            ", ['dbname' => DB_NAME]);

            $stats['database_size'] = $result ? $result['size'] : 0;

            return $stats;

        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Close database connection
     */
    public function close() {
        if ($this->inTransaction) {
            $this->rollback();
        }

        $this->connection = null;

        if (isDebugMode()) {
            error_log('[DB] Database connection closed');
        }
    }

    /**
     * Destructor - ensure connection is closed
     */
    public function __destruct() {
        $this->close();
    }
}

// ============================================================================
// HELPER FUNCTIONS FOR EASY ACCESS
// ============================================================================

/**
 * Get database instance
 *
 * @return Database
 */
function db() {
    return Database::getInstance();
}

/**
 * Execute SELECT query (shorthand)
 *
 * @param string $query
 * @param array $params
 * @return array
 */
function dbSelect($query, $params = []) {
    return db()->select($query, $params);
}

/**
 * Execute SELECT ONE query (shorthand)
 *
 * @param string $query
 * @param array $params
 * @return array|false
 */
function dbSelectOne($query, $params = []) {
    return db()->selectOne($query, $params);
}

/**
 * Execute INSERT query (shorthand)
 *
 * @param string $query
 * @param array $params
 * @return int Last insert ID
 */
function dbInsert($query, $params = []) {
    return db()->insert($query, $params);
}

/**
 * Execute UPDATE query (shorthand)
 *
 * @param string $query
 * @param array $params
 * @return int Affected rows
 */
function dbUpdate($query, $params = []) {
    return db()->update($query, $params);
}

/**
 * Execute DELETE query (shorthand)
 *
 * @param string $query
 * @param array $params
 * @return int Affected rows
 */
function dbDelete($query, $params = []) {
    return db()->delete($query, $params);
}

/**
 * Check if record exists (shorthand)
 *
 * @param string $table
 * @param string $column
 * @param mixed $value
 * @return bool
 */
function dbExists($table, $column, $value) {
    return db()->exists($table, $column, $value);
}

/**
 * Count records (shorthand)
 *
 * @param string $table
 * @param string $where
 * @param array $params
 * @return int
 */
function dbCount($table, $where = '', $params = []) {
    return db()->count($table, $where, $params);
}

// ============================================================================
// INITIALIZATION
// ============================================================================

// Test connection in debug mode
if (isDebugMode()) {
    try {
        $db = Database::getInstance();
        if ($db->testConnection()) {
            error_log('[DB] Connection test successful');
        } else {
            error_log('[DB] Connection test failed');
        }
    } catch (Exception $e) {
        error_log('[DB] Initialization error: ' . $e->getMessage());
    }
}

/**
 * USAGE EXAMPLES:
 *
 * // Get database instance
 * $db = Database::getInstance();
 * // Or use shorthand
 * $db = db();
 *
 * // SELECT all records
 * $users = db()->select("SELECT * FROM users WHERE role = :role", ['role' => 'admin']);
 * // Or use shorthand
 * $users = dbSelect("SELECT * FROM users WHERE role = :role", ['role' => 'admin']);
 *
 * // SELECT one record
 * $user = db()->selectOne("SELECT * FROM users WHERE id = :id", ['id' => 1]);
 *
 * // INSERT record
 * $userId = db()->insert("
 *     INSERT INTO users (username, email, password_hash)
 *     VALUES (:username, :email, :password)
 * ", [
 *     'username' => 'john_doe',
 *     'email' => 'john@example.com',
 *     'password' => password_hash('password123', PASSWORD_BCRYPT)
 * ]);
 *
 * // UPDATE record
 * $affected = db()->update("
 *     UPDATE users
 *     SET last_login = NOW()
 *     WHERE id = :id
 * ", ['id' => $userId]);
 *
 * // DELETE record
 * $deleted = db()->delete("DELETE FROM users WHERE id = :id", ['id' => $userId]);
 *
 * // Check if exists
 * $exists = db()->exists('users', 'email', 'john@example.com');
 *
 * // Count records
 * $count = db()->count('bookings', 'status = :status', ['status' => 'pending']);
 *
 * // Transactions
 * try {
 *     db()->beginTransaction();
 *
 *     db()->insert("INSERT INTO bookings (...) VALUES (...)", [...]);
 *     db()->update("UPDATE services SET bookings_count = bookings_count + 1 WHERE id = :id", ['id' => 1]);
 *
 *     db()->commit();
 * } catch (Exception $e) {
 *     db()->rollback();
 *     error_log('Transaction failed: ' . $e->getMessage());
 * }
 */
