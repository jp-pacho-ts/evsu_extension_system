<?php
class Database
{
    function connect()
    {
        $c = new mysqli(
            'sql308.infinityfree.com',
            'if0_42181193',
            'I6ihUSHujmm',
            'if0_42181193_extension_evsu',
            3306
        );

        if ($c->connect_error) {
            die('Database connection failed: ' . $c->connect_error);
        }

        $c->set_charset('utf8mb4');

        return $c;
    }
}
?>
