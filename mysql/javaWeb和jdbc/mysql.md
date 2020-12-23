# MYSQL

表:定义数据在表中如何存储，存储怎样的数据，数据如何分解，各部分如何命名等等;

模式：<font color = red >描述表的这组信息就是模式;</font>

DBMS分为两类：基于共享文件的DBMS和基于客户机-服务器的DBMS。

## MYSQL语法：
---
distinct:去重，必须在所有列的前面，并且不能多个去重，否则无效；
```
    select distinct name, id from table
```

limit: 限制返回所匹配的行，limit 5 则返回5行，limit 5,5 == limit 5 offset 5,从第五行开始返回五行（第一行为0）;

语法
```
    select name from table limit 5;
    select name from table limit 3,5;
```
order by:按顺序排序（默认升序 Ascend）（降序Descend）;
语法：
```
    按姓名升序
    select name,id  from table order by name;
    先然姓名再按 id;
    select name,id from table roder by id, name;
    先按id降序，再按姓名升序;
    select name , id from table order id desc, name;
```

> null值：不管匹配某个值与不匹配某个值都不会返回null，除非判断是否为null时;

And和Or：
```
    select name , id from table where id == 3
    and where name = 'dc';

    select name , id from table where id == 3
    or where name = 'dc';
```

+ MYSQl中与众语言一样，AND的优先级更高;
```
    此语句-> 选择name为'dc' 并且 id = 1003 的 或者 id = 1002的所有;
    select name , id from table where id = 1002 or where id = 1003 and where name = 'dc';
```
- 如果看到这，你问我怎么解决？我只能告诉你 ---- ();

<font color = red face = "宋体" size = 3>*IN(  ) 括号内的项等同于 OR 但通常用IN代替长串的OR,不仅代码简短，效率更高;*</font>

NOT:对于某种命令取反，例如 NOT IN （）或者 NOT BETWEEN;

### 通配符
---
% : %表示任何字符出现的任意次数;<font color = red face = "宋体">**此时应用LIKE**</font>;
```
选择所有以d开头的名字
    select name from table where name like 'd%'
```
<font color = red face = "宋体">即便使用%也不能匹配出null;</font>
```
    select name from table where name like '%'
```

_ :匹配一个;

> 不要过度使用通配符，虽然很方便但是慢;

### 正则表达式
---
关键字REGEXP:
```
此时肯定会返回'dc'
    select name from table where name regexp 'c'
此时不会返回'dc' like一般要搭配通配符使用 % or _;
    select name from table where name like 'c'
```

快捷用法:
1. 正则表达式中的OR 用 |;
    ```
    返回匹配c或者d的
    select name from table where name regexp 'd|c';
    ```
2. 也可以匹配几个字符之一
    ```
    返回匹配dc或者zc的
    select name from table where name regexp '[dz]c';
    ```
3. 可用^否定一个集合[^123]除1，2，3以外的;
4. 用-省略即 [1-3]代表1，2，3;
5. 在正则中用'\\'表示转移 及可用'\\.'来匹配带.的;而不能直接用.来匹配;

元字符 | 说明 |
----- | ----- |
 \\\f | 换页 |
 \\\n | 换行 |
 \\\r | 回车 |
 \\\t | 指标 |
 \\\v | 纵向制表 |

 若要匹配上述的<font color = green>**元字符**</font>则要用（\\\\\t）三个斜杠

可用{n}限制个数,且 * ？ + 同理限制个数：和XML一样*表示任意次数，？表示0 or 1， + 表示 {1,};

```
连续四个数字的;[:digit:]表示数字;
select id from table where id regexp [[:digit:]]{4};
```

说到正则就必须用到定位符了,因为匹配为全文匹配：
元字符|说明|
|---|---|
^|开头
$|结尾
[[:<:]]|词的开始
[[:>:]]|词的结尾

### 聚合函数
---

