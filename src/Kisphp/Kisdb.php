<?php

namespace Kisphp;

class Kisdb extends AbstractSingleton implements KisdbInterface
{
    /**
     * @var self
     */
    protected static $instance;

    /**
     * @var \PDO
     */
    protected $con;

    /**
     * @var bool
     */
    protected $isConnected = false;

    /**
     * @param string $dbHost
     * @param string $dbUser
     * @param string $dbPass
     * @param string $dbName
     * @param string $dbDriver
     *
     * @return $this
     */
    public function connect($dbHost = 'localhost', $dbUser = 'root', $dbPass = '', $dbName = 'test', $dbDriver = 'mysql')
    {
        try {
            $dsn = $this->getPdoDsn($dbHost, $dbName, $dbDriver);
            $this->con = new \PDO($dsn, $dbUser, $dbPass);
            $this->isConnected = true;
        } catch (\PDOException $e) {
            $this->getLog()->addError($e->getMessage(), $e->getCode());
        }

        $this->setDefaultFetchMode();

        return $this;
    }

    /**
     * @return $this
     */
    public function enableDebug()
    {
        $this->getLog()->enableDebug();

        return $this;
    }

    /**
     * @param string $dbHost
     * @param string $dbName
     * @param string $dbDriver
     *
     * @return string
     */
    protected function getPdoDsn($dbHost, $dbName, $dbDriver)
    {
        return sprintf('%s:host=%s;dbname=%s', $dbDriver, $dbHost, $dbName);
    }

    /**
     * @param string $query
     *
     * @return \PDOStatement
     */
    protected function execute($query)
    {
        if ($this->isConnected !== true) {
            return new ErrorConnection();
        }

        $stmt = $this->con->prepare($query);
        $stmt->execute();

        $this->getLog()->log($stmt);

        return $stmt;
    }

    /**
     * @param string $tableName
     * @param array $keyValues
     * @param bool $forceIgnore
     *
     * @return bool|string
     */
    public function insert($tableName, array $keyValues, $forceIgnore = false)
    {
        if ($this->isConnected !== true) {
            return false;
        }

        $parameters = [];
        foreach ($keyValues as $column => $value) {
            $parameters[] = sprintf('%s = \'%s\'', $column, $value);
        }

        $query = sprintf(
            'INSERT%sINTO %s SET %s',
            ($forceIgnore === true) ? ' IGNORE ' : ' ',
            $tableName,
            implode(', ', $parameters)
        );
        $this->execute($query);

        return $this->con->lastInsertId();
    }

    /**
     * @param string $tableName
     * @param array $keyValues
     * @param string|int $conditionValue
     * @param string $columnName
     *
     * @return int
     */
    public function update($tableName, array $keyValues, $conditionValue, $columnName = 'id')
    {
        if ($this->isConnected !== true) {
            return false;
        }

        $parameters = [];
        foreach ($keyValues as $column => $value) {
            $parameters[] = sprintf('%s = \'%s\'', $column, $value);
        }

        $query = sprintf('UPDATE %s SET %s WHERE %s = \'%s\'', $tableName, implode(', ', $parameters), $columnName, $conditionValue);

        $stmt = $this->execute($query);

        return $stmt->rowCount();
    }

    /**
     * @param string $query
     *
     * @return array
     */
    public function getPairs($query)
    {
        $dataResult = $this->execute($query)->fetchAll(\PDO::FETCH_NUM);

        $pairs = [];
        foreach ($dataResult as $item) {
            $pairs[$item[0]] = $item[1];
        }

        return $pairs;
    }

    /**
     * @param string $query
     *
     * @return string
     */
    public function getValue($query)
    {
        return $this->execute($query)->fetch(\PDO::FETCH_NUM)[0];
    }

    /**
     * @param string $query
     *
     * @return string
     */
    public function getRow($query)
    {
        return $this->execute($query)->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * @param string $query
     *
     * @return \PDOStatement
     */
    public function query($query)
    {
        if ($this->isConnected !== true) {
            return new ErrorConnection();
        }

        return $this->execute($query);
    }

    /**
     * @return KisdbLogger
     */
    public function getLog()
    {
        return KisdbLogger::getInstance();
    }

    /**
     * @param int $fetchMode
     *
     * @return $this
     */
    protected function setDefaultFetchMode($fetchMode = \PDO::FETCH_ASSOC)
    {
        if ($this->isConnected === true) {
            $this->con->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, $fetchMode);
        }

        return $this;
    }
}
