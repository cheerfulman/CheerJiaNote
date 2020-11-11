## pdo

大致看了一下php的`pdo`，发现跟`Java`的`JDBC`差不多；

**前提**

**首先我创建了一个数据库 --- test , 然后创建了一个foo;**

```php
<?php
// 相当于JDBC链接驱动
// url -- mysql:host = ?;dbname = ?;
// conn = DriverManager.getConnection(URL,USER,PASS); -- java
$dbms = "mysql";
$host = "localhost";
$dbName = "test";
$user = "root";
$pass = "password";
$dsn = "$dbms:host=$host;dbname=$dbName";

try{
    // 创建一个PDO类 相当于JDBC connection类
    $dbh = new PDO($dsn,$user,$pass);
    echo "连接成功<br>";
    // 写一个要执行的语句
    $sql_insert = "insert into foo(name ,age) values ('lomont',18)";
    $sql_query = "select name, age from foo";

    // 通过 PDO执行，并且返回一个结果集，该结果集是一个PDOStatement对象 JAVA中是resultSet对象
    $result = $dbh->query($sql_query);
    // 循环这个数组 输出
    foreach ($result as $row){
        printf("%s %s<br>",$row['name'],$row['age']);
    }
    
    $dbh = null;
}catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
}
?>
输出：
lomont 18
```

> 增删查改

```php
<?php
// 相当于JDBC链接驱动
// url -- mysql:host = ?;dbname = ?;
// conn = DriverManager.getConnection(URL,USER,PASS); -- java
$dbms = "mysql";
$host = "localhost";
$dbName = "test";
$user = "root";
$pass = "password";
$dsn = "$dbms:host=$host;dbname=$dbName";

$dbh = null;
$sql_insert = "insert into foo(name ,age) values ('lomont',18)"; // 增
$sql_delete = "delete from foo where name = 'lomont'"; // 删
$sql_query = "select name, age from foo"; // 查
$sql_update = "update foo set age = 20 where name = 'lomont'"; // 改
try{
    // 创建一个PDO类 相当于JDBC connection类
    $dbh = new PDO($dsn,$user,$pass);
    echo "连接成功<br>";
}catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
}


function insert($sql,$dbh){
    echo "轮到insert了<br>";
    var_dump($sql);
    var_dump($dbh);

    $result = $dbh->exec($sql);

    if($result) echo "插入成功<br>";
    else echo "插入失败<br>";
}

function delete($sql){
    global $dbh;
    $dbh->exec($sql);
}

function query($sql,$dbh){
    echo "query ... <br>";
    //     循环这个数组 输出
    foreach ($dbh->query($sql) as $row){
//        printf("%s %s<br>",$row['name'],$row['age']);
        print_r($row);
    }
}

function update($sql){
    global $dbh;
    $dbh->exec($sql);
}

insert($sql_insert,$dbh);
update($sql_update,$dbh);
delete($sql_delete);
query($sql_query,$dbh);
?>
```

