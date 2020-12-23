## Redis

## NoSQL

NoSQL = Not SQL (不仅仅是SQL)

泛指非关系型数据库，随着web2.0互联网的诞生！传统的关系数据库很难对付web2.0时代！尤其是超大规模的高并发社区！

NoSQL在如今大环境下发展迅速，Redis发展最快，也是必须掌握的技术！

很多数据类型的个人信息，社交网络，地理位置。这些数据类型的存储不需要一个固定的格式，不需要多余的操作就可以横向扩展！Map<String,Object>来控制。



> NoSQL特点

解耦!

1、方便扩展（数据间没有关系，很好扩展）

2、大数据量高性能（Redis一秒写8W次，读取11W，NoSQL的缓存记录级，是一种细粒度的缓存，性能高）

3、数据类型是多样性的！（不需要设计数据库）

4、传统的RDBMS和NoSQL

```
传统的 RDBMS
- 结构化组织
- SQL
- 数据和关系都存在单独的表中
- 操作，数据定义语言
- 严格的一致性
- 基础的事务
```

```
NoSQL
- 没有固定的查询语言
- 键值对存储，列存储，文档存储，图形数据库（社交关系）
- 最终一致性
- CAP定理和BASE
- 高性能，高可用，高可扩
```

> 3V + 3高

3V:

+ 海量Volume
+ 多样Variety
+ 实时Velocity

3高：

+ 高并发
+ 高性能 （保证用户体验）
+ 高可扩

NoSQL + RDBMS



## NoSQL四大分类

**KV键值对**

+ 新浪：**Redis**
+ 美团：Redis + Tair
+ 阿里、百度：Redis + memecache

**文档型数据库（bson格式和json一样）**

+ **MongoDB**
  + MongoDB是一个基于分布式文件存储的数据库，c++编写，用来处理大量文档
  + MongoDB 介于关系型数据库和非关系型数据库中间的产品，MongoDB 是非关系型数据库中功能最丰富的，最像关系型数据库的。
+ ConthDB

**列存储数据库**

+ **HBase**
+ 分布式文件系统

**图关系数据库**

+ 不是存图形而是存关系，如朋友圈社交网络，广告推荐
+ **Neo4j**,infoGrid；

## Redis入门

Redis，远程服务字典。

> Redis干嘛

1、内存存储、持久化、内存中是断电即失、所以说持久化很重要（数据库持久化回答rdb,aof)

2、效率高，用于告诉缓存

3、发布订阅系统

4、地图信息分析

5、计时器、计数器（浏览量！）

> Redis特性

1、多样的数据类型

2、持久化

3、集群

4、事务

```bash
# redis-benchmark
# redis-benchmark -h localhost -p 6379 -c 100 -n 100000
```

## 基础知识

![image-20200416225047525](C:\Users\Administrator\AppData\Roaming\Typora\typora-user-images\image-20200416225047525.png)

默认16个数据库，使用的是第0个。

可以使用select进行切换。

```bash
127.0.0.1:6379> select 3 # 切换数据库
OK
127.0.0.1:6379[3]> DBSIZE # 查看db大小
(integer) 0
127.0.0.1:6379[3]> 

```

基本语法

```bash
127.0.0.1:6379> get name 
"zhu"
127.0.0.1:6379> keys * 查看所有key

127.0.0.1:6379> flushdb  //清空当前库
OK
127.0.0.1:6379> keys *
(empty list or set)


flushall 清空所有数据库
```

**redis 端口号为什么是6379 （他偶像1的名字 手机打出来是6379）**

> Redis是单线程的

Redis是基于内存的操作，cpu不是Redis性能瓶颈，Redis的瓶颈是根据机器的内存和网络带宽，既然可以使用单线程来实现，就使用单线程。

Redis是C写的， 官方提供的数据为100000+ 的QPS,完全不比同样使用key-value的memecache差

**为什么Redis单线程还这么块？**

1、误区1：高性能服务器一定是多线程的？

2、误区2：多线程（cpu上下文会切换！）一定比单线程效率高！

先去cpu>内存>硬盘

核心：redis是将所有的数据都存储在内存中，所以单线程去操作效率是最高的，多线程（cpu上下文会切换:耗时）对内存系统来说，没有上下文切换效率是最高的。

## Redis五大基本数据类型

#### Redis-Key

```bash
# set key name
# keys *
# exists name //查看是否存在
# expire name 10 // key 为 name 的10 过期 （单点登录，过期时间）
# move name 1 // 将key为 name的 移到 数据库1
# select 1 // 选择 数据库 1
# ttl name // 查看 key为name的 过期时间
# type  name // 查看当前 key的 类型
```

#### String(字符串)

