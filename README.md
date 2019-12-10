# MySQL 数据字典转 Markdown 格式

## 配置说明

```php
$host = 'localhost';          // 服务地址
$port = 3306;                 // 端口
$dbname = 'mysql';            // 数据库名
$table = 'servers';           // 表名[null|tablename]null-打印数据库全部表
$username = 'user';           // 用户
$password = 'pass';           // 密码
$charset = 'utf8mb4';         // 字符集
$filename = 'mysql.md';       // 导出文件名（文件路径为当前文件目录）
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
