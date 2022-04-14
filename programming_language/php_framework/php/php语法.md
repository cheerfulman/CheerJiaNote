## 语法和命名空间

> 此前，先mark个常用的配置php时使用的函数phpinfo();
>
> 此函数可以查看php的版本等各种详细的信息，配置扩展时必用；

<font size = "5" color = "red">php是世界上最好的语言！</font>

### php语法

```text
php以<?php开始 以?>结束
居然可以镶嵌到html中，果然是世界上最好的语言
```

```php
<!DOCTYPE html>
<html>
<body>
<h1>我的第一张 PHP 页面</h1>

<?php
echo "php是世界上最好的语言！"
?>

</body>
</html>
```

+ 注释与其他语言相同
+ 变量对大小写敏感

### 变量

- 变量以 $ 符号开始，后面跟着变量的名称
- 变量名必须以字母或者下划线字符开始
- 变量名只能包含字母数字字符以及下划线（A-z、0-9 和 _ ）
- 变量名不能包含空格

**PHP 是一门弱类型语言**

> php变量赋值没有什么规范，直接$开头即可，后面可以接任何类型

```php
<?php
$x = 5;
$y = 7.500;
$str = "富途真棒!";
echo $x;
echo $y;
echo $str;
?>
```

### 变量作用域

- local
- global
- static
- parameter

> 1 . 在变量前添加关键字global可以变成全局变量
>
> 2.PHP 将所有全局变量存储在一个名为 $GLOBALS[*index*] 的数组中。 *index* 保存变量的名称。这个数组可以在函数内部访问，也可以直接用来更新全局变量。

```php
<?php
$x = 5;
$y = 6;

function test(){
    $GLOBALS['y'] = $GLOBALS['x'] + $GLOBALS['y'];
    ECHO $GLOBALS['y'];
    ECHO "<br>";
}
test();
echo $x;
echo "<br>";
echo $y;
?>
```

#### Static 作用域

> 当一个函数完成时，它的所有变量通常都会被删除
>
> 如果希望某个局部变量不被删除时，添加static关键字

**当不添加static时**：

```php
<?php
$x = 5;

function test(){
    $x = 0;
    $x ++;
    echo $x;
}

test();
test();
test();
echo $x;

?>
    
输出：1115
```

**添加static时**：

```php
<?php
$x = 5;

function test(){
    static $x = 0;
    $x ++;
    echo $x;
}

test();
test();
test();
echo $x;

?>
输出：1235
```

然后，每次调用该函数时，该变量将会保留着函数前一次被调用时的值。

**注释：**该变量仍然是函数的局部变量。

### echo和print

#### echo

echo 和 print 区别:

- echo - 可以输出一个或多个字符串
- print - 只允许输出一个字符串，返回值总为 1

下面演示echo如何输出变量和字符串：

```php
<?php
$str = "Good Good Very Good";
$str1 = "up up day day up";
$Game = array("leetcode","codeforces","luogu");

echo $str,"asdfsdaf";
echo "<br>";
echo "I want $str1";
echo "<br>";
echo "{$Game[0]}";

?>
输出:
Good Good Very Goodasdfsdaf
I want up up day day up
leetcode
```

#### print

下面演示print如何输出变量和字符串：

```php
<?php
$str = "Good Good Very Good";
$str1 = "up up day day up";
$Game = array("leetcode","codeforces","luogu");

print $str;
print "<br>";
print "I want $str1";
print "<br>";
print print "{$Game[0]}";

?>
输出：
Good Good Very Good
I want up up day day up
leetcode1
```

#### 小结

+ echo可以`echo $str,"asdfsdaf";`输出多个字符串
+ print可以`print print "{$Game[0]}";`有返回值

### 数据类型

> php数据类型总共有如下几种

+ String（字符串）
+  Integer（整型）
+ Float（浮点型）
+ Boolean（布尔型）
+ Array（数组）
+ Object（对象）
+  NULL（空值）

> 使用var_dump()函数可以返回变量数据类型和值

示例代码：

```php
<?php

$x = 1;
$y = 1.13;
$z = 2.4e5;

echo var_dump($x);
echo var_dump($y);
echo var_dump($z);

?>
输出：
D:\Wnmp\html\work\practice\phpinfo.php:7:int 1
D:\Wnmp\html\work\practice\phpinfo.php:8:float 1.13
D:\Wnmp\html\work\practice\phpinfo.php:9:float 240000 
```

#### 数组

>  数组使用array()定义：

```php
<?php

$cars = array('benchi','baoma');
echo $cars[0];
var_dump($cars)

?>
输出：
benchi
D:\Wnmp\html\work\practice\phpinfo.php:5:
array (size=2)
  0 => string 'benchi' (length=6)
  1 => string 'baoma' (length=5)
```

