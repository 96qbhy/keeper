<?php
/**
 * ConnectionManager.php
 *
 * @copyright Chongyi <xpz3847878@163.com>
 * @link      https://insp.top
 */

namespace Dybasedev\Keeper\Database\SQL;

use Dybasedev\Keeper\Database\ConnectionManager as BaseConnectionManager;
use Dybasedev\Keeper\Database\SQL\Connections\MySQLConnection;
use RuntimeException;

class ConnectionManager extends BaseConnectionManager
{
    public function createConnection($name)
    {
        switch ($this->config['connections'][$name]['driver']) {
            case 'mysql':
                return new MySQLConnection($this->config['connections'][$name]);
            default:
                throw new RuntimeException();
        }
    }
}