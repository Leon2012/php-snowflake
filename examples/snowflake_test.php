<?php
/**
 * @authors Leon Peng (leon.peng@live.com)
 * @date    2016-08-24 17:36:04
 *
 * @version $Id$
 */
require 'vendor/autoload.php';
use \Leon2012\Snowflake\Snowflake;

$snowflake = new Snowflake(1);
$st = microtime(true);

for ($i = 0; $i < 10000; ++$i) {
    $id = $snowflake->next();
    echo "{$id}\n";
}
echo 'time:'.(microtime(true) - $st)."\n";
