## 环境搭配

```xml
<?xml version="1.0" encoding="UTF-8"?>
<project xmlns="http://maven.apache.org/POM/4.0.0"
         xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:schemaLocation="http://maven.apache.org/POM/4.0.0 http://maven.apache.org/xsd/maven-4.0.0.xsd">
    <parent>
        <artifactId>mybatis-study</artifactId>
        <groupId>org.example</groupId>
        <version>1.0-SNAPSHOT</version>
    </parent>
    <modelVersion>4.0.0</modelVersion>

    <artifactId>mybatis-01</artifactId>
</project>
```



```xml
//Maven
<build>
    <resources>
        <resource>
            <directory>src/main/java</directory>
            <includes>
                <include>**/*.properties</include>
                <include>**/*.xml</include>
            </includes>
            <filtering>false</filtering>
        </resource>
        <resource>
            <directory>src/main/resources</directory>
            <includes>
                <include>**/*.properties</include>
                <include>**/*.xml</include>
            </includes>
            <filtering>false</filtering>
        </resource>
    </resources>
</build>
```



## CRUD

如果不设置手动提交事务，那么默认每条插入语句都是一个事务，每次都要提交事务。设置手动提交事务的话，可以在循环前开启事务，循环结束后再提交事务，只需要提交一次事务。

```java
//JDBC中手动关闭事务
connection.setAutoCommit(false);
for(1~1e8);
//将自动提交开启（才会在数据库中刷新，也就是成功插入）
connection.setAutoCommit(true);
//这样可以节约大量的时间
```

**事务：指一组操作要么执行成功，要么执行失败；**

1. 在所有操作未完成之前，其他回话是不能看到中间过程的；

   提交事务：`conn.commit();`

   出现异常执行回滚：`conn.rollback();`

### 1、编写接口

```java
public interface UserDao {
    //查询所有用户
    public List<User> getUserList();

    //插入用户
    public int insertUser(User user);

    //更新用户
    public int updateUser(User user);

    //根据id查询用户
    public User queryUser(int id);

    //删除用户
    public int deleteUser(int id);
}
```



### 2、写入对应SQL语句到`UserMapper.xml`中

```xml
//一个namespace对应一个mapper，代表其中的id不可相同
<mapper namespace="com.zhu.dao.UserDao">
    <select id="getUserList" resultType="com.zhu.pojo.User">
        select * from mybatis.user
    </select>

    <select id="queryUser" resultType="com.zhu.pojo.User">
        select * from mybatis.user where id = #{id}
    </select>

    <insert id="insertUser">
        insert into user(id, name, pwd) values (#{id},#{name},#{pwd})
    </insert>

    <update id="updateUser" parameterType = "com.zhu.pojo.User">
        update user set name = #{name}, pwd = #{pwd}  where id = #{id};
    </update>

    <delete id="deleteUser" parameterType = "int">
        delete from user where id = #{id}
    </delete>
</mapper>
```

### 3、测试

每一次调用`SqlSession`要记得`SqlSession.close()`

对于增删改，要提交事务`Sqlsession.commit()`

```java
public class UserDaoTest {
    @Test
    public void test(){
        //获取sqlSession对象
        SqlSession sqlSession = MybatisUtils.getSqlSession();

        UserDao userDao = sqlSession.getMapper(UserDao.class);
        List<User> userList = userDao.getUserList();
        for (User user : userList) {
            System.out.println(user);
        }
        sqlSession.close();
    }

    @Test
    public void insert(){
        SqlSession sqlSession = MybatisUtils.getSqlSession();
        UserDao userDao = sqlSession.getMapper(UserDao.class);

        int res = userDao.insertUser(new User(5, "快落", "12333"));

        sqlSession.commit();
        sqlSession.close();
    }
    @Test
    public void update(){
        SqlSession sqlSession = MybatisUtils.getSqlSession();
        UserDao mapper = sqlSession.getMapper(UserDao.class);
        mapper.updateUser(new User(5,"coco","45678"));

        sqlSession.commit();

        sqlSession.close();
    }
    @Test
    public void queryUser(){
        SqlSession sqlSession = MybatisUtils.getSqlSession();
        UserDao mapper = sqlSession.getMapper(UserDao.class);

        User user = mapper.queryUser(1);

        System.out.println(user);

        sqlSession.close();
    }
    @Test
    public void deleteUser(){
        SqlSession sqlSession = MybatisUtils.getSqlSession();

        UserDao mapper = sqlSession.getMapper(UserDao.class);

        mapper.deleteUser(4);

        sqlSession.commit();
        sqlSession.close();
    }
}
```



