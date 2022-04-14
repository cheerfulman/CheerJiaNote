

## spring

**Spring是一个轻量级的控制反转(IoC)和面向切面(AOP)的容器（框架）。**

Spring 框架是一个分层架构，由 7 个定义良好的模块组成。Spring 模块构建在核心容器之上，核心容器定义了创建、配置和管理 bean 的方式 .

+ 核心容器：核心容器提供 Spring 框架的基本功能。核心容器的主要组件是 BeanFactory，它是工厂模式的实现。BeanFactory 使用控制反转（IOC） 模式将应用程序的配置和依赖性规范与实际的应用程序代码分开。

+ Spring 上下文：Spring 上下文是一个配置文件，向 Spring 框架提供上下文信息。Spring 上下文包括企业服务，例如 JNDI、EJB、电子邮件、国际化、校验和调度功能。

+ Spring AOP：通过配置管理特性，Spring AOP 模块直接将面向切面的编程功能 , 集成到了 Spring 框架中。所以，可以很容易地使 Spring 框架管理任何支持 AOP的对象。Spring AOP 模块为基于 Spring 的应用程序中的对象提供了事务管理服务。通过使用 Spring AOP，不用依赖组件，就可以将声明性事务管理集成到应用程序中。


+ Spring DAO：JDBC DAO 抽象层提供了有意义的异常层次结构，可用该结构来管理异常处理和不同数据库供应商抛出的错误消息。异常层次结构简化了错误处理，并且极大地降低了需要编写的异常代码数量（例如打开和关闭连接）。Spring DAO 的面向 JDBC 的异常遵从通用的 DAO 异常层次结构。

+ Spring ORM：Spring 框架插入了若干个 ORM 框架，从而提供了 ORM 的对象关系工具，其中包括 JDO、Hibernate 和 iBatis SQL Map。所有这些都遵从 Spring 的通用事务和 DAO 异常层次结构。

+ Spring Web 模块：Web 上下文模块建立在应用程序上下文模块之上，为基于 Web 的应用程序提供了上下文。所以，Spring 框架支持与 Jakarta Struts 的集成。Web 模块还简化了处理多部分请求以及将请求参数绑定到域对象的工作。

+ Spring MVC 框架：MVC 框架是一个全功能的构建 Web 应用程序的 MVC 实现。通过策略接口，MVC 框架变成为高度可配置的，MVC 容纳了大量视图技术，其中包括 JSP、Velocity、Tiles、iText 和 POI。