```bash
#######################################
127.0.0.1:6379> set key1 v1 // 设置值
OK
127.0.0.1:6379> get key1  // 获得
"v1"
127.0.0.1:6379> keys * //查看所有key 
1) "key1"
2) "name"
3) "age"
127.0.0.1:6379> EXISTS kye1   // 判断是否存在
(integer) 0
127.0.0.1:6379> append key1 "hello" // 追加字符串，如果key不存在，则创建（set key "hello"）
(integer) 7
127.0.0.1:6379> get key1 
"v1hello"
127.0.0.1:6379> STRLEN key1 // 获取字符串长度
(integer) 7
127.0.0.1:6379> append key1 "hello word" 
(integer) 17
127.0.0.1:6379> STRLEN key1
(integer) 17
127.0.0.1:6379> 
##########################################

127.0.0.1:6379> set views 0
OK
127.0.0.1:6379> get views 
"0"
127.0.0.1:6379> INCR views // 自增1
(integer) 1
127.0.0.1:6379> decr views // 自减1
(integer) 0
127.0.0.1:6379> incrby views 10  // + 10
(integer) 10
127.0.0.1:6379> incrby views 18
(integer) 28
127.0.0.1:6379> decrby views 5 // -5
(integer) 23
##########################################

# 截取字符串
127.0.0.1:6379> set key1 "hello,world"
OK
127.0.0.1:6379> get key1
"hello,world"
127.0.0.1:6379> getrange key1 0 3 // 获取0-3的字符串 闭区间
"hell"
127.0.0.1:6379> getrange key1 0 -1 // 获取全部
"hello,world"
##################################################### 
# 替换
127.0.0.1:6379> setrange key2 1 xx // 将第一个位置的字符替换成xx
(integer) 7
127.0.0.1:6379> get key2
"axxdefg"
#############################################
# setex (set with expire) # 设置过期时间
# setnx (set if not exist) # 不存在设置 （在分布式锁中经常使用）

127.0.0.1:6379> setex key3 30 "hello" // 设置值为 hello， 并且30s 后过期（ex = expire）
OK
127.0.0.1:6379> ttl key3
(integer) 25
127.0.0.1:6379> get key3
"hello"
127.0.0.1:6379> setnx mymey "redis" # 如果不存在 则创建mymey ， nx = not exixt;
(integer) 1
127.0.0.1:6379> keys *
1) "key1"
2) "mymey"
3) "key2"
127.0.0.1:6379> setnx mymey "mongoDb"
(integer) 0
127.0.0.1:6379> get mymey
"redis"
##############################################################
# mset # 设置和获取多个值
# mget

127.0.0.1:6379> mset k1 v1 k2 v2 k3 v3 # 设置多个值
OK
127.0.0.1:6379> keys *
1) "k2"
2) "k3"
3) "k1"
127.0.0.1:6379> msetnx k1 v1 k2 v2
(integer) 0
127.0.0.1:6379> msetnx k1 v1 k5 v2  # 原子性操作，要么全部成功要么全部失败
(integer) 0
127.0.0.1:6379> get k5
(nil)

# 对象
set user:1 {name:zhangsan,age:3} # 设置一个user:1 对象值，为json 字符串保存一个对象
# user:{id}:{filed},如此设置在redis完全ok

127.0.0.1:6379> mset user:1:name zhangsan user:1:age 2 
OK
127.0.0.1:6379> mget user:1:name user:1:age
1) "zhangsan"
2) "2"
#######################################################
getset # 先get再set
127.0.0.1:6379> getset db redis # 如果不存在，则返回 nil
(nil)
127.0.0.1:6379> get db
"redis"
127.0.0.1:6379> getset db mongdb # 如果存在则获取，再设置
"redis"
127.0.0.1:6379> get db
"mongdb"

##############################
```

数据库是相同的!

String类似使用场景，value除了是字符串，还可以是数字

+ 计数器
+ 统计单位的数量
+ 粉丝，关注数
+ 对象缓存存储

#### List

在redis中，我们可以把list玩成，栈，队列，阻塞队列!

所有的List的命令都是l开头的！

```bash
127.0.0.1:6379> lpush list one # 放到首部
(integer) 1
127.0.0.1:6379> lpush list two
(integer) 2
127.0.0.1:6379> lpush list three
(integer) 3
127.0.0.1:6379> lrange list 0 -1
1) "three"
2) "two"
3) "one"
127.0.0.1:6379> lrange list 0 1 # 获取range 区间的值
1) "three"
2) "two"
127.0.0.1:6379> Rpush list right # 将一个值放到尾部
(integer) 4
127.0.0.1:6379> lrange list 0 -1
1) "three"
2) "two"
3) "one"
4) "right"
##############################################
lpop # 对应上方命令
rpop 
127.0.0.1:6379> Lrange list 0 -1 # 移除最左边
1) "three"
2) "two"
3) "one"
4) "right"
127.0.0.1:6379> lpop list
"three"
127.0.0.1:6379> rpop list # 移除最右边的元素
"right"
127.0.0.1:6379> lrange list 0 -1 
1) "two"
2) "one"
####################################
lindex (从零开始，类似数组)
127.0.0.1:6379> lindex list 1 # 通过下标获取值
"one"
127.0.0.1:6379> lindex list 0
"two"
###########################################
Llen 长度
127.0.0.1:6379> lpush list one
(integer) 1
127.0.0.1:6379> lpush list two
(integer) 2
127.0.0.1:6379> lpush list three
(integer) 3
127.0.0.1:6379> Llen list # 返回列表长度
(integer) 3
#######################################################
移除指定的值
Lrem

127.0.0.1:6379> lrange list 0 -1
1) "three"
2) "two"
127.0.0.1:6379> lpush list three
(integer) 3
127.0.0.1:6379> lrange list 0 -1
1) "three"
2) "three"
3) "two"
127.0.0.1:6379> lrem list 1 three # 一个three
(integer) 1
127.0.0.1:6379> lpush list three
(integer) 3
127.0.0.1:6379> Lrem list 2 three # 移除2个three
(integer) 2
127.0.0.1:6379> lrange list 0 -1
1) "two"
########################################################
trim 修剪, 

127.0.0.1:6379> rpush mylist "hello"
(integer) 1
127.0.0.1:6379> rpush mylist "hello1"
(integer) 2
127.0.0.1:6379> rpush mylist "hello2"
(integer) 3
127.0.0.1:6379> rpush mylist "hello3"
(integer) 4
127.0.0.1:6379> ltrim mylist 1 2 # 截取指定的值 （下标从【1，2】）
OK
127.0.0.1:6379> lrange mylist 0 -1
1) "hello1"
2) "hello2"
############################################################
rpoplpush #移除列表最后一个元素并 添加新的一个元素
127.0.0.1:6379> lrange mylist 0 -1
1) "hello1"
2) "hello2"
127.0.0.1:6379> rpoplpush mylist myotherlist # 移除最后一个元素到新的列表
"hello2"
127.0.0.1:6379> lrange mylist 0 -1
1) "hello1"
127.0.0.1:6379> lrange myotherlist 0 -1
1) "hello2"
################################################################
# lset 将列表指定下标的值，替换为另外一个值，更新操作
127.0.0.1:6379> EXISTS list # 判断这个列表是否存在
(integer) 0
127.0.0.1:6379> lset list 0 item# 如果不存在会报错
(error) ERR no such key
127.0.0.1:6379> lpush list value1
(integer) 1
127.0.0.1:6379> lrange list 0 0 
1) "value1"
127.0.0.1:6379> lset list 0 item #更新当前下标的值
OK
127.0.0.1:6379> lrange list 0 0
1) "item"
###########################################
# Linsert 将某个具体的value 插入到某个元素的 前面 或者后面
127.0.0.1:6379> rpush mylist hello
(integer) 1
127.0.0.1:6379> rpush mylist world
(integer) 2
127.0.0.1:6379> LINSERT mylist before world other # 在worl前插入other
(integer) 3
127.0.0.1:6379> lrange mylist 0 -1
1) "hello"
2) "other"
3) "world"
127.0.0.1:6379> linsert mylist after other 66666 # 在other 之后插入
(integer) 4
127.0.0.1:6379> lrange mylist 0 -1
1) "hello"
2) "other"
3) "66666"
4) "world"


```