```xml
<?xml version="1.0" encoding="UTF-8" ?>
<!DOCTYPE configuration
        PUBLIC "-//mybatis.org//DTD Config 3.0//EN"
        "http://mybatis.org/dtd/mybatis-3-config.dtd">
<configuration>
    <properties resource="db.properties"/>

    <environments default="development">
        <environment id="development">
            <!-- 事务管理器（transactionManager） type=”[JDBC|MANAGED]-->
            <transactionManager type="JDBC"/>
            <!-- 数据源（dataSource） type=”[UNPOOLED|POOLED|JNDI] 没有池，有池，和连接，池的作用就是快一些-->
            <dataSource type="POOLED">
                <property name="driver" value="${driver}"/>
                <property name="url" value="${url}"/>
                <property name="username" value="${username}"/>
                <property name="password" value="${password}"/>
            </dataSource>
        </environment>
    </environments>
    <!-- 对于每一个 UserMapper.xml都要在核心文件中配置，resource = 资源路径 用 / 隔开-->
    <mappers>
        <mapper resource="com/zhu/dao/UserMapper.xml"/>
    </mappers>
</configuration>
```



## 其他配置

在核心资源中可配置`properties文件`

```properties
driver=com.mysql.jdbc.Driver
url=jdbc:mysql://localhost:3306/mybatis?serverTimezone=UTC&useUnicode=true&characterEncoding=UTF-8
username=root
password=root
```

有默认顺序，即`properties`标签必须首位

在核心文件中导入

```xml
<properties resource="db.properties"></properties>
```

![image-20200210215011797](C:\Users\Administrator\AppData\Roaming\Typora\typora-user-images\image-20200210215011797.png)

为实体类`（pojo/entity)`取别名

```xml
<typeAliases>
    <typeAlias type="com.zhu.pojo.User" alias="user"/>
</typeAliases>
```

指定一个包，在改包下寻找`JavaBean`，默认用类首字母小写为别名，若想取花里胡哨别名，可用`@Alias("")`注解改别名

```xml
<typeAliases>
    <package name="com.zhu.pojo"/>
</typeAliases>
```



#### 映射器(mappers)

MapperRegistry:注册绑定我们的Mapper文件：

1、类路径资源

```xml
<mappers>
    <mapper resource="com/zhu/dao/UserMapper.xml"/>
</mappers>
```



  2、使用class文件绑定注册

```xml
<mappers>
    <mapper class="com.zhu.dao.UserDao"/>
</mappers>
```

注意点：

+ 接口和Mapper配置文件必须同名
+ 接口和Mapper配置文件必须在同一个包下！

3、使用扫描包进行注入绑定

```xml
<mappers>
    <package name="com.zhu.dao"/>
</mappers>
```

注意点：

+ 接口和Mapper配置文件必须同名
+ 接口和Mapper配置文件必须在同一个包下



## 生命周期和作用域

![image-20200211152136774](C:\Users\Administrator\AppData\Roaming\Typora\typora-user-images\image-20200211152136774.png)

生命周期和作用域至关重要，因为错误的使用会导致严重的**并发**问题

**SqlSessionFactoryBuilder**

+ 一旦创建SqlSessionFactory就不再需要它了。
+ 局部变量。

**SqlSessionFactory**

+ 可以想象成 --> 数据库连接池
+ SqlSessionFactory一旦被创建就应该在应用的运行期间一直存在，**没有任何理由丢弃它或者重新创建另一个实例**
+ 因为SqlSessionFactory的最佳作用域是应用作用域
+ 最简单就是使用单例模式，或者静态单例模式。

**SqlSession**

+ 连接到连接池的一个请求!

+ SqlSession的实例不是线程安全的，因此不能被共享，它的最佳作用域是在方法中，也就是请求或者方法作用域

+ 用完之后要及时关闭，否则资源被占用。

  

每一个Mapper相当于一个业务。

映射集`ResultMap`

解决字段名和映射名不一致问题：

```xml
<resultMap id="qid" type="com.zhu.pojo.User">
    <result property="password" column="pwd"/>
</resultMap>
<select id="queryUser" resultMap="qid">
    select * from mybatis.user where id = #{id}
</select>
```



## 日志

+ SLF4J | LOG4J | LOG4J2 | JDK_LOGGING | COMMONS_LOGGING | STDOUT_LOGGING | NO_LOGGING

