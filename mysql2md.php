<?php
    $host = 'localhost';
    $port = 3306;
    $dbname = 'mysql';
    $table = 'servers';              // [null|tablename]null-打印全部表
    $username = 'user';
    $password = 'pass';
    $charset = 'utf8mb4';
    $filename = 'mysql.md';

    $mysqli = new mysqli($host, $username, $password, $dbname, $port);
    $mysqli->set_charset($charset);
    $result = $mysqli->query('SHOW TABLES;');
    $items = $result ? $result->fetch_all() : [];
    $tableItems = [];
    if ($items) foreach ($items as $item) {
        if (is_null($table)) {
            isset($item['0']) && $tableItems[] = $item['0'];
        } else {
            isset($item['0']) && $item['0'] == $table && $tableItems[] = $item['0'];
        }
    }

    if ($tableItems) {
        $string = '';
        foreach ($tableItems as $table) {
            $result = $mysqli->query('SHOW table status like \''. $table .'\'');
            $tableInfo = $result->fetch_assoc();
            $string .= $tableInfo['Name'] . ($tableInfo['Comment'] ? (' -- '. $tableInfo['Comment']) : '')."\n\n";
            $string .= '| 字段 | 类型 | 空 | 默认值 | 注释 |'. "\n";
            $string .= "| :- | :- | :- | :- | :- |\n";
            $result = $mysqli->query('SHOW FULL COLUMNS FROM '. $table);
            if ($fields = $result->fetch_all()) foreach ($fields as $field) {
                $string .= '| '. $field['0'] .' | '. $field['1'] .' | '. ($field['3'] === 'NO' ? '否' : '是') .' | '. $field['5'] .' | '. $field['8'] ." |\n";
            }
            $string .= "\n";
        }
        file_put_contents(__DIR__ . DIRECTORY_SEPARATOR . $filename, $string);
        echo "导出成功，共导出 ". count($tableItems) ." 个表结构！\n";
    } else {
        echo "数据表不存在！\n";
    }
    $mysqli->close();