+ list 实际上是链表，可以在before Node after， left,right 插入
+ 如果key 不存在 则创建新的链表
+ 存在则新增内容
+ 如果移除了所有值，空链表也代表不存在
+ 在两边插入或改动，效率最高！改变中间元素，效率会低点。

#### Set(集合)

set中的值是不能重复的！

```bash
127.0.0.1:6379> Sadd myset hello # 在set中添加元素
(integer) 1
127.0.0.1:6379> Sadd myset hello1
(integer) 1
127.0.0.1:6379> Sadd myset hello2
(integer) 1
127.0.0.1:6379> smembers myset # 查看set的所有值
1) "hello2"
2) "hello1"
3) "hello"
127.0.0.1:6379> SISMEMBER myset hello #判断是否存在 1代表存在，
(integer) 1
127.0.0.1:6379> SISMEMBER myset hello9 #0代表没得
(integer) 0
127.0.0.1:6379> Scard myset # 获取集合内容的个数
(integer) 3

##############################
# srem set中移除
127.0.0.1:6379> Scard myset
(integer) 3
127.0.0.1:6379> srem myset hello # 移除hello
(integer) 1
127.0.0.1:6379> scard myset # 长度为2
(integer) 2
127.0.0.1:6379> SMEMBERS myset
1) "hello2"
2) "hello1"
################################################
# set 无序不重复集合，抽随机！
127.0.0.1:6379> Srandmember myset # 随机抽出一个元素
"hello1"
127.0.0.1:6379> Srandmember myset
"hello2"
127.0.0.1:6379> Srandmember myset
"hello1"
127.0.0.1:6379> Srandmember myset
"hello1"
127.0.0.1:6379> Srandmember myset
"hello1"
127.0.0.1:6379> Srandmember myset
"hello1"
127.0.0.1:6379> Srandmember myset
"hello1"
127.0.0.1:6379> Srandmember myset 2 # 随机抽出 指定个数的元素
1) "hello1"
2) "hello2"
#######################################
# 删除指定key，随机删除key
127.0.0.1:6379> spop myset # 随机删除
"hello1"
127.0.0.1:6379> SMEMBERS myset
1) "hello2"
################################################################
# 将一个指定的值，移动到另一个set中
127.0.0.1:6379> sadd myset "hello"
(integer) 1
127.0.0.1:6379> sadd myset "world"
(integer) 1
127.0.0.1:6379> sadd myset "zhuzhu"
(integer) 1
127.0.0.1:6379> sadd myset "set2"
(integer) 1
127.0.0.1:6379> smove myset myset2 "zhuzhu" # 将“zhzhu”移动到Myset2
(integer) 1
127.0.0.1:6379> smembers myset
1) "world"
2) "set2"
3) "hello"
127.0.0.1:6379> smembers myset2
1) "zhuzhu"
##############################################################
微博，B站（共同关注，集合）

127.0.0.1:6379> sadd key1 a
(integer) 1
127.0.0.1:6379> sadd key1 b
(integer) 1
127.0.0.1:6379> sadd key1 c
(integer) 1
127.0.0.1:6379> sadd key2 c
(integer) 1
127.0.0.1:6379> sadd key2 d
(integer) 1
127.0.0.1:6379> sadd key2 e
(integer) 1
127.0.0.1:6379> sdiff key1 key2  # 差集
1) "b"
2) "a"
127.0.0.1:6379> sinter key1 key2 # inter 并集
1) "c"
127.0.0.1:6379> sunion key1 key2 # sunion 并集
1) "e"
2) "a"
3) "c"
4) "b"
5) "d"

```

微博，将所有用户关注的人放在一个set集合中! 将它的粉丝也放在一个集合中！

共同关注，共同爱好，二度好友,推荐好友。

#### Hash(哈希)

Map集合，key-Map集合<key-value>！，key的值是map集合。本质和string没太大区别，只是变成了一个简单的key-value

`hset myhash field1 kuangshen`