<img src="C:\Users\Administrator\AppData\Roaming\Typora\typora-user-images\image-20200211170715903.png" alt="image-20200211170715903" style="zoom:150%;" />

日志名：logImpl, 默认是STDOUT_LOGGING。

<img src="C:\Users\Administrator\AppData\Roaming\Typora\typora-user-images\image-20200211170954117.png" alt="image-20200211170954117" style="zoom:200%;" />

```xml
<settings>
    <setting name="logImpl" value="STDOUT_LOGGING"/>
</settings>

```

#### 1、Log4j

导包：

```xml
<dependency>
    <groupId>log4j</groupId>
    <artifactId>log4j</artifactId>
    <version>1.2.17</version>
</dependency>
```

配置`log4j.properties文件`

```properties
#将等级为DEBUG的日志信息输出到console和file这两个目的地，console和file的定义在下面的代码
log4j.rootLogger=DEBUG,console,file

#控制台输出的相关设置
log4j.appender.console = org.apache.log4j.ConsoleAppender
log4j.appender.console.Target = System.out
log4j.appender.console.Threshold=DEBUG
log4j.appender.console.layout = org.apache.log4j.PatternLayout
log4j.appender.console.layout.ConversionPattern=[%c]-%m%n

#文件输出的相关设置
log4j.appender.file = org.apache.log4j.RollingFileAppender
log4j.appender.file.File=./log/kuang.log
log4j.appender.file.MaxFileSize=10mb
log4j.appender.file.Threshold=DEBUG
log4j.appender.file.layout=org.apache.log4j.PatternLayout
log4j.appender.file.layout.ConversionPattern=[%p][%d{yy-MM-dd}][%c]%m%n

#日志输出级别
log4j.logger.org.mybatis=DEBUG
log4j.logger.java.sql=DEBUG
log4j.logger.java.sql.Statement=DEBUG
log4j.logger.java.sql.ResultSet=DEBUG
log4j.logger.java.sql.PreparedStatement=DEBUG
```

`static Logger logger = Logger.getLogger(UserDaoTest.class);`



## 利用Map进行分页查询

**`parameterMap` 已经被废弃**

```xml
<select id="queryPage" resultMap="qid" parameterType="map">
    select * from user limit #{startIndex},#{pageSize}
</select>
```

Dao层`public List<User> queryPage(Map<String,Integer> map);`

测试方法

```java
@Test
public void page(){
    SqlSession sqlSession = MybatisUtils.getSqlSession();
    UserDao mapper = sqlSession.getMapper(UserDao.class);
    Map<String,Integer> map = new HashMap<String,Integer>();
    map.put("startIndex",0);
    map.put("pageSize",2);

    List<User> list = mapper.queryPage(map);
    for (User user : list) {
        System.out.println(user);
    }
    sqlSession.close();
}
```

## 注解

接口中使用注解

```java
@Select("select * from user")
List<User> getUsers();
```

核心配置文件

```xml
<mappers>
    <mapper class="com.zhu.dao.UserMapper"/>
</mappers>
```

测试类：

```java
@Test
public void queryUsersTest(){
    SqlSession sqlSession = MybatisUtils.getSqlSession();
    UserMapper mapper = sqlSession.getMapper(UserMapper.class);
    List<User> users = mapper.getUsers();
    for (User user : users) {
        System.out.println(user);
    }
    sqlSession.close();
}
```

**CAUD**

只需要在接口上配置注解即可，不用配置Xml,但是遇到参数与属性不一致时，不能用resultMap修改了（暂时没学）。

接口层：配好注解可以直接测试

```JAVA
@Select("select * from user")
List<User> getUsers();

@Select("select * from user where id = #{id}")
User getUserById(@Param("id") int id);

@Insert("insert into user (id,name,pwd) values(#{id},#{name},#{password})")
int addUser(User user);

@Update("update user set name = #{name}, pwd = #{password} where id = #{id}")
int updateUser(User user);

@Delete("delete from user where id = #{id}")
int deleteUser(@Param("id") int id);
```

## 多表连接查询

#### 1、一对多

xml的嵌套，`<association>`标签代表 对象，`<collection>`代表集合

`property` 代表当前`type`（对象） 里的 属性，而 `column`代表数据库中的列名，将其一一对应；

嵌套`<association>`同理，将新的对象中的属性，与列名对应起来；

**Student属性：**

```java
public class Student {
    private int id;
    private String name;
    private Teacher teacher;
}
```