**IOC:**
**控制反转IoC(Inversion of Control)，是一种设计思想，DI(依赖注入)是实现IoC的一种方法**，也有人认为DI只是IoC的另一种说法。没有IoC的程序中 , 我们使用面向对象编程 , 对象的创建与对象间的依赖关系完全硬编码在程序中，对象的创建由程序自己控制，控制反转后将对象的创建转移给第三方，个人认为所谓控制反转就是：获得依赖对象的方式反转了。
![](https://blog.kuangstudy.com/usr/uploads/2019/10/71929266.png)

**IoC是Spring框架的核心内容**，使用多种方式完美的实现了IoC，可以使用XML配置，也可以使用注解，新版本的Spring也可以零配置实现IoC

控制反转是一种通过描述（XML或注解）并通过第三方去生产或获取特定对象的方式。在Spring中实现控制反转的是IoC容器，其实现方法是依赖注入（Dependency Injection,DI）。


spring中.xml的配置
```java
    <bean id = "Person" class="com.kuang.entity.Person">
<!--        <constructor-arg index="0" value="阿朱"></constructor-arg>-->
<!--        <constructor-arg index="1" value="18"></constructor-arg>-->
<!--        <constructor-arg index="2" value="male"></constructor-arg>-->
<!--        <constructor-arg name="name" value="啊猪猪"></constructor-arg>-->
<!--        <constructor-arg name="age" value="21"></constructor-arg>-->
<!--        <constructor-arg name="sex" value="male"></constructor-arg>-->
        <constructor-arg type="java.lang.String" value="啊朱"></constructor-arg>
        <constructor-arg type="int" value="20"></constructor-arg>
        <constructor-arg type="java.lang.String" value="male"></constructor-arg>
    </bean>
```

可以取别名：`<bean id = "Person" class="com.kuang.entity.Person" name = "别名">`

导入.xml文件 '<import resouce = "beans.xml">'

## **DI依赖注入：**

+ 依赖注入：Set注入
  + 依赖：bean对象的创建 依赖于容器
  + 注入：bean对象中的所有属性，由容器来注入

各种类型的配置--> `ref`(引用)

```xml
<bean id = "Person" class="com.kuang.entity.Person">
    <property name="name">
        <null></null>
    </property>
    <property name="address" ref="add"></property>
    <property name="style">
        <array>
            <value>简约</value>
            <value>大气</value>
            <value>上档次</value>
        </array>
    </property>
    <property name="hobbys">
        <list>
            <value>吃饭</value>
            <value>睡觉</value>
            <value>打豆豆</value>
        </list>
    </property>
    <property name="card">
        <map>
            <entry key="工商" value="140140"></entry>
            <entry key="中国" value="140148"></entry>
            <entry key="农业" value="140190"></entry>
        </map>
    </property>
    <property name="game">
        <set>
            <value>风在吼</value>
            <value>kk</value>
            <value>kk</value>
        </set>
    </property>
    <property name="info">
        <props>
            <prop key="username">root</prop>
            <prop key="password">123456</prop>
        </props>
    </property>
</bean>
```

P命名和c命名：
1. 导入p命名空间：（注入属性）
    + 引入`xmlns:p="http://www.springframework.org/schema/p"`
    `<bean id = "Person" class="com.kuang.entity.Person" p:name="123">`
2. 导入c命名：（注入构造器）
    + 引入`xmlns:c="http://www.springframework.org/schema/c"`
    `<bean id="add" class="com.kuang.entity.Address" c:address="中国">`

**默认 scope = 'singleton' (单例模式)： 单例，全局取出来的是一个Class**
**scope = 'prototype' (原型模式)： 每次从容器中get时，都会产生一个新的对象**

其余 request,session,application，websocket只在web开发中使用；

`autowire = "byName"` : 会自动在容器上下文查找，和自己对象set方法后面值对应的beanid,**id不同则不行**；

`autowire = "byType"` : 会自动在容器上下文查找，和自己对象属性类型相同的bean，**class有多个则不行**；

注解实现自动装配:
添加 ： `xmlns:context="http://www.springframework.org/schema/context"` 和 

```
http://www.springframework.org/schema/context
https://www.springframework.org/schema/context/spring-context.xsd
```
还有 `<context:annotation-config/>`



**@Autowired : 在属性上使用即可，可以不要Setter方法，可以定义`@Autowired(required = "false")`即可以为Null，默认通过byType方法实现，要求此对象必须存在;**

```java
@Target({ElementType.CONSTRUCTOR, ElementType.METHOD, ElementType.PARAMETER, ElementType.FIELD, ElementType.ANNOTATION_TYPE})
@Retention(RetentionPolicy.RUNTIME)
@Documented
public @interface Autowired {
    boolean required() default true;
}
```
`@Qualifier(value = "")` 可配合 `@Autowired`使用 即差不多。
@Resource : 默认通过byName方法实现，如果找不到名字则通过byType，如果找不到id且多个相同type，则报错。



## 注解

@Component : 组件，说明这个类被Spring管理了，就是bean。

```java
//相当于<bean id = user class = "com.zhu.pojo.User/>
//@Component 组件
@Component
public class User {
    @Value("猪猪")
    public String name;

    @Override
    public String toString() {
        return "User{" +
            "name='" + name + '\'' +
            '}';
    }
}
```

@Value() ： 注入属性。

```java
@Value("猪猪")
public String name;
```

**测试结果：**`User{name='猪猪'}`

**指定扫描的注解：**
**`<context:component-scan base-package="com.zhu">`**
**`<context:annotation-config/>`**

`@Configuration` : 相当于一个配置类（beans.xml）。
`@Bean : `相当于之前的一个bean标签，这个方法的名字相当于id，返回值相当于class属性



@Component衍生注解

+ dao【@Repository】

+ service 【@Service】

+ controller【@Controller】

  这四个注解功能一样，都代表注册到spring中，装配Bean

`@Configuration`:配置类，相当于一个applicationContext.xml

AOP:面向切面

![image-20200213225820166](C:\Users\Administrator\AppData\Roaming\Typora\typora-user-images\image-20200213225820166.png)





## 动态代理 

接口：

```java
public interface Rent {
    public void rent();

    public void add();

    public void delete();

    public void rise();
}
```

真实实现类：

```java
public class RentImpl implements Rent{

    @Override
    public void rent() {
        System.out.println("RentImpl: 出租啦");
    }

    @Override
    public void add() {
        System.out.println("RentImpl: 增加出租房啦");
    }

    @Override
    public void delete() {
        System.out.println("RentImpl: 删除出租房啦");
    }

    @Override
    public void rise() {
        System.out.println("RentImpl: 出租房涨价啦");
    }
}
```

动态生产代理工具

```java
public class ProxyInvocationHandler implements InvocationHandler {

    public Object target;


    public void setTarget(Object obj){
        this.target = obj;
    }

    public Object getProxy(){
        return Proxy.newProxyInstance(this.getClass().getClassLoader(),target.getClass().getInterfaces()
                                      ,this);
    }

    @Override
    public Object invoke(Object o, Method method, Object[] objects) throws Throwable {
        System.out.println("执行了" + method.getName() + "方法");
        Object result = method.invoke(target,objects);
        return null;
    }
}
```

消费者(Client)：

```java
public class Client {
    public static void main(String[] args) {
        RentImpl ri = new RentImpl();
        ProxyInvocationHandler pih = new ProxyInvocationHandler();

        pih.setTarget(ri); //跟网上的 构造方法一样，设置 自己 要代理的接口

        Rent proxy = (Rent)pih.getProxy(); //得到代理类

        proxy.delete(); //执行方法
    }
}
```

网上查阅了些许资料，但是还是不明白其newProxyInstance(),和invoke()方法，待日后定重新查看源码。

使用spring的API接口

```java
public class Log implements MethodBeforeAdvice {
    @Override
    public void before(Method method, Object[] objects, Object o) throws Throwable {
        System.out.println(o.getClass().getName() + "的" + method.getName() + "方法被执行");
    }
}
```

```java
public class AfterLog implements AfterReturningAdvice {

    @Override
    public void afterReturning(Object o, Method method, Object[] objects, Object o1) throws Throwable {
        System.out.println("执行了" + method.getName() + "返回结果为" + o);
    }
}
```

Xml配置

```xml
<bean id="log" class="com.log.Log"/>
<bean id ="afterLog" class="com.log.AfterLog"/>
<bean id="rent" class="com.zhu.RentImpl"/>

<aop:config>
    <aop:pointcut id="pointcut" expression="execution(* com.zhu.RentImpl.*(..))"/>

    <!--增加环绕 -->
    <aop:advisor advice-ref="log" pointcut-ref="pointcut"/>
    <aop:advisor advice-ref="afterLog" pointcut-ref="pointcut"/>
</aop:config>
```

切面xml文档：切面就是一个类

```xml
<aop:config>
    <!-- 切面-->
    <aop:aspect ref="diy">
        <aop:pointcut id="pointcut" expression="execution(* com.zhu.RentImpl.*(..))"/>
        <aop:before method="before" pointcut-ref="pointcut"/>
        <aop:after method="after" pointcut-ref="pointcut"/>
    </aop:aspect>
</aop:config>
```

使用注解实现:

```java
@Aspect
public class Diy {
    @Before("execution(* com.zhu.RentImpl.*(..))")
    public void before(){
        System.out.println("方法之前输出==================");
    }
    @After("execution(* com.zhu.RentImpl.*(..))")
    public void after(){
        System.out.println("==================方法之后输出");
    }
}
```

## spring整合mybatis

在spring中可配置 连接数据库的信息，通过`SqlSessionFactoryBean`获取`sqlSessionFactory`

通过`SqlSessionTemplate`代替原来的`sqlSession`

```xml
<?xml version="1.0" encoding="UTF-8"?>
<beans xmlns="http://www.springframework.org/schema/beans"
       xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
       xmlns:aop="http://www.springframework.org/schema/aop"
       xsi:schemaLocation="http://www.springframework.org/schema/beans
                           https://www.springframework.org/schema/beans/spring-beans.xsd
                           http://www.springframework.org/schema/aop
                           https://www.springframework.org/schema/aop/spring-aop.xsd">

    <!--    DataSource : 使用spring 的数据源 替换Mybatis配置 c3p0 dbcp druid
        这里使用sping 提供的JDBC-->
    <bean id="dataSource" class="org.springframework.jdbc.datasource.DriverManagerDataSource">
        <property name="driverClassName" value="com.mysql.jdbc.Driver"/>
        <property name="url" value="jdbc:mysql://localhost:3306/mybatis?serverTimezone=UTC&amp;useUnicode=true&amp;characterEncoding=UTF-8&amp;useSSL=false"/>
        <property name="username" value="root"/>
        <property name="password" value="root"/>
    </bean>

    <!--    SqlSessionFactory-->
    <bean id="sqlSessionFactory" class="org.mybatis.spring.SqlSessionFactoryBean">
        <property name="dataSource" ref="dataSource" />
        <property name="configLocation" value="classpath:mybatis-config.xml"/>
        <property name="mapperLocations" value="classpath:com/zhu/dao/UserMapper.xml"/>
    </bean>

    <bean id="sqlSession" class="org.mybatis.spring.SqlSessionTemplate">
        <constructor-arg index="0" ref="sqlSessionFactory"/>
    </bean>

    <bean id="userMapper" class="com.zhu.dao.UserMapperImpl">
        <property name ="sqlSession" ref="sqlSession"/>
    </bean>
</beans>
```



mybatis配置：

注意Mybatis `<mapper resource="com/zhu/dao/UserMapper.xml"/>`

 spring 中`<property name="mapperLocations" value="classpath:com/zhu/dao/UserMapper.xml"/>`

两者不能重复配置，否则将导致：sqlSessionFactory错误，原因：重复扫描`mapper.xml`包

```xml
<?xml version="1.0" encoding="UTF-8" ?>
<!DOCTYPE configuration
        PUBLIC "-//mybatis.org//DTD Config 3.0//EN"
        "http://mybatis.org/dtd/mybatis-3-config.dtd">
<configuration>

    <typeAliases>
        <package name="com.zhu.pojo"/>
    </typeAliases>

    <!--    <mappers>-->
    <!--        <mapper resource="com/zhu/dao/UserMapper.xml"/>-->
    <!--    </mappers>-->
</configuration>
```

小结：

1. 数据源的配置 ---- （dataSource）

   ```xml
   <bean id="dataSource" class="org.springframework.jdbc.datasource.DriverManagerDataSource">
       <property name="driverClassName" value="com.mysql.jdbc.Driver"/>
       <property name="url" value="jdbc:mysql://localhost:3306/mybatis?serverTimezone=UTC&amp;useUnicode=true&amp;characterEncoding=UTF-8&amp;useSSL=false"/>
       <property name="username" value="root"/>
       <property name="password" value="root"/>
   </bean>
   ```

   

2. 配置SqlSessionFactory

   ```xml
   <!--    SqlSessionFactory-->
   <bean id="sqlSessionFactory" class="org.mybatis.spring.SqlSessionFactoryBean">
       <property name="dataSource" ref="dataSource" />
       <!--        mybatis配置文件路径-->
       <property name="configLocation" value="classpath:mybatis-config.xml"/>
       <!--        mapper.xml路径配置，注意此处配置后，在mybatis路径中将 不要配置 mapper-->
       <property name="mapperLocations" value="classpath:com/zhu/dao/UserMapper.xml"/>
   </bean>
   ```

3. 如果使用继承SqlSessionDaoSupport 的类，则无需配置`sqlSession`此类中自带有。否则需要`SqlSessionTemplate`，通过此类，完成`Mybatis`中`SqlSession`的操作

   ```xml
   <bean id="sqlSession" class="org.mybatis.spring.SqlSessionTemplate">
       <!--        只能通过构造器 注入-->
       <constructor-arg index="0" ref="sqlSessionFactory"/>
   </bean>
   ```

   或者

   ```java
   public class UserMapperImpl2 extends SqlSessionDaoSupport implements UserMapper{
       @Override
       public List<User> queryUser() {
           return getSqlSession().getMapper(UserMapper.class).queryUser();
       }
   }
   ```

`mybatis-config.xml`中只需要配置 `typeAliases`和`setting`即可。

spring： 用来注入Bean，使其简单化，只用从文件中就可以读出实例

mybatis:  一个mapper.xml可以相当于操作一个数据库，然后在mybatis.xml（核心文件）中注册。



在spring中没有配置了mapperLocations的话，在mybatis文件中只能用resource配置映射文件，用class会报错（SqlSessionFactory错误） （原因未知）；



## 声明式事务

#### 1、回顾事务

+ 把一组业务当成一个业务来做，也么都成功，要么都失败！
+ 事务在项目开发中，十分重要，涉及到数据的一致性原则问题，不能马虎！
+ 确保完整性和一致性；

**事务就是把一系列的动作当成一个独立的工作单元，这些动作要么全部完成，要么全部不起作用**

事务ACID原则：

+ 原子性 ：事务是原子性操作，由一系列动作组成，事务的原子性确保动作要么全部完成，要么完全不起作用
+ 一致性 ： 一旦所有事务动作完成，事务就要被提交。数据和资源处于一种满足业务规则的一致性状态中
+ 隔离性 ：多个业务可能操作同一个资源，防止数据损坏。可能多个事务会同时处理相同的数据，因此每个事务都应该与其他事务隔离开来，防止数据损坏

+ 持久性 ： 事务一旦提交，无论系统发生什么问题，结果都不会被影响，被持久的写到存储器中。

**使用Spring管理事务，注意头文件的约束导入 : tx**

```xml
xmlns:tx="http://www.springframework.org/schema/tx"

http://www.springframework.org/schema/tx
http://www.springframework.org/schema/tx/spring-tx.xsd">
```

配置jdbc事务

```xml
<bean id="transactionManager" class="org.springframework.jdbc.datasource.DataSourceTransactionManager">
    <property name="dataSource" ref="dataSource" />
</bean>
```

配置事务通知

```xml
<!--配置事务通知-->
<tx:advice id="txAdvice" transaction-manager="transactionManager">
    <tx:attributes>
        <!--配置哪些方法使用什么样的事务,配置事务的传播特性-->
        <tx:method name="add" propagation="REQUIRED"/>
        <tx:method name="delete" propagation="REQUIRED"/>
        <tx:method name="update" propagation="REQUIRED"/>
        <tx:method name="search*" propagation="REQUIRED"/>
        <tx:method name="get" read-only="true"/>
        <tx:method name="*" propagation="REQUIRED"/>
    </tx:attributes>
</tx:advice>
```

**spring事务传播特性：**

事务传播行为就是多个事务方法相互调用时，事务如何在这些方法间传播。spring支持7种事务传播行为：

- propagation_requierd：如果当前没有事务，就新建一个事务，如果已存在一个事务中，加入到这个事务中，这是最常见的选择。
- propagation_supports：支持当前事务，如果没有当前事务，就以非事务方法执行。
- propagation_mandatory：使用当前事务，如果没有当前事务，就抛出异常。
- propagation_required_new：新建事务，如果当前存在事务，把当前事务挂起。
- propagation_not_supported：以非事务方式执行操作，如果当前存在事务，就把当前事务挂起。
- propagation_never：以非事务方式执行操作，如果当前事务存在则抛出异常。
- propagation_nested：如果当前存在事务，则在嵌套事务内执行。如果当前没有事务，则执行与propagation_required类似的操作

配置aop进行切入

```xml
<!--配置aop织入事务-->
<aop:config>
    <aop:pointcut id="txPointcut" expression="execution(* com.kuang.dao.*.*(..))"/>
    <aop:advisor advice-ref="txAdvice" pointcut-ref="txPointcut"/>
</aop:config>
```

错误总结:

```JAVA
UserMapperImpl userMapper = context.getBean("userMapperImpl", UserMapperImpl.class);
```

项目中使用了org.aspectj.lang.annotation.Aspect切面技术，即UserMapperImpl是一个切入点，针对org.aspectj.lang.annotation.Aspect的实现原理是基于接口的动态代理技术，而不是基于实现类的动态代理技术，所以以上代码应该改为:

```JAVA
UserMapper userMapper = context.getBean("userMapperImpl", UserMapper.class);
```

小结:配置事务可以使其遵循ACID原则；AOP即使用了动态代理 是 基于接口的。