```bash
127.0.0.1:6379> hset myhash field1 kuangshen # set 一个具体的 key-value
(integer) 1
127.0.0.1:6379> hget myhash field1 # 获取一个字段
"kuangshen"
127.0.0.1:6379> hmset myhash field1 hello field2 world # set多个key-value
OK
127.0.0.1:6379> hmget myhash field1 field2 # get 多个key的值
1) "hello"
2) "world"
127.0.0.1:6379> hgetall myhash # 获取所有
1) "field1"
2) "hello"
3) "field2"
4) "world"

127.0.0.1:6379> hdel myhash field1 # 删除指定的key
(integer) 1
127.0.0.1:6379> hgetall myhash # 获取所有key-value键值对
1) "field2"
2) "world"
########################################
# hlen 获取hash字段数量

127.0.0.1:6379> hmset myhash field1 hello field2 world
OK
127.0.0.1:6379> hgetall myhash
1) "field2" 键
2) "world" 值
3) "field1" 键
4) "hello" 值
127.0.0.1:6379> hlen myhash # 两个键值对
(integer) 2
##################################################################
127.0.0.1:6379> Hexists myhash field1 # 判断hash 中是否有该key
(integer) 1
127.0.0.1:6379> Hexists myhash field2
(integer) 1
127.0.0.1:6379> Hexists myhash field123
(integer) 0
###############################################################
# 只获取 所有的key
# 只获取所有的value
127.0.0.1:6379> hkeys myhash # 获取所有指定的值
1) "field2"
2) "field1"
127.0.0.1:6379> hvals myhash # 获取所有的字段
1) "world"
2) "hello"
#############################################
incr decr
127.0.0.1:6379> hset myhash field3 5 
(integer) 1
127.0.0.1:6379> HINCRBY myhash field3 1 #指定增量，如果是-1 相当于Hdecrby
(integer) 6
127.0.0.1:6379> hsetnx myhash field4 88888 # 如果存在则设置
(integer) 1
127.0.0.1:6379> hsetnx myhash field4 88888
(integer) 0
################################################
127.0.0.1:6379> hset user:1 name zhuzhu # 设置user信息
(integer) 1
127.0.0.1:6379> hget user:1:name
(error) ERR wrong number of arguments for 'hget' command
127.0.0.1:6379> hget user:1 name
"zhuzhu"

```

hash变更的数据 user name age



#### Zset(有序集合)

在set的基础上，增加一个值.

set k1,v1 Zset k1 score1 v1;

```bash
127.0.0.1:6379> zadd myset 1 one # 添加值
(integer) 1
127.0.0.1:6379> zadd myset 2 two 3 three
(integer) 2

127.0.0.1:6379> ZRANGE myset 0 -1 # 获取
1) "one"
2) "two"
3) "three" 

#####################################
127.0.0.1:6379> zadd salary 2500 xiaoming
(integer) 1
127.0.0.1:6379> zadd salary 5000 xiaoming1
(integer) 1
127.0.0.1:6379> zadd salary 500 xiaoming2
(integer) 1
127.0.0.1:6379> ZRANGEBYSCORE salary -inf +inf # 从min-max 的值闭区间
1) "xiaoming2"
2) "xiaoming"
3) "xiaoming1"
127.0.0.1:6379> ZRANGEBYSCORE salary 0 -1
(empty list or set)
127.0.0.1:6379> ZRANGEBYSCORE salary +inf -inf
(empty list or set)
127.0.0.1:6379> ZRANGEBYSCORE salary -inf +inf withscores 带上分数
1) "xiaoming2"
2) "500"
3) "xiaoming"
4) "2500"
5) "xiaoming1"
6) "5000"
127.0.0.1:6379> ZRANGEBYSCORE salary -inf 3000 withscores
1) "xiaoming2"
2) "500"
3) "xiaoming"
4) "2500"
#####################################
# 移除元素 zrem
127.0.0.1:6379> zrange salary 0 -1
1) "xiaoming2"
2) "xiaoming"
3) "xiaoming1"
127.0.0.1:6379> zrem salary xiaoming
(integer) 1
127.0.0.1:6379> zrange salary 0 -1 
1) "xiaoming2"
2) "xiaoming1"
127.0.0.1:6379> zcard salary # 获取元素的个数
(integer) 2
127.0.0.1:6379> zrevrange salary 0 -1 withscores # 从大到小排序，rev（反转） range
1) "xiaoming1"
2) "5000"
3) "xiaoming2"
4) "500"
###################################################
127.0.0.1:6379> zadd myset 1 hello
(integer) 1
127.0.0.1:6379> zadd myset 2 hello2 3 hello3
(integer) 2
127.0.0.1:6379> zcount myset 1 3 # 获取指定区间的成员数量
(integer) 3
127.0.0.1:6379> zcount myset 1 2
(integer) 2
127.0.0.1:6379> zcount myset 5 2
(integer) 0
127.0.0.1:6379> zcount myset 5 100
(integer) 0

```

案例思路：set 排序 存储班级成绩表，工资表排序

普通消息，1，普通消息2，带权重进行判断

## 三种特殊数据类型

#### geospatial 地理位置

Redis的Geo。

```bash
############## geoadd 添加地理位置
# 规则：两极无法直接添加， 一般会下载城市数据，通过java一键导入
# 参数： key 值（维度，经度，名称）
127.0.0.1:6379> geoadd china:city 116.40 39.90 beijing
(integer) 1
127.0.0.1:6379> geoadd china:city 121.47 31.23 shanghai
(integer) 1
127.0.0.1:6379> geoadd china:city 106.50 29.53 chongqing 114.05 22.52 shenzheng 
(integer) 2
127.0.0.1:6379> geoadd china:city 120.16 30.24 hangzhou 108.96 34.26 xian
(integer) 2

```

- 有效的经度从-180度到180度。
- 有效的纬度从-85.05112878度到85.05112878度。