#### 对象

> 使用class声明对象

```php
class Car{
    var $name;
    var $prince;
    function Car($name,$prince){
        $this->name = $name;
        $this->prince = $prince;
    }
    function setPrince($prince){
        $this->prince = $prince;
    }

    function getCar(){
        return array("name" => $this->name,
                    "prince" => $this->prince
            );
    }
}
```

顺带一提：`get_object_vars`将其转化为关联数组比如getCar()；

#### null值

可以通过设置null来清空数据，与其他语言好像相似；

### 常量

设置常量，使用 define() 函数，函数语法如下：

`bool define ( string $name , mixed $value [, bool $case_insensitive = false ] )`

- **name：**必选参数，常量名称，即标志符。
- **value：**必选参数，常量的值。
- **case_insensitive** ：可选参数，如果设置为 TRUE，该常量则大小写不敏感。默认是大小写敏感的。

> 示例代码

```php
<?php
// 不区分大小写的常量名
define("car","Cars");
function test(){
    echo car;
}
test();
echo car;
?>
输出：
CarsCars
```

#### 小结：

+ 常量是全局的，默认是全局变量

### 字符串

#### PHP 并置运算符

在 PHP 中，只有一个字符串运算符。

并置运算符 (.) 用于把两个字符串值连接起来 === 其它语言的 + 。

：

```php
<?php
// 不区分大小写的常量名
define("car","Cars");
$t = "my car is";
echo $t . " " . car;
echo car;
?>
输出：
my car is CarsCars
```

#### String API

+ strlen(): 返回字符串长度
+ strpos(): 函数用于在字符串内查找一个字符或一段指定的文本。

示例:

```php
<?php
// 不区分大小写的常量名
define("car","Cars");

echo strpos(car,"C");
?>
输出:0
```

更多String API参考手册：https://www.php.cn/php/php-ref-string.html

#### 其它API

+ intdiv(x1,x2) ： x1整除x2；

  示例代码：

  ```php
  <?php
  
  function toBinaryNum($x){
      if(intdiv($x,2) > 0)
          toBinaryNum(intdiv($x,2));
      echo $x % 2;
  }
  
  toBinaryNum(578);
  ?>
  输出：
  1001000010
  ```

### 其它

  php的**if-else**语句与其它语句相同，示例：

  ```php
  <?php
  
  $t = 100;
  
  if ($t < 10) {
      echo "Have a good morning!";
  } elseif ($t < 20) {
      echo "Have a good day!";
  } else {
      echo "Have a good night!";
  }
  
  ?>
  ```

  **switch-case**语句：

  ```php
  <?php
  $t = 1;
  
  switch ($t){
      case 1:echo "Have a good morning!";break;
      case 2:echo "Have a good day!";break;
      default: echo "Have a good night!";
  }
  
  ?>
  ```

  while、for与其它语言无差别，我们看看foreach：

```php
<?php

$colors = array("red","green","blue","yellow");

foreach ($colors as $color) {
    echo "$color <br>";
}
?>
输出：
red
green
blue
yellow
```

php中函数的用法：

```php
<?php
function writeMsg() {
  echo "Hello world!";
}

writeMsg(); // 调用函数
?>

// 含参数
<?php
function familyName($fname,$year) {
  echo "$fname Zhang. Born in $year <br>";
}

familyName("Li","1975");
familyName("Hong","1978");
familyName("Tao","1983");
?>

// 默认参数
<?php
function setHeight($minheight=50) {
  echo "The height is : $minheight <br>";
}

setHeight(350);
setHeight(); // 将使用默认值 50
setHeight(135);
setHeight(80);
?>

// 返回值
<?php
function sum($x,$y) {
  $z=$x+$y;
  return $z;
}

echo "5 + 10 = " . sum(5,10) . "<br>";
echo "7 + 13 = " . sum(7,13) . "<br>";
echo "2 + 4 = " . sum(2,4);
?>
```

在php中有三种数组类型：

+ 索引数组 - 普通数组
+ 关联数组 - map
+ 多维数组 - 二维数组

```php
<?php

// 索引数组
$cars=array("Volvo","BMW","SAAB");

echo count($cars) . "<br>";

for($i = 0; $i < count($cars); $i ++){
    echo $cars[$i] . "<br>";
}

// 关联数组
$age = array("a"=>30,"b"=>2,'c'=>12);

echo  " a is ". $age['a'] . " years old<br>";


foreach ($age as $a => $value){
    echo "key = " . $a . " value = " . $value . "<br>";
}
?>
```

> 多维数组

```php
$sites = array
(
    "runoob"=>array
    (
        "菜鸟教程",
        "http://www.runoob.com"
    ),
    "google"=>array
    (
        "Google 搜索",
        "http://www.google.com"
    ),
    "taobao"=>array
    (
        "淘宝",
        "http://www.taobao.com"
    )
);

print_r($sites)
```