```xml
<select id="getStudent" resultMap="StuTeacher">
    select s.id sid, s.name sname, t.id tid,t.name tname
    from student s,teacher t
    where s.tid = t.id
</select>

<resultMap id="StuTeacher" type="com.zhu.pojo.Student">
    <result property="id" column="sid"/>
    <result property="name" column="sname"/>
    <association property="teacher" javaType="com.zhu.pojo.Teacher">
        <result property="id" column="tid"/>
        <result property="name" column="tname"/>
    </association>
</resultMap>
```



方法二：嵌套查询

```xml
<select id="getStudent2" resultMap="StuTeacher2">
    select * from student
</select>
<resultMap id="StuTeacher2" type="com.zhu.pojo.Student">
    <result property="id" column="id"/>
    <result property="name" column="name"/>
    <association property="teacher" column="tid" javaType="com.zhu.pojo.Teacher" select="getTeacher"/>
</resultMap>

<select id="getTeacher" resultType="com.zhu.pojo.Teacher">
    select * from teacher where id = #{id}
</select>
```

#### 2、多对一

`<collection>`集合的应用，其标签`javaType`相当于该集合的类型，而`ofType`则是该集合的泛型。

**Teacher属性:**

```java
public class Teacher {
    private int id;
    private String name;
    private List<Student> students;
}
```



```xml
<!-- 方法一：复杂sql,按结果嵌套查询-->
<select id="queryTeacher" resultMap="TeaStudent">
    select s.id sid, s.name sname,t.name tname,t.id tid
    from student s, teacher t
    where s.tid = t.id and t.id = #{id}
</select>

<resultMap id="TeaStudent" type="com.zhu.pojo.Teacher">
    <result property="id" column="tid"/>
    <result property="name" column="tname"/>
    <collection property="students" ofType="com.zhu.pojo.Student">
        <result property="id" column="sid"/>
        <result property="name" column="sname"/>
        <result property="tid" column="tid"/>
    </collection>
</resultMap>
```



```xml
<!-- 方法二： 复杂xml  -->

<select id="queryTeacher2" resultMap="TeaStudent2">
    select * from teacher where id = #{id};
</select>

<resultMap id="TeaStudent2" type="com.zhu.pojo.Teacher">
    <collection property="students" javaType="ArrayList" ofType="com.zhu.pojo.Student" select="get" column="id"></collection>
</resultMap>

<select id="get" resultType="com.zhu.pojo.Student">
    select * from student where tid = #{id}
</select>
```

## 动态SQL

UUID类使用

```java
public class IDtils {
    public static String getId(){
        return UUID.randomUUID().toString().replaceAll("-","");
    }
}
```

遇到属性名，与数据库列名不一致时，可开启自动驼峰命名规则（camel case）映射。

```xml
<settings>
    <setting name="logImpl" value="STDOUT_LOGGING"/>
    <setting name="mapUnderscoreToCamelCase" value="true"/>
</settings>
```

`<where>`标签，相当于在SQL中添加where，如果没有if成立，则不会自动添加，如果第一个条件开头为and 或者or ，则会省略该and 或者 or；

#### 1、if语句

```xml
<select id="queryBlogIf" resultType="com.zhu.pojo.Blog" parameterType="map">
    select * from blog
    <where>
        <if test="title != null">
            and title = #{title}
        </if>
        <if test="author != null">
            and author = #{author}
        </if>
    </where>
</select>
```

#### 2、choose语句：

```xml
<select id="queryBlogIf" resultType="com.zhu.pojo.Blog" parameterType="map">
    select * from blog
    <where>
        <choose>
            <when test="title != null">
                title = #{title}
            </when>
            <when test="author != null">
                and author = #{author}
            </when>
            <otherwise>
                view = #{view}
            </otherwise>
        </choose>
    </where>
</select>
```

#### 3、set

```xml
<update id="updateBlog" parameterType="map">
    update blog
    <set>
        <if test="title != null">
            title = #{title},
        </if>
        <if test="author != null">
            author = #{author}
        </if>
    </set>
    where id = #{id}
</update>
```



#### 4、使用SQL抽取公共部分

在其他地方使用，`<include>`标签引用

```xml
<sql id="update1">
    <if test="title != null">
        title = #{title},
    </if>
    <if test="author != null">
        author = #{author}
    </if>
</sql>
<update id="updateBlog" parameterType="map">
    update blog
    <set>
        <include refid="update1"></include>
    </set>
    where id = #{id}
</update>
```

注意事项：

+ 最好基于单表定义SQL片段
+ 不要存在where标签

#### 4、forEach