当坐标位置超出上述指定范围时，该命令将会返回一个错误。

> getpos

```bash
# 获取 城市的 坐标值
127.0.0.1:6379> GEOPOS china:city shanghai
1) 1) "121.47000163793563843"
   2) "31.22999903975783553"
127.0.0.1:6379> GEOPOS china:city shanghai chongqin beijing
1) 1) "121.47000163793563843"
   2) "31.22999903975783553"
2) (nil)·						# 如果不存在 返回nil
3) 1) "116.39999896287918091"
   2) "39.90000009167092543"
127.0.0.1:6379> GEOPOS china:city shanghai chongqing beijing
1) 1) "121.47000163793563843"
   2) "31.22999903975783553"
2) 1) "106.49999767541885376"
   2) "29.52999957900659211"
3) 1) "116.39999896287918091"
   2) "39.90000009167092543"

```

> GEODIST

两人之间的距离

单位：

+ m 米
+ km 千米
+ mi 英里
+ ft 英尺

```bash
127.0.0.1:6379> GEODIST china:city beijing shanghai # 两个城市的距离
"1067378.7564"
127.0.0.1:6379> GEODIST china:city beijing shanghai km # 单位换成km
"1067.3788"

```

> georadius 以给定的经纬度为中心，找出某一半径内的元素

附近的人？ （获取所有人的地址，定位！）通过半径来查询

```bash
127.0.0.1:6379> GEORADIUS china:city 110 30 1000 km #（从 110 30  这个经纬度找）
1) "chongqing"
2) "xian"
3) "shenzheng"
4) "hangzhou"
127.0.0.1:6379> GEORADIUS china:city 110 30 500 km
1) "chongqing"
2) "xian"

127.0.0.1:6379> GEORADIUS china:city 110 30 500 km withdist # 带上距离参数
1) 1) "chongqing"
   2) "341.9374"
2) 1) "xian"
   2) "483.8340"
127.0.0.1:6379> GEORADIUS china:city 110 30 500 km withcoord # 带上经纬度坐标
1) 1) "chongqing"
   2) 1) "106.49999767541885376"
      2) "29.52999957900659211"
2) 1) "xian"
   2) 1) "108.96000176668167114"
      2) "34.25999964418929977"
127.0.0.1:6379> GEORADIUS china:city 110 30 500 km withcoord withcoord count 1 # 筛选出指定个数的 值
1) 1) "chongqing"
   2) 1) "106.49999767541885376"
      2) "29.52999957900659211"
127.0.0.1:6379> GEORADIUS china:city 110 30 500 km withcoord withcoord count 2
1) 1) "chongqing"
   2) 1) "106.49999767541885376"
      2) "29.52999957900659211"
2) 1) "xian"
   2) 1) "108.96000176668167114"
      2) "34.25999964418929977"

```

> GEORADIUSBYMEMBER  通过geo成员查找

```bash
# 指定城市周围的其它 城市
127.0.0.1:6379> GEORADIUSBYMEMBER china:city beijing 1000 km
1) "beijing"
2) "xian"
127.0.0.1:6379> GEORADIUSBYMEMBER china:city hangzhou  400 km
1) "hangzhou"
2) "shanghai"

```

> geohash 命令，返回一个或多个位置元素的hash表示

```bash
# 如果两个字符串越接近，表示距离越近
127.0.0.1:6379> geohash china:city shanghai hangzhou
1) "wtw3sj5zbj0"
2) "wtmkn31bfb0"

```

> GEO 底层的实现原理其实就是 Zset

```bash
# 可以使用ZSET 所有命令
127.0.0.1:6379> ZRANGE china:city 0 -1 # 查看地图中全部元素
1) "chongqing"
2) "xian"
3) "shenzheng"
4) "hangzhou"
5) "shanghai"
6) "beijing"
127.0.0.1:6379> zrem china:city beijing # 移除指定的元素
(integer) 1
127.0.0.1:6379> ZRANGE china:city 0 -1
1) "chongqing"
2) "xian"
3) "shenzheng"
4) "hangzhou"
5) "shanghai"
```

#### Hyperloglog 基数统计

> 什么是基数?

A{1，3，5，7，8，7}

B{1，3，5，7，8}

基数{找不重复的元素} = 5，可以接受误差

redis Hyperloglog 基数统计的算法！

**网站的UV(一个人访问一个网站多次，但还是算做一个人！)**

优点：占用内存是固定的，2^64不同元素的技术，只要废12kb内存

> 测试使用

```bash
127.0.0.1:6379> pfadd mykey a b c d e f g h i j # 创建第一组元素
(integer) 1
127.0.0.1:6379> pfcount mykey # 统计 mykey元素数量
(integer) 10
127.0.0.1:6379> pfadd mykey2 i j z x c v b n m # 创建第二组元素
(integer) 1
127.0.0.1:6379> pfcount mykey2 
(integer) 9
127.0.0.1:6379> pfmerge mykey3 mykey mykey2 # 合并Mykey mykey2 成mykey3（并集）
OK

127.0.0.1:6379> pfcount mykey3
(integer) 15
```

如果允许容错，那么就可以使用Hyperloglog！

如果不需要容错，则set或者字节的数据类型！

#### Bitmap

> 位存储

只有两个状态的，都可以使用Bitmaps

> 记录7天的打卡情况

```bash
127.0.0.1:6379> setbit sign 0 0
(integer) 0
127.0.0.1:6379> setbit sign 1 0
(integer) 0
127.0.0.1:6379> setbit sign 2 0
(integer) 0
127.0.0.1:6379> setbit sign 3 1
(integer) 0
127.0.0.1:6379> setbit sign 4 1
(integer) 0
127.0.0.1:6379> setbit sign 5 0
(integer) 0
127.0.0.1:6379> setbit sign 6 1
(integer) 0

```

