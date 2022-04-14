## JDBC
---
+ 加载驱动类：Class.forName("类名")；
+ 给出url、username、password,其中url背下 来
+ 使用DriverManager类来得到Connection对象！

JDBC连接出现的问题--> `The server time zone value 'xxxxx' is unreconized or represents more than one time zone`
这是由于数据库系统和系统时区的差异造成的。
解决方案在jdbc连接的url后面加上`serverTimezone=GMT`

url解析: (固定)jdbc:(厂商名称这里用Mysql)mysql:(子协议由厂商自己决定)
mysql的子协议结构：//主机：端口号/数据库名称

故完整的为：
```java
String url = "jdbc:mysql://localhost:3306/student?serverTimezone=UTC";
```

con.createStatement(int,int);
![在这里插入图片描述](https://img-blog.csdnimg.cn/20200113170431136.png)
结果集的操作：
![在这里插入图片描述](https://img-blog.csdnimg.cn/20200113164805371.png?x-oss-process=image/watermark,type_ZmFuZ3poZW5naGVpdGk,shadow_10,text_aHR0cHM6Ly9ibG9nLmNzZG4ubmV0L2Zvb2xpc2hwaWNoYW8=,size_16,color_FFFFFF,t_70)

## preparedStatement
---
+ 它是Statement接口的子接口：
+ 防SQL攻击、提高代码可读性和可维护性、效率

如何得到PreparedStatement对象：
1. 给出sql模板；
2. 调用Connection方法，得到PreparedStatement方法
3. 调用pstmt的Setxxx()为其赋值
4. 调用pstmt的executeQuery()或pstmt的executeUpdate();

## 预处理的原理：
1、 服务器工作：
+ 校验sql语句的语法
+ 编译：一个函数相似的东西
+ 执行:调用函数
2、 PreparedStatement:
+ 前提:连接的数据库必须支持预处理！几乎没有不支持的
+ 每个pstmt都与一个sql模板绑在一起，先把sql模板给数据库，数据库先进行校验，再进行编译。执行时只是把参数传递过去而已；
+ 第二次执行时，就不用再校验语法了，也不用再次编译，直接执行；