函数|说明
---|---
avg()|返回某列的平均值
count()|返回某列行数
max()|返回某列的最大值
min()|返回某列的最小值
sum()|返回某列的和

avg():只用于单个列，且不计算为null的值;

组合聚集函数：
```
    select count(*) as num_items, min(price) as price_min
        max(price) as price_max, avg(price) as price_avg
        from table;
```

使用聚合函数一般更改其列名，使其易于观察；

### 分组数据
---

where 过滤的是分组，而不是行;

group by 语句：
1. group by 可以包含任意数目的列。
2. 如果在group by子句中 嵌套了分组，数据在最后规定的分组上进行汇总；
3. group by 子句列出的每个列 都必须是检索列 或者有效表达式（但不能是聚集函数) 如果在select中使用表达式，则在group by 子句也要使用表达式，不能使用别名
4. 除聚集函数外，select 每个列都必须在group by 子句中给出
5. 如果分组有NUll，则null将做一个分组返回
6. group by 在where之后 order by 之前

ROLLUP 语句:得到每个分组的总级别；
```
    select name,count(*) as nums from table group by name with rollup
```

> Having 和 where区别：
> where 在分组前过滤数据，having 在分组后进行过滤      

---
> 外键：外键为表中的一列，它包含另一个表的主键值，定义了两个表之间的关系；

内部联结：常规的联结等于 where table1.列名1 = table2.列名2 也是table1 inner join table2 on 列名1 = 列名2；

外部联结：left outer join on 或者 right outer join on

### 组合查询
---
使用union: 将两个select语句输出组合成单个查询的结果集；
> 并且自动合并相同的行；不合并的为union all

### 插入数据
---
insert:
1. 插入完整的行```insert into table values();```
2. # MYSQL

表:定义数据在表中如何存储，存储怎样的数据，数据如何分解，各部分如何命名等等;

模式：<font color = red >描述表的这组信息就是模式;</font>

DBMS分为两类：基于共享文件的DBMS和基于客户机-服务器的DBMS。

## MYSQL语法：
---
distinct:去重，必须在所有列的前面，并且不能多个去重，否则无效；
```
    select distinct name, id from table
```

limit: 限制返回所匹配的行，limit 5 则返回5行，limit 5,5 == limit 5 offset 5,从第五行开始返回五行（第一行为0）;

语法
```
    select name from table limit 5;
    select name from table limit 3,5;
```
order by:按顺序排序（默认升序 Ascend）（降序Descend）;
语法：
```
    按姓名升序
    select name,id  from table order by name;
    先然姓名再按 id;
    select name,id from table roder by id, name;
    先按id降序，再按姓名升序;
    select name , id from table order id desc, name;
```

> null值：不管匹配某个值与不匹配某个值都不会返回null，除非判断是否为null时;

And和Or：
```
    select name , id from table where id == 3
    and where name = 'dc';

    select name , id from table where id == 3
    or where name = 'dc';
```

+ MYSQl中与众语言一样，AND的优先级更高;
```
    此语句-> 选择name为'dc' 并且 id = 1003 的 或者 id = 1002的所有;
    select name , id from table where id = 1002 or where id = 1003 and where name = 'dc';
```
- 如果看到这，你问我怎么解决？我只能告诉你 ---- ();

<font color = red face = "宋体" size = 3>*IN(  ) 括号内的项等同于 OR 但通常用IN代替长串的OR,不仅代码简短，效率更高;*</font>

NOT:对于某种命令取反，例如 NOT IN （）或者 NOT BETWEEN;

### 通配符
---
% : %表示任何字符出现的任意次数;<font color = red face = "宋体">**此时应用LIKE**</font>;
```
选择所有以d开头的名字
    select name from table where name like 'd%'
```
<font color = red face = "宋体">即便使用%也不能匹配出null;</font>
```
    select name from table where name like '%'
```

_ :匹配一个;

> 不要过度使用通配符，虽然很方便但是慢;

