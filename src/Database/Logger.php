<?php
namespace Libyaf\Database;

class Logger implements \Doctrine\DBAL\Logging\SQLLogger
{
    //超过一定条数执行清理
    const FLUSH_COUNT = 50;

    public $queries = [];

    private $currentQuery = 0;

    private $start = null;

    private $group;

    public function __construct($group = 'default')
    {
        $this->group = $group;
    }

    public function startQuery($sql, array $params = null, array $types = null)
    {
        $this->start = microtime(true);

        $this->queries[++$this->currentQuery] = [
            'group'         => $this->group,
            'sql'           => $sql,
            'params'        => $params,
            'types'         => $types,
            'executionMS'   => 0,
        ];

    }

    public function stopQuery()
    {
        $this->queries[$this->currentQuery]['executionMS'] = microtime(true) - $this->start;

        \Libyaf\Logkit\Logger::ins('_sql')->info(json_encode($this->queries[$this->currentQuery]));

        if ($this->currentQuery % self::FLUSH_COUNT === 0) {
            $this->queries = [];
        }
    }
}

