<?php

try {
    /**
     * 必须参数[$host, $username, $password, $dbname]
     */
    $host = 'localhost';
    $username = 'user';
    $password = 'pass';
    $dbname = 'mysql';

    /**
     * 可选参数[$port, $table, $charset, $filename]
     */
    // $port = 3306;           // 默认为 3306
    $table = 'servers';     // 表名，默认为[null-打印全部表]
    // $charset = 'utf8mb4';   // 字符集，默认为[utf8mb4]
    // $filename = 'mysql';    // 不带后缀，默认为 数据库名[$dbname]

    $mysqlToMd = new MysqlToMd($host, $username, $password, $dbname);

    // $mysqlToMd->setPort($port);
    $mysqlToMd->setTable($table);
    // $mysqlToMd->setCharset($charset);
    // $mysqlToMd->setFilename($filename);

    if ($row = $mysqlToMd->writeToMd()) {
        echo "导出成功，共导出 ". $row ." 个表！\n";
    } else {
        echo "导出失败，表不存在！\n";
    }
} catch (\Exception $e) {
    echo $e->getMessage() ."\n";
}

/**
 *
 * @author zxf
 * @date    2019年12月11日
 */
class MysqlToMd
{
    /**
     *
     * @var string
     */
    protected $host;
    /**
     *
     * @var string
     */
    protected $username;
    /**
     *
     * @var string
     */
    protected $password;
    /**
     *
     * @var string
     */
    protected $dbname;
    /**
     *
     * @var int
     */
    protected $port = 3306;
    /**
     *
     * @var string
     */
    protected $charset = 'utf8mb4';
    /**
     *
     * @var string
     */
    protected $table;
    /**
     *
     * @var string
     */
    protected $filename;

    /**
     *
     * @var mysqli
     */
    protected $mysqli;

    /**
     *
     * @author zxf
     * @date    2019年12月11日
     * @param  string $host
     * @param  string $username
     * @param  string $password
     * @param  string $dbname
     */
    public function __construct(string $host, string $username, string $password, string $dbname)
    {
        $this->setHost($host)->setUsername($username)->setPassword($password)->setDbname($dbname);
        $this->mysqli = new mysqli($this->getHost(), $this->getUsername(), $this->getPassword(), $this->getDbname(), $this->getPort());
    }

    /**
     *
     * @author zxf
     * @date    2019年12月11日
     * @throws \Exception
     * @throws Exception
     * @return number
     */
    public function writeToMd()
    {
        try {
            $this->getMysqli()->set_charset($this->getCharset());
            $result = $this->getMysqli()->query('SHOW TABLES;');
            $items = $result ? $result->fetch_all() : [];
            $tableItems = [];
            if ($items) foreach ($items as $item) {
                if (is_null($this->getTable())) {
                    isset($item['0']) && $tableItems[] = $item['0'];
                } else {
                    isset($item['0']) && $item['0'] == $this->getTable() && $tableItems[] = $item['0'];
                }
            }

            if ($tableItems) {
                $string = '';
                foreach ($tableItems as $table) {
                    $result = $this->getMysqli()->query('SHOW table status like \''. $table .'\'');
                    $tableInfo = $result->fetch_assoc();
                    $string .= $tableInfo['Name'] . ($tableInfo['Comment'] ? (' -- '. $tableInfo['Comment']) : '')."\n\n";
                    $string .= '| 字段 | 类型 | 空 | 默认值 | 注释 |'. "\n";
                    $string .= "| :- | :- | :- | :- | :- |\n";
                    $result = $this->getMysqli()->query('SHOW FULL COLUMNS FROM '. $table);
                    if ($fields = $result->fetch_all()) foreach ($fields as $field) {
                        $string .= '| '. $field['0'] .' | '. $field['1'] .' | '. ($field['3'] === 'NO' ? '否' : '是') .' | '. $field['5'] .' | '. $field['8'] ." |\n";
                    }
                    $string .= "\n";
                }

                $this->getMysqli()->close();
                file_put_contents($this->getFilepath(), $string);
                return count($tableItems);
            } else {
                throw new \Exception('数据表不存在！');
            }
        } catch (\Exception $e) {
            $this->getMysqli()->close();
            throw $e;
        }
    }

    /**
     *
     * @author zxf
     * @date    2019年12月11日
     * @return mysqli
     */
    public function getMysqli()
    {
        return $this->mysqli;
    }

    /**
     *
     * @author zxf
     * @date    2019年12月11日
     * @param  string $host
     * @return MysqlToMd
     */
    public function setHost(string $host)
    {
        $this->host = $host;
        return $this;
    }

    /**
     *
     * @author zxf
     * @date    2019年12月11日
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     *
     * @author zxf
     * @date    2019年12月11日
     * @param  string $username
     * @return MysqlToMd
     */
    public function setUsername(string $username)
    {
        $this->username = $username;
        return $this;
    }

    /**
     *
     * @author zxf
     * @date    2019年12月11日
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     *
     * @author zxf
     * @date    2019年12月11日
     * @param  string $password
     * @return MysqlToMd
     */
    public function setPassword(string $password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     *
     * @author zxf
     * @date    2019年12月11日
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     *
     * @author zxf
     * @date    2019年12月11日
     * @param  string $dbname
     * @return MysqlToMd
     */
    public function setDbname(string $dbname)
    {
        $this->dbname = $dbname;
        return $this;
    }

    /**
     *
     * @author zxf
     * @date    2019年12月11日
     * @return string
     */
    public function getDbname()
    {
        return $this->dbname;
    }

    /**
     *
     * @author zxf
     * @date    2019年12月11日
     * @param  int $port
     * @return MysqlToMd
     */
    public function setPort(int $port)
    {
        $this->port = $port;
        return $this;
    }

    /**
     *
     * @author zxf
     * @date    2019年12月11日
     * @return string
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     *
     * @author zxf
     * @date    2019年12月11日
     * @param  string $charset
     * @return MysqlToMd
     */
    public function setCharset(string $charset)
    {
        $this->charset = $charset;
        return $this;
    }

    /**
     *
     * @author zxf
     * @date    2019年12月11日
     * @return string
     */
    public function getCharset()
    {
        return $this->charset;
    }

    /**
     *
     * @author zxf
     * @date    2019年12月11日
     * @param  string $table
     * @return MysqlToMd
     */
    public function setTable(string $table)
    {
        $this->table = $table;
        return $this;
    }

    /**
     *
     * @author zxf
     * @date    2019年12月11日
     * @return string
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     *
     * @author zxf
     * @date    2019年12月11日
     * @param  string $filename
     * @return MysqlToMd
     */
    public function setFilename(string $filename = null)
    {
        if (is_null($filename)) {
            $filename = $this->getDbname();
        }
        $this->filename = $filename;
        return $this;
    }

    /**
     *
     * @author zxf
     * @date    2019年12月11日
     * @return string
     */
    public function getFilename()
    {
        if (is_null($this->filename)) {
            $this->setFilename();
        }
        return $this->filename .'.md';
    }

    /**
     *
     * @author zxf
     * @date    2019年12月11日
     * @return string
     */
    public function getFilepath()
    {
        return __DIR__ . DIRECTORY_SEPARATOR . $this->getFilename();
    }
}