> 查看是否打卡

```bash
127.0.0.1:6379> getbit sign 3 # 查看 第三天的
(integer) 1
127.0.0.1:6379> getbit sign 6
(integer) 1
127.0.0.1:6379> getbit sign 5
(integer) 0
127.0.0.1:6379> bitcount sign # 统计打卡记录
(integer) 3  # 共三天

```

## 事务

Redis 事务本质：一组命令的集合! 一个事务中的所有命令都会被序列化，在事务执行的过程中，会按照顺序执行。

```
---------- 队列
set
set
set
----------
```

**Redis事务没有隔离级别的概念！**

所有的命令都在事务中，并没有直接被执行！只有发起执行命令时才会执行（exec）

**Redis单条命令保存原子性，但是事务不保证原子性！**

redis事务：

+ 开启事务（multi）
+ 命令入队
+ 执行事务（exec)

> 正常执行事务

```bash
127.0.0.1:6379> multi # 开启事务
OK
127.0.0.1:6379> set k1 v1
QUEUED
127.0.0.1:6379> set k2 v2
QUEUED
127.0.0.1:6379> get k2
QUEUED
127.0.0.1:6379> set k3 v3
QUEUED
127.0.0.1:6379> exec # 执行事务
1) OK
2) OK
3) "v2"
4) OK
#############################################
# 取消事务后，队列中的命令都不会执行
127.0.0.1:6379> multi  # 开启事务
OK
127.0.0.1:6379> set k1 v1
QUEUED
127.0.0.1:6379> set k2 v2
QUEUED
127.0.0.1:6379> set k4 v4
QUEUED
127.0.0.1:6379> discard # 取消事务
OK
127.0.0.1:6379> get k4
(nil)

```

> 编译型异常

```bash
127.0.0.1:6379> multi
OK
127.0.0.1:6379> set k1 v1
QUEUED
127.0.0.1:6379> set k2 v2
QUEUED
127.0.0.1:6379> set k4 v4
QUEUED
127.0.0.1:6379> discard
OK
127.0.0.1:6379> get k4
(nil)
127.0.0.1:6379> multi
OK
127.0.0.1:6379> set k1 v1
QUEUED
127.0.0.1:6379> set k2 v2
QUEUED
127.0.0.1:6379> getsetasadf k  # 命令错误后
(error) ERR unknown command `getsetasadf`, with args beginning with: `k`, 
127.0.0.1:6379> set k4 v5
QUEUED
127.0.0.1:6379> exec # 事务不会执行
(error) EXECABORT Transaction discarded because of previous errors.

```



> 运行时异常(1/0),如果 队列中存在语法性，那么执行命令时，其它命令是正常执行的。（所以不保证原子性）

```bash
127.0.0.1:6379> set k1 v1
OK
127.0.0.1:6379> multi
OK
127.0.0.1:6379> incr k1 # 自增 字符串 会出错
QUEUED
127.0.0.1:6379> set k2 v2
QUEUED
127.0.0.1:6379> set k3 v3
QUEUED
127.0.0.1:6379> get k3
QUEUED
127.0.0.1:6379> exec # 但是还是可以执行事务
1) (error) ERR value is not an integer or out of range
2) OK
3) OK
4) "v3"
127.0.0.1:6379> keys *
1) "k2"
2) "k3"
3) "k1"

```

> 监控！Watch

**乐观锁** ：访问记录前不会加锁，数据更新时会正式对数据冲突与否进行检验

**悲观锁** ：在数据被处理之前先对数据进行加锁。

> Redis设置监控

```bash
127.0.0.1:6379> set money 100
OK
127.0.0.1:6379> se out 0
(error) ERR unknown command `se`, with args beginning with: `out`, `0`, 
127.0.0.1:6379> watch money
OK
127.0.0.1:6379> multi
OK
127.0.0.1:6379> decrby money 20
QUEUED
127.0.0.1:6379> incrby out 20
QUEUED
127.0.0.1:6379> exec
1) (integer) 80
2) (integer) 20

```

测试多线程修改值，监视！

```bash
## 线程1
127.0.0.1:6379> watch money # 监视money，相当于加乐观锁
OK
127.0.0.1:6379> multi 
OK 
127.0.0.1:6379> DECRBY money 10
QUEUED
127.0.0.1:6379> INCRBY out 10
QUEUED
127.0.0.1:6379> exec # 执行之前，另一个线程，修改了我们的值，导致事务执行失败
(nil)
## 线程二 修改线程一的money
127.0.0.1:6379> get money 
"80"
127.0.0.1:6379> set money 1000
OK

```

## Jedis

> jedis 是Redis 官方推荐的java连接开发工具！ 使用java 操作Redis中间件，如果要使用java操作redis，那么一定要学习jedis

+ 连接数据库
+ 操作命令
+ 断开

```java
public static void main(String[] args) {
    Jedis jedis = new Jedis("127.0.0.1",6379);
    // 所有指令都是方法
    System.out.println(jedis.ping());
}
```

#### SpringBoot整合

SpringBoot2.x后，从jedis 变成了lettuce?

jedis: 采用直连，多个线程操作的话是不安全的，如果要避免不安全，使用jedis pool 连接池！更像BIO模式

lettuce:采用 netty,实例可以再多个线程中进行共享，不存在线程不安全的情况! 可以减少线程数据了，更像NIO模式



+ 导入依赖

  ```xml
  <dependency>
      <groupId>org.springframework.boot</groupId>
      <artifactId>spring-boot-starter-data-redis</artifactId>
  </dependency>
  ```

