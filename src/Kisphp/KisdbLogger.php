<?php

namespace Kisphp;

class KisdbLogger extends AbstractSingleton
{
    /**
     * @var KisdbLogger
     */
    protected static $instance;

    /**
     * @var array
     */
    protected $logs = [];

    /**
     * @var bool
     */
    protected $debugEnabled = false;

    /**
     * @return $this
     */
    public function enableDebug()
    {
        $this->debugEnabled = true;

        return $this;
    }

    /**
     * @param \PDOStatement $statement
     *
     * @return $this
     */
    public function log(\PDOStatement $statement)
    {
        if ($this->debugEnabled === false) {
            return $this;
        }

        $this->logs[] = [
            'sql' => $statement->queryString,
            'error_code' => $statement->errorCode(),
            'error_message' => $statement->errorInfo()[2],
        ];

        return $this;
    }

    /**
     * @param string $message
     * @param string $code
     *
     * @return $this
     */
    public function addError($message, $code)
    {
        if ($this->debugEnabled === false) {
            return $this;
        }

        $this->logs[] = [
            'sql' => null,
            'error_code' => $code,
            'error_message' => $message,
        ];

        return $this;
    }

    /**
     * @return array
     */
    public function getLog()
    {
        return $this->logs;
    }

    /**
     * @return string
     */
    public function getLastQuery()
    {
        $lastLog = end($this->logs);

        return $lastLog['sql'];
    }
}