### 正则表达式
---
关键字REGEXP:
```
此时肯定会返回'dc'
    select name from table where name regexp 'c'
此时不会返回'dc' like一般要搭配通配符使用 % or _;
    select name from table where name like 'c'
```

快捷用法:
1. 正则表达式中的OR 用 |;
    ```
    返回匹配c或者d的
    select name from table where name regexp 'd|c';
    ```
2. 也可以匹配几个字符之一
    ```
    返回匹配dc或者zc的
    select name from table where name regexp '[dz]c';
    ```
3. 可用^否定一个集合[^123]除1，2，3以外的;
4. 用-省略即 [1-3]代表1，2，3;
5. 在正则中用'\\'表示转移 及可用'\\.'来匹配带.的;而不能直接用.来匹配;

元字符 | 说明 |
----- | ----- |
 \\\f | 换页 |
 \\\n | 换行 |
 \\\r | 回车 |
 \\\t | 指标 |
 \\\v | 纵向制表 |

 若要匹配上述的<font color = green>**元字符**</font>则要用（\\\\\t）三个斜杠

可用{n}限制个数,且 * ？ + 同理限制个数：和XML一样*表示任意次数，？表示0 or 1， + 表示 {1,};

```
连续四个数字的;[:digit:]表示数字;
select id from table where id regexp [[:digit:]]{4};
```

说到正则就必须用到定位符了,因为匹配为全文匹配：
元字符|说明|
|---|---|
^|开头
$|结尾
[[:<:]]|词的开始
[[:>:]]|词的结尾

### 聚合函数
---

函数|说明
---|---
avg()|返回某列的平均值
count()|返回某列行数
max()|返回某列的最大值
min()|返回某列的最小值
sum()|返回某列的和

avg():只用于单个列，且不计算为null的值;

组合聚集函数：
```
    select count(*) as num_items, min(price) as price_min
        max(price) as price_max, avg(price) as price_avg
        from table;
```

使用聚合函数一般更改其列名，使其易于观察；

### 分组数据
---

where 过滤的是分组，而不是行;

group by 语句：
1. group by 可以包含任意数目的列。
2. 如果在group by子句中 嵌套了分组，数据在最后规定的分组上进行汇总；
3. group by 子句列出的每个列 都必须是检索列 或者有效表达式（但不能是聚集函数) 如果在select中使用表达式，则在group by 子句也要使用表达式，不能使用别名
4. 除聚集函数外，select 每个列都必须在group by 子句中给出
5. 如果分组有NUll，则null将做一个分组返回
6. group by 在where之后 order by 之前

ROLLUP 语句:得到每个分组的总级别；
```
    select name,count(*) as nums from table group by name with rollup
```

> Having 和 where区别：
> where 在分组前过滤数据，having 在分组后进行过滤

insert:插入多条语句，比同时使用多个Insert要快；

### 更新和删除
---
update:注意要使用where否则容易更新表中的所有行;
```
update table set 列 = (某值) where 限定条件
```

delete::注意要使用where否则容易删除表中的所有行;
```
delete from table where 限定条件
```
> delete删除的是表的内容而不是表，delete不能删除表本身;
> 想要删除表可以用truncate table，它是将原来的表删除，然后再创建一个新表

+ 在想进行删除delete或者更新update时，先用select看看where后面的语句，避免delete 或者 update错；

### 创建表
---
使用create创建表:语法：
```
create table(列名，列数据类型，约束条件)
```
null不等于空，空是''；
对主键定义关键字为:primary key

auto_increment:自动增量，比如学号从01开始新加入的人就是02，一个表只允许一个auto_increment列，它可以当主键；

> 可以使用select last_insert_id()查看最后一个auto_increment值；
> insert 可以插入指定的auto_increment值，但是不能存在已有的值

### 更改表
---
使用alter table关键字：
```
alter table 表名 add 列名 char(20);
```
> foreign key 定义外键

