<?php

namespace Kisphp;

use PDO;

class ErrorConnection extends \PDOStatement
{
    public function fetchObject($class_name = null, $ctor_args = null)
    {
        return false;
    }

    public function fetch($fetch_style = null, $cursor_orientation = PDO::FETCH_ORI_NEXT, $cursor_offset = 0)
    {
        return false;
    }

    public function fetchAll($fetch_style = null, $fetch_argument = null, $ctor_args = null)
    {
        return false;
    }
}
