# Simple Database connection using PDO

## Installation

Run in terminal

```sh
composer require kisphp/database:~1.0
```

Then in php file add 

```php
<?php

require 'vendor/autoload.php';

```

## Connect to database

```php
<?php

use Kisphp\Kisdb;

$db = Kisdb::getInstance();
$db->connect(
    $databaseHost,      // localhost
    $databaseUsername,  // root
    $databasePassword,  // {blank}
    $databaseName       // test
);
```


## Database Insert

> `$db->insert('table_name', 'data array');`

If you need `INSERT IGNORE` syntax, then pass `true` for the third parameter

```php
$db = Kisdb::getInstance();

$db->insert('test_table', [
    'column_1' => 'value_1',
    'column_2' => 'value_2',
]);

// will return last_insert_id

$insertIgnore = true;
$db->insert(
    'test_table',
    [
        'column_1' => 'value_1',
        'column_2' => 'value_2',
    ],
    $insertIgnore
);
// will execute INSERT IGNORE ...

```

## Database update

> `$db->update('table_name', 'data array', 'condition value', 'column name (default=id)');`

```php

$db = Kisdb::getInstance();

$db->update('test_table', [
    'column_1' => 'value_1',
    'column_2' => 'value_2',
], 20);

// will return affected_rows

```


## Get single value

```php
$db = Kisdb::getInstance();

$value = $db->getValue("SELECT column_1 FROM test_table");
```

## Get pairs 

```php
$db = Kisdb::getInstance();

$pairs = $db->getPairs("SELECT id, column_1 FROM test_table");

/*
will result
$pairs = [
     '1' => 'c1.1',
     '2' => 'c2.1',
     '3' => 'c3.1',
];
*/
```

## Get Custom query
 

```php
$db = Kisdb::getInstance();

$query = $db->query("SELECT * FROM test_table ");

while ($item = $query->fetch(\PDO::FETCH_ASSOC)) {
    var_dump($item);
}
```
