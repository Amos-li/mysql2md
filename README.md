# MySQL 数据字典转 Markdown 格式

## 配置说明

```php
// 必须参数[$host, $username, $password, $dbname]
$host = 'localhost';
$username = 'user';
$password = 'pass';
$dbname = 'mysql';

// 可选参数[$port, $table, $charset, $filename]
$port = 3306;           // 默认为 3306
$table = 'servers';     // 表名，默认为[null-打印全部表]
$charset = 'utf8mb4';   // 字符集，默认为[utf8mb4]
$filename = 'mysql';    // 不带后缀，默认为 数据库名[$dbname]
```

## 运行说明

```shell
$ php ./mysql2md.php
```

## 结果示例

servers -- MySQL Foreign Servers table

| 字段 | 类型 | 空 | 默认值 | 注释 |
| :- | :- | :- | :- | :- |
| Server_name | char(64) | 否 |  |  |
| Host | char(64) | 否 |  |  |
| Db | char(64) | 否 |  |  |
| Username | char(64) | 否 |  |  |
| Password | char(64) | 否 |  |  |
| Port | int(4) | 否 | 0 |  |
| Socket | char(64) | 否 |  |  |
| Wrapper | char(64) | 否 |  |  |
| Owner | char(64) | 否 |  |  |
