## SPL

SPL是Standard PHP Library（PHP标准库）

### 双向链表 - SplDoublyLinkedList

双向链表不多说了，直接看API；

+ top(); -- 相当于获得尾部 getLast()
+ Bootom()； --- 链表首部  element()
+ unshift(): ---往首部添加元素 addFirst()
+ shift() : -- 删除链表头结点  removeFirst()
+ Rewind() : 将结点指向链表首部
+ current(): 获得当前链表节点所在的元素，使用前先Rewind(),如果节点指向被删除了，则指向空；

+ next(): 指针指向下一个
+ prev()：前一个

```php
<?php
$q = new SplDoublyLinkedList();
$q->push(1);
$q->push(2);
$q->push(3);
$q->shift();
$q->unshift(123);
$q->rewind();

for($q->rewind(); $q->valid(); $q->next()){
    echo $q->current() . "<br>";
}
echo $q->current() . "<br>";
echo $q->bottom() . "<br>";
echo $q->top() . "<br>";
print_r($q);
?>
```

### 栈 - SplStack

继承自SplDoublyLinkedList类的SplStack类

- `push`:压入堆栈（存入）
- `pop`:退出堆栈（取出）
- `top`:返回栈顶
- `rewind()` 指向栈顶

```php
<?php
$stack = new SplStack();
$stack->push(123);
$stack->push("II");
$stack->push("I123I");

echo $stack->count();
for($i = 0; $i < $stack->count(); $i ++){
    echo $stack->offsetGet($i) . "\n";
}

echo $stack->offsetGet(0);
echo $stack->offsetGet(1);

echo $stack->pop() ."\n";
print_r($stack);
?>
输出：
3I123I
II
123
I123IIII123I
SplStack Object
(
    [flags:SplDoublyLinkedList:private] => 6
    [dllist:SplDoublyLinkedList:private] => Array
        (
            [0] => 123
            [1] => II
        )

)
```

### 队列 - SqlQueue

- `enqueue`:进入队列
- `dequeue`:退出队列
- `offSet(0)`: 是`Bottom`所在的位置
- `rewind`: 操作使得指针指向`Bottom`（首）所在的位置的节点
- `next`: 操作使得当前指针指向`Top`方向(尾)的下一个节点

```php
<?php
$q = new SplQueue();
$q->enqueue("asdf");
$q->enqueue("asd");
$q->enqueue("1");

echo $q->top() . "\n";
foreach ($q as $value){
    echo $value . "\n";
}
print_r($q);
?>
输出：
1
asdf
asd
1
SplQueue Object
(
    [flags:SplDoublyLinkedList:private] => 4
    [dllist:SplDoublyLinkedList:private] => Array
        (
            [0] => asdf
            [1] => asd
            [2] => 1
        )

)
```

### 迭代器

#### ArrayIterator迭代器

- `seek()`，指针定位到某个位置，很实用，跳过前面`n-1`的元素
- `ksort()`，对`key`进行字典序排序
- `asort()`，对`值`进行字典序排序

```php
<?php
$arr=array(
    'apple' => 'apple value',
    'orange' => 'orange value',
    'grape' => 'grape value',
    'plum' => 'plum value'
);
$a = new ArrayObject($arr);
$it = $a->getIterator(); // 生成数组迭代器
foreach ($it as $key => $value){
    echo $key . ": " . $value . "\n";
}

print "\n\n";

$it->rewind();
while($it->valid()){
    $it->seek(1); //position，跳过前面 n-1的元素
    while($it->valid()){
        echo $it->key().' : '.$it->current()."\n";
        $it->next();
    }
}
print "\n\n";

$it->ksort();//对key进行字典序排序
//$it->asort();//对值进行字典序排序
foreach ($it as $key => $value){
    echo $key . ": " . $value . "\n";
}
?>
输出：
apple: apple value
orange: orange value
grape: grape value
plum: plum value


orange : orange value
grape : grape value
plum : plum value


apple: apple value
grape: grape value
orange: orange value
plum: plum value
```

#### Appenditerator迭代器

可以将几个迭代器一起遍历

```php
<?php

$arr=array(
    'apple' => 'apple value',
    'orange' => 'orange value',
    'grape' => 'grape value',
    'plum' => 'plum value'
);

$arr_a = new ArrayIterator($arr);
$arr_b = new ArrayIterator(array('b',1,23,423,2345,234,34,2));
$it = new AppendIterator();
$it->append($arr_a);
$it->append($arr_b);

foreach ($it as $key => $value){
    print_r($value . "\n");
}
?>

输出：
apple value
orange value
grape value
plum value
b
1
23
423
2345
234
34
2
```

#### MultipleIterator迭代器

用于把多个`Iterator`里面的数据组合成为**一个整体**来访问

- `Multipleiterator`将多个`arrayiterator`**拼凑起来**
- `Appenditerator`将多个`arrayiteratorr`**连接起来**

```php
<?php
$idIter = new ArrayIterator(array(1,2,3));
$nameIter = new ArrayIterator(array("小明","小红","小丽","1"));
$ageIter = new ArrayIterator(array(18,30,17));

$mutiIter = new MultipleIterator();
$mutiIter->attachIterator($idIter);
$mutiIter->attachIterator($nameIter);
$mutiIter->attachIterator($ageIter);

foreach ($mutiIter as $value){
    print_r($value);
}
?>
输出：
Array
(
    [0] => 1
    [1] => 小明
    [2] => 18
)
Array
(
    [0] => 2
    [1] => 小红
    [2] => 30
)
Array
(
    [0] => 3
    [1] => 小丽
    [2] => 17
)
```

