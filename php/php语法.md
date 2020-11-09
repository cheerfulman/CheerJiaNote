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

  