> 一些数组API

```php
sort() - 以升序对数组排序
rsort() - 以降序对数组排序
asort() - 根据值，以升序对关联数组进行排序
ksort() - 根据键，以升序对关联数组进行排序
arsort() - 根据值，以降序对关联数组进行排序
krsort() - 根据键，以降序对关联数组进行排序
```

```php
<?php

$age=array("Bill"=>"35","Steve"=>"37","Peter"=>"43");
asort($age);
foreach($age as $x=>$x_value) {
    echo "Key=" . $x . ", Value=" . $x_value;
    echo "<br>";
}

?>

<?php
$age=array("Bill"=>"35","Steve"=>"37","Peter"=>"43");
ksort($age);
foreach($age as $x=>$x_value) {
    echo "Key=" . $x . ", Value=" . $x_value;
    echo "<br>";
}

?>
```

对象相关；

如继承 ---> extends；

构造方法 ---> __construct() or 类名()

访问控制：public、protected、private

接口 ---> interface

实现 ---> implements

调用父类 ---> parent::  

抽象类 ---> abstract

静态 --> static

不可变 ---> final

### 超级全局变量

#### $GLOBALS

用法前面讲过：

```php
$x = 70;
function t(){
    $GLOBALS['x'] = 2;
}
```

#### $_SERVER

$_SERVER是一个包含头信息(header)、路径(path)、以及脚本位置(script locations)等等信息的数组。这个数组中的项目由 Web 服务器创建。不能保证每个服务器都提供全部项目；

```php
<?php 
echo $_SERVER['PHP_SELF'];
echo "<br>";
echo $_SERVER['SERVER_NAME'];
echo "<br>";
echo $_SERVER['HTTP_HOST'];
echo "<br>";
echo $_SERVER['HTTP_REFERER'];
echo "<br>";
echo $_SERVER['HTTP_USER_AGENT'];
echo "<br>";
echo $_SERVER['SCRIPT_NAME'];
?>
```

#### $_REQUEST

$_REQUEST 用于收集HTML表单提交的数据。

```php
<html>
<body>

<form method="post" action="<?php echo $_SERVER['PHP_SELF']?>">
    Name: <input type="text" name="fname">
    <input type="submit">
</form>

<?php
$name = $_REQUEST['fname'];
echo $name;

?>

</body>
</html>
```

####  $_POST

PHP $_POST 被广泛应用于收集表单数据,在HTML form标签的指定该属性："method="post"。

```php
<html>
<body>

<form method="post" action="<?php echo $_SERVER['PHP_SELF']?>">
    Name: <input type="text" name="fname">
    <input type="submit">
</form>

<?php
$name = $_POST['fname'];
echo $name;

?>

</body>
</html>
```

#### $_GET

PHP $_GET 同样被广泛应用于收集表单数据，在HTML form标签的指定该属性："method="get",也可以收集URL中发送的数据。

> 比如我们url中添加参数a=100&b=1000则可以用$_GET取出

```php
<html>
<body>
<a href="phpinfo.php?a=100&b=10000">Test $GET</a>
</body>
</html>
    
phpinfo.php
<?php echo "a ".$_GET['a'] . " b " .$_GET['b'];?>
```

### 魔术常量

+ `__LINE__`: 返回当前行号

  ```php
  <?php
  echo '这是第 “ '  . __LINE__ . ' ” 行';
  ?>
  ```

+ `__FILE__` ：文件完整路径和文件名

  ```php
  echo "该文件位于" . __FILE__ ;
  ```

+ `__DIR__` :文件所在的目录

  ```php
  echo "该文件位于" . __DIR__ ."目录<br>";
  ```

+ `__function__`: 返回函数被定义时的名字

  ```php
  function name(){
      echo  "该函数名字为" . __FUNCTION__ ."<br>";
  }
  name();
  ```

+ `__CLASS__`:返回该类被定义时的名字

  ```php
  class test {
      function _print() {
          echo '类名为：'  . __CLASS__ . "<br>";
          echo  '函数名为：' . __FUNCTION__ ;
      }
  }
  $t = new test();
  $t->_print();
  ```

+ `——TRAIT`:是php实现代码复用的方法称为`trait`

+ `__METHOD__` : 方法被定义时的名字;

+ `__NAMESPACE__`：命名空间的名称；

### 命名空间

命名空间：解决重名问题，约定一个前缀；

+ 解决命名重读
+ 提高代码可读性（通过namespace创建一个简短的名称）

### 魔术方法

+ `__construct()` 实例化对象时被调用，当__construct和以类名为函数名的函数同时存在时，__construct将被调用，另一个不被调用。