+ 配置

  ```properties
  spring.redis.host=127.0.0.1
  spring.redis.port=6379
  ```

+ 简单测试

  ```java
  // 获取Redis连接对象
  //        RedisConnection connection = redisTemplate.getConnectionFactory().getConnection();
  //        connection.flushAll();
  //        connection.flushDb();
  
  redisTemplate.opsForValue().set("name","aa");
  System.out.println(redisTemplate.opsForValue().get("name"));
  ```

  ## Redis.conf详解

  > 单位

1、配置文件 unit 单位 对大小写不敏感



![image-20200417222835162](C:\Users\Administrator\AppData\Roaming\Typora\typora-user-images\image-20200417222835162.png)

2、可以配置引用文件

![image-20200417222933595](C:\Users\Administrator\AppData\Roaming\Typora\typora-user-images\image-20200417222933595.png)

3、通用设置

```bash
bind 127.0.0.1 # 邦定的ip
protected-mode yes # 保护模式
port 6379 # 端口设置
daemonize yes # 守护线程开启
pidfile /var/run/redis_6379.pid # 如果后台运行，就要指定一个pid

# 日志
# Specify the server verbosity level.
# This can be one of:
# debug (a lot of information, useful for development/testing) # 测试和开发阶段
# verbose (many rarely useful info, but not a mess like the debug level)
# notice (moderately verbose, what you want in production probably) # 生产环境使用
# warning (only very important / critical messages are logged) 
loglevel notice
logfile "" # 生产的文件名
databases 16 # 数据库数量，默认是16
always-show-logo yes # 是否显示Logo
```

4、快照

持久化，在规定时间内，执行多少次操作，则会持久化到.rdb.aof

redis是内存数据库，如果没有持久化，则断电即失

```bash
save 900 1 # 如果900s内，至少有一个key进行了修改，则进行持久化操作
save 300 10 # 300s内， 10个Key进行修改
save 60 10000 # 60s内 1000 key进行修改

stop-writes-on-bgsave-error yes # 持久化 如果出错，是否继续工作

rdbcompression yes # 是否压缩 rdb 文件，需要消耗一些cpu 资源

rdbchecksum yes # 保存rdb时如果出错了进行校验
dir ./ # rdb 保存的目录

```

```bash
appendonly no   # 默认不开aof模式，使用rdb方式持久化，大部分情况下，rdb够用
appendfilename "appendonly.aof" # 持久化的文件名字

```

## Redis持久化

Redis在内存上操作，防止断电即失。

触发机制

1、 save规则满足的情况下，会自动出发rdb规则

2、执行flushall命令，也会触发我们的rdb规则！

3、退出redis，也会产生rdb文件！

备份自动生成一个dump.rdb

> 如何恢复rdb文件！

1、只需要将rdb文件放到redis启动目录就可以了，redis启动时会自动检查dump.rdb恢复其中的数据

2、查看需要存在的位置

```bash
config get dir
1) "dir"
2) "/usr/local/bin" # 如果这么目录下存在 dump.rdb 文件， 启动会自动恢复其中的数据
```

**优点：**

1、适合大规模的数据恢复

2、对数据的完整性不高

**缺点：**

1、需要一定的时间间隔进程操作！如果redis以为宕机了，这个最后异常修改的数据就没了（小范围的数据丢失）

2、fork进程的适合，会占用一定的内存空间！



**AOF持久化**

**优点：**实时的写入硬盘

**缺点**：消耗高；



**总结：** 大量读，少量写，用RDB持久化，如果写得多并且**数据不能丢**，用AOF。

## Redis消息订阅

订阅端：

```bash
127.0.0.1:6379> SUBSCRIBE zhuzhu # 订阅的频道
Reading messages... (press Ctrl-C to quit)
1) "subscribe"
2) "zhuzhu"
3) (integer) 1
1) "message" # 消息
2) "zhuzhu" # 频道
3) "hello,world" # 信息
1) "message"
2) "zhuzhu"
3) "hello,reids"
```

发送端：

```bash
127.0.0.1:6379> PUBLISH zhuzhu "hello,world" # 发布消息到频道
(integer) 1
127.0.0.1:6379> PUBLISH zhuzhu "hello,reids"
(integer) 1

```

## Redis主从复制

概念：从一台服务器的数据，复制到另一台。只能从主机到从机。

1、数据冗余 ： 实现了数据的热备份，是持久化之外的一种数据冗余方式

2、故障恢复 : 主节点出现问题时，可以从节点提供服务，实现快速故障恢复。

3、负载均衡 : 在主从复制的基础上，配合读写分离，可以由主节点提供写服务，从节点提供读服务，分担服务器负载，尤其是在写少读多的场景下，通过多个从节点分担读负载，可以大大提高Reduis服务的并发量。

4、高可用（集群）：主从复制是哨兵和集群能够实施的基础，因此说主从复制是Redis高可用的基础

#### 环境配置

只配置从库，不用配置主库！

```bash
127.0.0.1:6379> info replication # 查看当前库的信息
# Replication
role:master   # 角色 master
connected_slaves:0  # 没有从机
master_replid:30ae4c2a7fb832569ac63b8ead44cd07222a3e9a
master_replid2:0000000000000000000000000000000000000000
master_repl_offset:0
second_repl_offset:-1
repl_backlog_active:0
repl_backlog_size:1048576
repl_backlog_first_byte_offset:0
repl_backlog_histlen:0

```

配置：

1、复制三个conf文件

2、修改端口号（pid）。

3、修改dump.rdb名字

4、logfile名字

修改之后启动三个Redis服务器。

![image-20200418103232878](C:\Users\Administrator\AppData\Roaming\Typora\typora-user-images\image-20200418103232878.png)

#### 一主二从

**只要配置从机。**

