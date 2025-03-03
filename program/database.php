<?php

namespace Testbike;


/**
 * class for doing queries to the database
 */
class Database
{
    /**
     * @param mixed $value
     * @return string
     */
    // coinvert PHP datatypes to the proper SQL formats
    private static function sql_format($value): string
    {
        if ($value === null) {
            return 'NULL';
        } else {
            $type = gettype($value);
            switch ($type) {
                case 'bool':
                    return ($value ? 'TRUE' : 'FALSE');
                case 'integer':
                    return sprintf('%d', $value);
                case 'float':
                    return sprintf('%.4f', $value);
                case 'string':
                    return sprintf('\'%s\'', $value);
                default:
                    throw (new \Exception(sprintf('unhandled type "%s" for value %s', strval($type), json_encode($value))));
            }
        }
    }
    
    /**
     * @param string $sql SQL query string template; placeholders are marked with the prefix ":"
     * @param array $arguments
     * @return array {list<map<string,any>>}
     */
    public static function query(string $sql, array $arguments = []): array
    {
        $path = (__DIR__.'/../database/testbike.sqlite3');
        if (! file_exists($path)) {
            throw (new \Exception('database file missing'));
        } else {
            $connection = (new \SQLite3($path));
            foreach ($arguments as $key => $value) {
                $sql = str_replace(sprintf(':%s', $key), self::sql_format($value), $sql);
            }
            $result = $connection->query($sql);
            if ($result === false) {
                $message = $connection->lastErrorMsg();
                $connection->close();
                throw (new \Exception($message));
            } else {
                $rows = [];
                while ($row = $result->fetchArray(SQLITE3_ASSOC)) array_push($rows, $row);
                $connection->close();
                return $rows;
            }
        }
    }
}