# Mysql存储引擎

咸鱼太久，今天回顾一下下Mysql的一些知识；

### mysql的架构

在Mysql中客户端是不能执行sql语句的，它要交给服务端，而服务的分为

![image-20200701154315802](C:\Users\admin\AppData\Roaming\Typora\typora-user-images\image-20200701154315802.png)

![image-20200716193624167](C:\Users\admin\AppData\Roaming\Typora\typora-user-images\image-20200716193624167.png)

## sql语句整体流程

+ 用户通过Navicat等客户端与服务器建立链接，就会进行用户名密码认证，或者SSL认证
+ 登录后，Mysql会根据角色判断对应表的权限
+ 当用户发送一条sql语句后，MySQL会先查询缓存，如果有则返回，没有则进行下面操作。update，insert,delete则不经过缓存
+ MySQL进行解析，校验，再对解析树进行查询优化，生成执行计划。
+ 使用生产的执行计划来调用存储引擎的接口，explain等，来查看是否走所有。
+ 如果拿到了结果集并且为select语句，MySQL就会将结果放入缓存中，同时返回给客户端。

参考学习：https://blog.csdn.net/qq_33774822/article/details/93885710