```bash
127.0.0.1:6380> SLAVEOF 127.0.0.1 6379 # 认主
OK
127.0.0.1:6380> info replication
# Replication
role:slave
master_host:127.0.0.1
master_port:6379
master_link_status:up
master_last_io_seconds_ago:2
master_sync_in_progress:0
slave_repl_offset:0
slave_priority:100
slave_read_only:1
connected_slaves:0
master_replid:74cb4ee38b9c083d4f9280435e053e9c7f418452
master_replid2:0000000000000000000000000000000000000000
master_repl_offset:0
second_repl_offset:-1
repl_backlog_active:1
repl_backlog_size:1048576
repl_backlog_first_byte_offset:1
repl_backlog_histlen:0
#######################################
127.0.0.1:6379> info replication
# Replication
role:master
connected_slaves:2
slave0:ip=127.0.0.1,port=6380,state=online,offset=168,lag=0
slave1:ip=127.0.0.1,port=6381,state=online,offset=168,lag=0
master_replid:74cb4ee38b9c083d4f9280435e053e9c7f418452
master_replid2:0000000000000000000000000000000000000000
master_repl_offset:168
second_repl_offset:-1
repl_backlog_active:1
repl_backlog_size:1048576
repl_backlog_first_byte_offset:1
repl_backlog_histlen:168
```

从配置文件中配置：

![image-20200418104112120](C:\Users\Administrator\AppData\Roaming\Typora\typora-user-images\image-20200418104112120.png)

> 主机负责写，从机来读。

```bash
127.0.0.1:6380> set kkk oo # 从机写，会报错
(error) READONLY You can't write against a read only replica.
```

**测试:主机断开连接，从机依然连接的主机，但是不能写，如果主机重连，则恢复正常**

**如果是命令行配置的主从，从机重启就会变成主机，只要变回从机，数据就恢复了**

> 复制原理

Slave 启动 成功连接到master之后会发送一个sync同步命令

Master接到命令，启动后台存盘进程，同时收集所有接受到用于修改数据集的命令，**在后台执行完毕后，master 将传送正个数据文件到slave，并完成一次完全同步。**

+ **全量复制** ：slave 服务在接受到数据库文件数据后，将其存盘并加载到内存中。
+ **增量复制**：Master继续将新的所有手机到修改命令一次传给slave，完成同步。

只要是重新连接到主机，则会执行一次全量复制。每次修改数据 就会 执行增量复制。



> 如果主机端口连接，使用`SLAVEOF no one` 命令可以使自己变成主机！其它节点可以手动连接到最新的节点上

## 哨兵模式

哨兵原理：通过发送命令，等待Redis服务器的响应，从而监视运行的多个Redis服务器。

> 哨兵配置文件

```bash
# sentinel monito 被监控名称 Host port 1
sentinel monitor myredis 127.0.0.1 6379 1 #1 代表主机挂了 slave投票看谁当主机，票数最多的当。  
```

> 启动哨兵

```bash
redis-sentinel zconfig/sentinel.conf 
1977:X 18 Apr 2020 11:26:00.399 # oO0OoO0OoO0Oo Redis is starting oO0OoO0OoO0Oo
1977:X 18 Apr 2020 11:26:00.399 # Redis version=5.0.8, bits=64, commit=00000000, modified=0, pid=1977, just started
1977:X 18 Apr 2020 11:26:00.399 # Configuration loaded
                _._                                                  
           _.-``__ ''-._                                             
      _.-``    `.  `_.  ''-._           Redis 5.0.8 (00000000/0) 64 bit
  .-`` .-```.  ```\/    _.,_ ''-._                                   
 (    '      ,       .-`  | `,    )     Running in sentinel mode
 |`-._`-...-` __...-.``-._|'` _.-'|     Port: 26379
 |    `-._   `._    /     _.-'    |     PID: 1977
  `-._    `-._  `-./  _.-'    _.-'                                   
 |`-._`-._    `-.__.-'    _.-'_.-'|                                  
 |    `-._`-._        _.-'_.-'    |           http://redis.io        
  `-._    `-._`-.__.-'_.-'    _.-'                                   
 |`-._`-._    `-.__.-'    _.-'_.-'|                                  
 |    `-._`-._        _.-'_.-'    |                                  
  `-._    `-._`-.__.-'_.-'    _.-'                                   
      `-._    `-.__.-'    _.-'                                       
          `-._        _.-'                                           
              `-.__.-'                                               

1977:X 18 Apr 2020 11:26:00.400 # WARNING: The TCP backlog setting of 511 cannot be enforced because /proc/sys/net/core/somaxconn is set to the lower value of 128.
1977:X 18 Apr 2020 11:26:00.403 # Sentinel ID is 8197b43cadde8990cd392d1b86dcbdf8813bba22
1977:X 18 Apr 2020 11:26:00.403 # +monitor master myredis 127.0.0.1 6379 quorum 1
1977:X 18 Apr 2020 11:26:00.403 * +slave slave 127.0.0.1:6380 127.0.0.1 6380 @ myredis 127.0.0.1 6379

```

如果主机断开，就会在从机中随机选择一个服务器。

如果主机回来，只能当从机。

**优点：**

1、哨兵集群，基于主从复制，所有的主从配置优点，它全有

2、主从可以切换，故障可以转换，系统的可用性更好

3、哨兵模式是主从复制的升级手动到自动，更加健壮。

**缺点：**

1、redis不好在线扩容，集群容量一旦达到上线，在线扩容就十分麻烦。

2、 实现哨兵模式的配置其实是很麻烦的，里面有很多选择。

## Redis缓存穿透和雪崩









## 问题

1. Redis的缓存淘汰策略
   + 定期检查
   + 惰性删除