### 删除表
---
使用drop table关键字：
```
drop table 表名 ;
```

重命名表语法
```
rename table to 名字
```

### 视图
---
视图：视图是虚拟的表。与包含数据的表不一样，视图只包含使用时动态检索数据的查询；

为什么使用视图:
1. 重用sql语句
2. 简化复杂的sql操作。在编写查询后，可以方便地重用它而不必知道它的基本查询细节
3. 使用表的组成部分而不是整个表
4. **保护数据**
5. 更改数据格式和表示

视图的规则和限制：
1. 视图名必须唯一(不能给视图取与别的视图或者表相同的名字)
2. 对于可创建的视图的数目没有限制
3. 创建视图必须有足够的权限
4. 视图可以嵌套
5. order by 可以用在视图中，但如果从该视图检索数据的select语句中也有order by 那么该视图中的order by 将被覆盖
6. 视图不能索引，也不能有关联的触发器或默认值
7. 视图可以和表一起使用。例如:编写一条联结表和视图的select语句


创建视图：```create view```
通常用法``` create view 视图名 as select语句```
查看视图的语句: ```show create view viewname;```
删除视图： ```drop view viewname;
更新视图: 可以先用drop 再用 create 也可以用 create or replace view

*视图是一个虚拟的表*


### 触发器
---
触发器可响应以下语句：
1. DELETE
2. INSERT
3. UPDATE 
故一个表中最多6个触发器，分别是前三个语句的之前和之后。
触发器的创建: create trigger
删除： drop trigger 

## mysql 进阶

**sql语句执行的过程**：客户端将sql交给服务端---> 在服务端上 进行语句解析--->  绑定变量赋值 --->  语句执行 --->  提取数据（交给客户端）

> 语句解析举例： select distinct from join on where group by having order by limit
>
> 解析后： from on join where group by having select  distinct   order by limit

**InnoDB** : 事务优先（适合高并发的）行锁；（默认隔离级别，可重复读）

**MyISAM** : 性能优先 ，表锁；

---

**单值索引** ：单列，一个表可以有多个单值索引；

`create index user_index on 表(列)`

**唯一索引** :  不能重复；

`create unique index 列_index on user(列)`

**全文索引** : 多个列构成的索引；

`create index 列1_列2_index on 表('列1'，'列2')`

> 删除索引：
>
> drop index 索引名 on 表名；
>
> drop index id_index on user;
>
> 查询索引:
>
> show index from 表名;(\G)

## explain

select_type :  查询类型

- primary : 包含子查询的SQL中的主查询
- subquery: 包含子查询SQL中的子查询
- simple : 简单查询（不包含子查询、union）
- derived ： 衍生查询（使用到了临时表） `select * form (select * from table) 临时表`

## mysql 优化

>  优化insert语句

如果同一个客户端插入很多行，应该尽量使用多个值表一次性插入；

如果是一个文件装载一个表时，使用load data infile 比insert语句快很多。

> order by 优化

using filesort 有两种算法：单路排序和双路排序（根据IO次数）

单路排序将所有字段放入buffer，比双路更占用buffer空间，双路只放排序字段，然后在重新IO一遍（共两遍）

保证排序一致性(要么全是asc,要么是desc)

**索引：**

> 单表优化：

如果（a,b,c,d）复合索引 和使用的顺序全部一致（且不跨列使用）则复合索引全部使用，如果部分使用则使用部分索引。

（select a1,a2,a3,a4 from 表 where a1 =1 and a2= 2 order by a4 = 2  ）

> 多表优化：

- 联合查询时，小表在左，左表建索引；

- 不要在索引上进行其他操作（计算）。

- 最好不使用not null ，is null, != <>;

- 尽量使用索引覆盖；
- 不要以%开头
- 不要进行类型转换，where name(字符型) = 121(整型)
- 不要使用or

### 其他优化方法

**exists 和 in **: 主查询的数据集大用in，子查询数据集大用exist;

