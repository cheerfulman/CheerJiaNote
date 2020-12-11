## Redis

Redis是分布式系统中的重要组件，主要是解决高并发、大数据场景下、热点数据访问性能问题，提高高性能快速访问的。

当项目中部分数据比较频繁，对下游DB造成较大的服务器压力时，可以使用缓存提高效率。

**官方话语**：支持10W+的并发

## 常用的数据结构指令

![](https://upload-images.jianshu.io/upload_images/11474088-2bff85f7b51a7207.png?imageMogr2/auto-orient/strip|imageView2/2/w/538/format/webp)

## 场景分析

### string存储

![image-20201210153159058](../img/image-20201210153159058.png)

**场景一：商品库存数**

一般来说，商品库存数是一个热点数据，交易行为会直接影响库存。而Redis自身String提供了：

`set goods_03 10` 设置id为3的商品库存为10；

`decr goods_03` 扣减库存

**场景二：时效信息存储**

Redis做缓存有一个最大的优点 ----  可以设置失效时间 expireTime；

比如获取登录验证码，设置验证码有效时间在5分钟等；

![](https://upload-images.jianshu.io/upload_images/11474088-450f13064b41e206.png?imageMogr2/auto-orient/strip|imageView2/2/w/564/format/webp)

### hash存储数据

![](https://upload-images.jianshu.io/upload_images/11474088-58c3fc9de814b5bf.png?imageMogr2/auto-orient/strip|imageView2/2/w/583/format/webp)

可以用HASH存储对象：{name : lomont, age : 18， sex: 男}

**场景一：购物车**

功能：

+ **全选**：一键获取该用户所有商品
+ **商品数量**：购物车图标上显示购物车数量
+ **删除**：移除购物车里的某个商品
+ **增加**：增加或减少某个商品的数量

比如该用户userID : 003

`hmset cart:003 goods:01 1 goods:03 3`

用户ID为KEY,商品ID为Field，加入购物车数量为value

**获取商品数量** ： `hlen cart:001`

**获取全部商品：**`hgetall cart:003`

**增加商品数量：** `hincrby cart:003 goods:01 1`

<img src="../img/image-20201210203502003.png" alt="image-20201210203502003" style="zoom:50%;" />

#### **实现信息存储的优缺点**

##### 原生: 

- set user: 1:name 	james;
- set user:1:age        23;
- set user:1:sex        boy;

**优点:**简单直观，每个键对应一个值

**缺点:**键数过多，占用内存多，用户信息过于分散，不用于生产环境

##### **将对象序列化存入**

redis set user:1 serial ize (userInfo);

**优点:**编程简单，若使用序列化合理内存使用率高

**缺点:**序列化与反序列化有一定开销，更新属性时需要把userInfo全取出来进行反序列化，更新后再序列化到redis

##### **hash存储:**

hmset user:1 name james age 23 sex boy

增加年龄：`hincrby user:1 age 2`

修改姓名：`hset user:1 name lomont`

**优点:**简单直观，使用合理可减少内存空间消耗

### List使用场景

**![image-20201210204058248](../img/image-20201210204058248.png)**

可以通过`brpop`实现阻塞队列

**场景一：最新上架商品**

比如对新上架的产品进行推荐模块，这个模块存储新上架的前50名；

因为 list 结构的数据查询两端附近的数据性能非常好，可以使用list来进行top 50，`ltrim` 指令对一个列表进行修剪（trim）；

**场景二：一对多分析**

<img src="../img/6BFF6025B1CC3929BE7573DC15FAAAF1.jpg" alt="img" style="zoom: 25%;" />

**一个用户订阅了多个订阅号**：当一个一个订阅号发送了一个消息给用户时，`lpush mes:uId 100 101 102`

执行`lrange mes:uId 0  -1` 查出所有消息ID 通过**异步**的方式加载图片等信息

**场景三：分页**

比如博客文章中使用`lrange`进行快速分页，当你下拉时，进行不断的分页；

### set使用场景

set提供去重的功能，要求列表里面元素不重复，并且提供交集、并集、差集。

**场景一：抽奖活动**

![image-20201210212547070](../img/image-20201210212547070.png)

一个用户参加一次，每有一个用户就放入set: **sadd act:001 u01 u02 u03 u04 u05**

![image-20201210212908038](../img/image-20201210212908038.png)

**场景二：利用交集差集等**

比如QQ的共同好友，可能认识的人 --- 或者推荐系统等

### Zset集合

常用于排行榜，如视频网站需要对用户上传视频做排行榜，或点赞数与集合有联系，不能有重复的成员

![](https://upload-images.jianshu.io/upload_images/11474088-ecdd42729adb0e6f.png?imageMogr2/auto-orient/strip|imageView2/2/w/434/format/webp)

**场景一：排行榜系统**

<img src="../img/image-20201211100118045.png" alt="image-20201211100118045" style="zoom: 50%;" />

日期作为key，如 `topic:20201211` ,

![image-20201211100828047](../img/image-20201211100828047.png)

![image-20201211100932638](../img/image-20201211100932638.png)

### BitMap

位图，支持用bit位来存储信息，可以用来实现**布隆过滤器（BloomFilter）**；

**场景一：登录在线**

上亿用户的去重登录打卡统计，查询某用户是否在线；

### 总结

1. 缓存：合理的利用缓存不仅能够提升网站访问速度，还能大大降低数据库的压力。Redis提供了键过期功能，也提供了灵活的键淘汰策略；
2. 排行榜 --- zset
3. 计数器 ： 高访问量的网站每次浏览都要对商品，浏览量等+1，如果每次都请求数据库肯定压力很大，通过Redis的incrby实现基于内存操作性能很好
4. 分布式会话
5. 分布式锁
6. 社交网络：点赞，共同爱好，好友等  ---  set
7. 最新列表 :  list

