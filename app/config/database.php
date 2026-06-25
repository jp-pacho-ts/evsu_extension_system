<?php
class Database
{
    private $connections;

    public function __construct()
    {
        $this->connections = [
            'local' => [
                'host' => $this->env('DB_LOCAL_HOST', '127.0.0.1'),
                'username' => $this->env('DB_LOCAL_USERNAME', 'root'),
                'password' => $this->env('DB_LOCAL_PASSWORD', ''),
                'database' => $this->env('DB_LOCAL_DATABASE', 'extension_evsu'),
                'port' => (int) $this->env('DB_LOCAL_PORT', '3306'),
            ],
            'hosted' => [
                'host' => $this->env('DB_HOSTED_HOST', 'sql308.infinityfree.com'),
                'username' => $this->env('DB_HOSTED_USERNAME', 'if0_42181193'),
                'password' => $this->env('DB_HOSTED_PASSWORD', 'I6ihUSHujmm'),
                'database' => $this->env('DB_HOSTED_DATABASE', 'if0_42181193_extension_evsu'),
                'port' => (int) $this->env('DB_HOSTED_PORT', '3306'),
            ],
        ];
    }

    public function connect()
    {
        $preferred = $this->isLocalRequest() ? 'local' : 'hosted';
        $fallback = $preferred === 'local' ? 'hosted' : 'local';
        $errors = [];

        foreach ([$preferred, $fallback] as $connectionName) {
            $config = $this->connections[$connectionName];

            try {
                $c = @new mysqli(
                    $config['host'],
                    $config['username'],
                    $config['password'],
                    $config['database'],
                    $config['port']
                );
            } catch (mysqli_sql_exception $exception) {
                $errors[] = $connectionName . ': ' . $exception->getMessage();
                continue;
            }

            if ($c->connect_error) {
                $errors[] = $connectionName . ': ' . $c->connect_error;
                continue;
            }

            $c->set_charset('utf8mb4');

            return $c;
        }

        die('Database connection failed: ' . implode(' | ', $errors));
    }

    private function isLocalRequest()
    {
        $host = strtolower($_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? '');
        $host = preg_replace('/:\\d+$/', '', $host);

        return in_array($host, ['localhost', '127.0.0.1', '::1', '[::1]'], true);
    }

    private function env($name, $default)
    {
        $value = getenv($name);

        if ($value === false || $value === '') {
            return $default;
        }

        return $value;
    }
}
?>
