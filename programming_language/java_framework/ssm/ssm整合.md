## 默认其jdk为1.8配置

```xml
<build> 
    <plugins> 
        <plugin> 
            <groupId>org.apache.maven.plugins</groupId> 
            <artifactId>maven-compiler-plugin</artifactId> 
            <version>3.1</version> 
            <configuration> 
                <source>1.8</source> 
                <target>1.8</target> 
            </configuration> 
        </plugin> 
    </plugins> 
</build>  
```



# SSM整合

在Mybatis中有配置一段包扫描，我们继承supportDao层的时间。

```xml
<!-- 配置扫描Dao接口的包，动态实现Dao接口，注入到Spring容器中 -->
<bean class="org.mybatis.spring.mapper.MapperScannerConfigurer">
    <!--这里是今天要说的重点-->
    <property name="sqlSessionFactoryBeanName" value="sqlSessionFactory"></property>
    <!-- 给出需要扫描的Dao接口包 -->
    <property name="basePackage" value="org.zhu.dao"></property>
</bean>
```

1. 环境pom.xml依赖：

```xml
<dependencies>
    <!--Junit-->
    <dependency>
        <groupId>junit</groupId>
        <artifactId>junit</artifactId>
        <version>4.12</version>
    </dependency>
    <!--数据库驱动-->
    <dependency>
        <groupId>mysql</groupId>
        <artifactId>mysql-connector-java</artifactId>
        <version>5.1.47</version>
    </dependency>
    <!-- 数据库连接池 -->
    <dependency>
        <groupId>com.mchange</groupId>
        <artifactId>c3p0</artifactId>
        <version>0.9.5.2</version>
    </dependency>

    <!--Servlet - JSP -->
    <dependency>
        <groupId>javax.servlet</groupId>
        <artifactId>servlet-api</artifactId>
        <version>2.5</version>
    </dependency>
    <dependency>
        <groupId>javax.servlet.jsp</groupId>
        <artifactId>jsp-api</artifactId>
        <version>2.2</version>
    </dependency>
    <dependency>
        <groupId>javax.servlet</groupId>
        <artifactId>jstl</artifactId>
        <version>1.2</version>
    </dependency>

    <!--Mybatis-->
    <dependency>
        <groupId>org.mybatis</groupId>
        <artifactId>mybatis</artifactId>
        <version>3.5.2</version>
    </dependency>
    <dependency>
        <groupId>org.mybatis</groupId>
        <artifactId>mybatis-spring</artifactId>
        <version>2.0.2</version>
    </dependency>

    <!--Spring-->
    <dependency>
        <groupId>org.springframework</groupId>
        <artifactId>spring-webmvc</artifactId>
        <version>5.1.9.RELEASE</version>
    </dependency>
    <dependency>
        <groupId>org.springframework</groupId>
        <artifactId>spring-jdbc</artifactId>
        <version>5.1.9.RELEASE</version>
    </dependency>
</dependencies>
```

