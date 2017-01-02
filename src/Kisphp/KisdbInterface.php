<?php

namespace Kisphp;

interface KisdbInterface
{
    /**
     * @param string $dbHost
     * @param string $dbUser
     * @param string $dbPass
     * @param string $dbName
     * @param string $dbDriver
     *
     * @return mixed
     */
    public function connect($dbHost = 'localhost', $dbUser = 'root', $dbPass = '', $dbName = 'test', $dbDriver = 'mysql');

    /**
     * @param $tableName
     * @param array $keyValues
     *
     * @return mixed
     */
    public function insert($tableName, array $keyValues);

    /**
     * @param $tableName
     * @param array $keyValues
     * @param $conditionValue
     * @param string $columnName
     *
     * @return mixed
     */
    public function update($tableName, array $keyValues, $conditionValue, $columnName = 'id');

    /**
     * @param string $query
     *
     * @return mixed
     */
    public function getPairs($query);

    /**
     * @param string $query
     *
     * @return mixed
     */
    public function getRow($query);

    /**
     * @param string $query
     *
     * @return mixed
     */
    public function query($query);
}