```xml
<select id="queryBlogForEach" parameterType="map" resultType="com.zhu.pojo.Blog">
    select * from mybatis.blog
    <where>
        id in
        <foreach collection="ids" item="id" open="(" separator="," close=")">
            #{id}
        </foreach>
    </where>
</select>
```

`collection`代表传进来的建，`item`为其元素名，`open`以什么开头，`separator`以什么分割，`close`以什么结束

完整SQL,`select * from mybatis.blog where id in (1,2,3)`

该测试类

```java
public void queryByForEach(){
    SqlSession sqlSession = MybatisUtils.getSqlSession();
    BlogMapper mapper = sqlSession.getMapper(BlogMapper.class);
    List ids = new ArrayList<Integer>();
    ids.add(1);ids.add(2);ids.add(3);
    HashMap<String,List> map = new HashMap<>();

    map.put("ids",ids);

    List<Blog> blogs = mapper.queryBlogForEach(map);
    for (Blog blog : blogs) {
        System.out.println(blog);
    }
    sqlSession.close();
}
```

**1. 动态SQL就是在拼接SQL语句，我们只要保证SQl的正确性，按照SQL格式去排列组合即可**

**2 .所谓动态SQl本质还是SQl语句，只是我们在SQL层面可以去执行一个逻辑代码**

## 缓存

#### 一级缓存

**一级缓存：一级缓存是Session会话级别的缓存，位于表示一次数据库会话的SqlSession对象之中，又被称之为本地缓存。一级缓存是MyBatis内部实现的一个特性，用户不能配置，默认情况下自动支持的缓存，用户没有定制它的权利（不过这也不是绝对的，可以通过开发插件对它进行修改）；**

![image-20200212214304868](C:\Users\Administrator\AppData\Roaming\Typora\typora-user-images\image-20200212214304868.png)

测试代码:

```ja
@Test
public void queryTest(){
SqlSession sqlSession = MybatisUtils.getSqlSession();
UserMapper mapper = sqlSession.getMapper(UserMapper.class);

User user = mapper.queryById(1);
System.out.println(user);
System.out.println("==========================");
User user1 = mapper.queryById(1);
System.out.println(user == user1);
sqlSession.close();
}
```



当添加增查改时： 答案为false

![image-20200212214637222](C:\Users\Administrator\AppData\Roaming\Typora\typora-user-images\image-20200212214637222.png)



```java
public void queryTest(){
    SqlSession sqlSession = MybatisUtils.getSqlSession();
    UserMapper mapper = sqlSession.getMapper(UserMapper.class);

    User user = mapper.queryById(1);
    System.out.println(user);
    System.out.println("==========================");
    mapper.updateUser(new User(4,"wantobg","666666"));
    User user1 = mapper.queryById(1);
    System.out.println(user == user1);
    sqlSession.close();
}
```

**执行增删查改后，数据库的数据改变，可能会刷新缓存**

缓存失效情况

1. 查询不同的东西
2. 执行增删改，可能改变原来的数据，必定刷新缓存
3. 查询不同的Mapper.xml
4. 手动清理缓存`sqlSession.cleanCache()`

#### 二级缓存

二级缓存： 相当于在一个namespace(全局缓存)

工作机制

+ 一个会话查询一条数据，这个数据会被放在当前的一级缓存中；
+ 如果当前会话关闭，这个会话对应的一级缓存就没了；
+ 但是我们想要的是，会话关闭，一级缓存中的数据被保存到二级缓存中；
+ 新的会话查询信息，就可以从二级缓存中获取内容；
+ 不同的mapper查出的数据会放在自己对应的缓存(map)中；

`<cache/>`在xml中配置全局缓存。

步骤：

1. 核心文件中配置缓存`<setting name="cacheEnabled" value="true"/>`

2. Mapper.xml中开启缓存

   **这些参数要配，不然出错，注解由于不需要xml，所有无法开启二级缓存**

   `

   ```
   <cache eviction="FIFO"
           flushInterval="20"
           size="520"
           readOnly="true"/>
   ```

   `

`org.apache.ibatis.cache.CacheException: Error serializing object.  Cause: java.io.NotSerializableException: com.zhu.pojo.User`**二级缓存要记得序列化**

![image-20200212222234799](C:\Users\Administrator\AppData\Roaming\Typora\typora-user-images\image-20200212222234799.png)

小结：

+ 只有开启了二级缓存，在同一个Mapper下才有效
+ 所有的数据会先放入一级缓存中
+ 只有当会话提交，或者关闭的时候，才会提交到二级缓存中