2. MAVEN的资源过滤

   ```xml
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

3. BookMapper接口:(把Book全部不小心写成了User)

   ```java
   public interface UserMapper {
       //查询所有书籍
       List<Books> queryAllBook();
   
       //查询单个书籍
       Books queryBookById(@Param("bookID") int id);
   
       //添加一本书
       int addBook(Books books);
   
       //修改一本书
       int updateBook(Books books);
   
       //删除一本书
       int deleteBook(@Param("bookID")int id);
   }
   ```

4. 对应的mapper.xml

   ```xml
   <?xml version="1.0" encoding="UTF-8" ?>
   <!DOCTYPE mapper
           PUBLIC "-//mybatis.org//DTD Config 3.0//EN"
           "http://mybatis.org/dtd/mybatis-3-mapper.dtd">
   <mapper namespace="com.zhu.dao.UserMapper">
   
       <select id="queryAllBook" resultType="Books">
           select * from ssmbuild.books
       </select>
   
   
       <select id="queryBookById" resultType="books">
           select * from ssmbuild.books where bookID=#{bookID}
       </select>
   
       <insert id="addBook" parameterType="books">
           insert into ssmbuild.books (bookName, bookCounts, detail) VALUES (
           #{bookName},#{bookCounts},#{detail}
           )
       </insert>
   
   
       <update id="updateBook" parameterType="books">
           update ssmbuild.books set bookName=#{bookName},bookCounts=#{bookCounts},
           detail=#{detail} where bookID=#{bookID}
       </update>
   
       <delete id="deleteBook" parameterType="int">
           delete from ssmbuild.books where bookID=#{bookID}
       </delete>
   </mapper>
   ```

5. mybatis-config.xml的核心配置

   ```xml
   <?xml version="1.0" encoding="UTF-8" ?>
   <!DOCTYPE configuration
           PUBLIC "-//mybatis.org//DTD Config 3.0//EN"
           "http://mybatis.org/dtd/mybatis-3-config.dtd">
   <configuration>
       <typeAliases>
           <package name="com.zhu.pojo"/>
       </typeAliases>
   
   
       <mappers>
           <mapper class="com.zhu.dao.UserMapper"/>
       </mappers>
   
   </configuration>
   ```

6. spring-dao.xml的配置：

   1. 配置数据源（dataSource）
   2. sqlSessionFactory
   3. MapperScannerConfigurer : 省略创造一个类继承supportDao层的步骤

   ```xml
   <?xml version="1.0" encoding="UTF-8"?>
   <beans xmlns="http://www.springframework.org/schema/beans"
          xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
          xmlns:context="http://www.springframework.org/schema/context"
          xsi:schemaLocation="http://www.springframework.org/schema/beans
   http://www.springframework.org/schema/beans/spring-beans.xsd
   http://www.springframework.org/schema/context
   https://www.springframework.org/schema/context/spring-context.xsd">
   
   
   
       <bean id="dataSource" class="org.springframework.jdbc.datasource.DriverManagerDataSource">
           <property name="driverClassName" value="com.mysql.jdbc.Driver"/>
           <property name="url" value="jdbc:mysql://localhost:3306/ssmbuild?serverTimezone=UTC&amp;useUnicode=true&amp;characterEncoding=UTF-8&amp;useSSL=false"/>
           <property name="username" value="root"/>
           <property name="password" value="root"/>
       </bean>
   
   
       <bean id="sqlSessionFactory" class="org.mybatis.spring.SqlSessionFactoryBean">
           <property name="dataSource" ref="dataSource"/>
           <property name="configLocation" value="classpath:mybatis-config.xml"/>
       </bean>
   
   
       <bean class="org.mybatis.spring.mapper.MapperScannerConfigurer">
           <property name="sqlSessionFactoryBeanName" value="sqlSessionFactory"/>
           <property name="basePackage" value="com.zhu.dao"/>
       </bean>
   
   </beans>
   ```

7. service接口

   ```java
   public interface UserService {
       List<Books> queryAllBook();
   
       //查询单个书籍
       Books queryBookById( int id);
   
       //添加一本书
       int addBook(Books books);
   
       //修改一本书
       int updateBook(Books books);
   
       //删除一本书
       int deleteBook(int id);
   
   }
   ```

   实现类：利用 set或者 构造器，注入属性 `UserMapper`

   ```java
   public class UserServiceImpl implements UserService {
   
       private UserMapper userMapper;
   
   //    public UserServiceImpl(UserMapper userMapper) {
   //        this.userMapper = userMapper;
   //    }
   public void setUserMapper(UserMapper userMapper) {
       this.userMapper = userMapper;
   }
       
   
       public List<Books> queryAllBook() {
           return userMapper.queryAllBook();
       }
   
       public Books queryBookById(int id) {
           return userMapper.queryBookById(id);
       }
   
       public int addBook(Books books) {
           return userMapper.addBook(books);
       }
   
       public int updateBook(Books books) {
           return userMapper.updateBook(books);
       }
   
       public int deleteBook(int id) {
           return userMapper.deleteBook(id);
       }
   
   
   }
   ```

8. service层的spring配置 `sping-service`：

   ```xml
   <?xml version="1.0" encoding="UTF-8"?>
   <beans xmlns="http://www.springframework.org/schema/beans"
          xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
          xmlns:aop="http://www.springframework.org/schema/aop"
          xmlns:context="http://www.springframework.org/schema/context"
          xsi:schemaLocation="http://www.springframework.org/schema/beans
                              https://www.springframework.org/schema/beans/spring-beans.xsd
                              http://www.springframework.org/schema/aop
                              https://www.springframework.org/schema/aop/spring-aop.xsd http://www.springframework.org/schema/context https://www.springframework.org/schema/context/spring-context.xsd">
   
       <import resource="classpath:spring-dao.xml"/>
   
       <context:component-scan base-package="com.zhu.service"/>
   
       <bean id="userServiceImpl" class="com.zhu.service.UserServiceImpl">
   <!--        <constructor-arg index="0" ref="userMapper"/>-->
   
           <property name="userMapper" ref="userMapper"/>
       </bean>
   
   
       <bean id="transactionManager" class="org.springframework.jdbc.datasource.DataSourceTransactionManager">
           <property name="dataSource" ref="dataSource"/>
       </bean>
   </beans>
   ```

   

9. web.xml

   1. 核心：DispatcherServlet(前端控制器)，所有请求先经过这，通过映射器，适配器找到对应的servlet

   2. sping自带的监听过滤器，防止jsp页面乱码

      ```xml
      <?xml version="1.0" encoding="UTF-8"?>
      <web-app xmlns="http://xmlns.jcp.org/xml/ns/javaee"
               xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
               xsi:schemaLocation="http://xmlns.jcp.org/xml/ns/javaee http://xmlns.jcp.org/xml/ns/javaee/web-app_4_0.xsd"
               version="4.0">
      
      
          <servlet>
              <servlet-name>spingmvc</servlet-name>
              <servlet-class>org.springframework.web.servlet.DispatcherServlet</servlet-class>
      
              <init-param>
                  <param-name>contextConfigLocation</param-name>
                  <param-value>classpath:applicationContext.xml</param-value>
              </init-param>
              <load-on-startup>1</load-on-startup>
          </servlet>
          
          <servlet-mapping>
              <servlet-name>spingmvc</servlet-name>
              <url-pattern>/</url-pattern>
          </servlet-mapping>
          
          <filter>
              <filter-name>encodingFilter</filter-name>
              <filter-class>org.springframework.web.filter.CharacterEncodingFilter</filter-class>
              <init-param>
                  <param-name>encoding</param-name>
                  <param-value>utf-8</param-value>
              </init-param>
          </filter>
          <filter-mapping>
              <filter-name>encodingFilter</filter-name>
              <url-pattern>/*</url-pattern>
          </filter-mapping>
      
          <session-config>
              <session-timeout>15</session-timeout>
          </session-config>
      </web-app>
      ```

9. sping-mvc配置：

   1. 扫描对应包
   2. 静态资源过滤`<mvc:default-servlet-handler/>`
   3. 增加注解驱动`<mvc:annotation-driven/>`

   ```xml
   <?xml version="1.0" encoding="UTF-8"?>
   <beans xmlns="http://www.springframework.org/schema/beans"
          xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
          xmlns:context="http://www.springframework.org/schema/context"
          xmlns:mvc="http://www.springframework.org/schema/mvc"
          xsi:schemaLocation="http://www.springframework.org/schema/beans
           http://www.springframework.org/schema/beans/spring-beans.xsd
           http://www.springframework.org/schema/context
           http://www.springframework.org/schema/context/spring-context.xsd
           http://www.springframework.org/schema/mvc
           https://www.springframework.org/schema/mvc/spring-mvc.xsd">
   
       <context:component-scan base-package="com.zhu.controller"/>
       <mvc:annotation-driven/>
       <mvc:default-servlet-handler/>
   
       <bean class="org.springframework.web.servlet.view.InternalResourceViewResolver">
           <property name="prefix" value="/WEB-INF/jsp/"/>
           <property name="suffix" value=".jsp"/>
       </bean>
   </beans>
   ```

10. application将其整合在一起

    ```xml
    <?xml version="1.0" encoding="UTF-8"?>
    <beans xmlns="http://www.springframework.org/schema/beans"
           xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
           xmlns:context="http://www.springframework.org/schema/context"
           xmlns:mvc="http://www.springframework.org/schema/mvc"
           xsi:schemaLocation="http://www.springframework.org/schema/beans
            http://www.springframework.org/schema/beans/spring-beans.xsd
            http://www.springframework.org/schema/context
            http://www.springframework.org/schema/context/spring-context.xsd
            http://www.springframework.org/schema/mvc
            https://www.springframework.org/schema/mvc/spring-mvc.xsd">
    
        <import resource="classpath:spring-dao.xml"/>
        <import resource="classpath:spring-mvc.xml"/>
        <import resource="classpath:spring-service.xml"/>
    
    </beans>
    ```

11. controller

    ```java
    @Controller
    @RequestMapping("/book")
    public class BooksController {
        @Autowired
        private UserServiceImpl userService;
    
        @RequestMapping("/list")
        public String list(Model model){
            List<Books> list = userService.queryAllBook();
            model.addAttribute("list",list);
            return "allBook";
        }
    
        @RequestMapping("/addBook")
        public String addBook(Model model){
            return "addBook";
        }
    
        @RequestMapping("/addBooks")
        public String addBooks(Books books){
            userService.addBook(books);
            return "redirect:/book/list";
        }
    
        @RequestMapping("/updata/{id}")
        public String updataBook(@PathVariable("id") int id,Model model){
            Books books = userService.queryBookById(id);
            model.addAttribute("books",books);
            return "updata";
        }
    
        @RequestMapping("/updataSeccessed")
        public String updataBooks(Books books){
            userService.updateBook(books);
            return "redirect:/book/list";
        }
    
    
        @RequestMapping("/delete/{id}")
        public String deleteBook(@PathVariable("id") int id){
            userService.deleteBook(id);
            return "redirect:/book/list";
        }
    }
    ```

    

12. tomcat首页

    ```jsp
    <%@ page contentType="text/html;charset=UTF-8" language="java" %>
    <html>
      <head>
        <title>书籍首页</title>
        <style>
          a{
            text-decoration: none;
            color: black;
            background-color: deepskyblue;
            border-radius: 5px;
          }
          div{
            margin: auto;
            text-align: center;
          }
        </style>
      </head>
      <body>
      <div>
        <a href="${pageContext.request.contextPath}/book/list">点击进入书籍列表</a>
      </div>
      </body>
    </html>
    ```

13. 显示书籍 allBook.jsp

    ```jsp
    <%@ taglib prefix="c" uri="http://java.sun.com/jsp/jstl/core" %>
    <%@ page contentType="text/html;charset=UTF-8" language="java" %>
    <html>
    <head>
        <title>书籍列表</title>
        <style>
            h1 {
                color: black;
                background-color: deepskyblue;
                border-radius: 5px;
            }
            table{
                text-align: center;
                border: deepskyblue 1px solid;
                width: 500px;
                line-height: 30px;
            }
        </style>
    </head>
    <body>
        <h1>书籍列表</h1>
        <form action="${pageContext.request.contextPath}/book/addBook" method="post">
            <input type="submit" value="新增书籍">
        </form>
        <hr>
    
        <table>
            <thead>
                <tr>
                    <th>书籍名称</th>
                    <th>书籍数量</th>
                    <th>书籍描述</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <c:forEach var="books" items="${requestScope.get('list')}">
                    <tr>
                        <td>${books.bookName}</td>
                        <td>${books.bookCounts}</td>
                        <td>${books.detail}</td>
                        <td>
                            <a href="${pageContext.request.contextPath}/book/updata/${books.bookID}">更改</a>
                            <a href="${pageContext.request.contextPath}/book/delete/${books.bookID}">删除</a>
                        </td>
                    </tr>
                </c:forEach>
            </tbody>
        </table>
    </body>
    </html>
    ```

14. 添加书籍 addBook.jsp

    ```jsp
    <%@ page contentType="text/html;charset=UTF-8" language="java" %>
    <html>
    <head>
        <title>Title</title>
    </head>
    <body>
        <form action="${pageContext.request.contextPath}/book/addBooks" method="post">
    
            书籍名称<input type="text" name="bookName" required><br><br><br>
            书籍数量<input type="text" name="bookCounts" required><br><br><br>
            书籍描述<input type="text" name="detail" required><br><br><br>
            <input type="submit" value="确认添加">
        </form>
    
    </body>
    </html>
    ```

15. 更新书籍 updata.jsp

    ```jsp
    <%@ page contentType="text/html;charset=UTF-8" language="java" %>
    <html>
    <head>
        <title>Title</title>
    </head>
    <body>
    <form action="${pageContext.request.contextPath}/book/updataSeccessed" method="post">
        <input type="hidden" name="bookID" value="${books.bookID}">
        书籍名称<input type="text" name="bookName" value="${books.bookName}" required><br><br><br>
        书籍数量<input type="text" name="bookCounts" value="${books.bookCounts}" required><br><br><br>
        书籍描述<input type="text" name="detail" value="${books.detail}" required><br><br><br>
        <input type="submit" value="确认修改">
    </form>
    </body>
    </html>
    ```

    