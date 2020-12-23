## hello world

1. pom.xml

   ```xml
   <?xml version="1.0" encoding="UTF-8"?>
   <project xmlns="http://maven.apache.org/POM/4.0.0" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
            xsi:schemaLocation="http://maven.apache.org/POM/4.0.0 https://maven.apache.org/xsd/maven-4.0.0.xsd">
       <modelVersion>4.0.0</modelVersion>
       <parent>
           <groupId>org.springframework.boot</groupId>
           <artifactId>spring-boot-starter-parent</artifactId>
           <version>2.2.4.RELEASE</version>
           <relativePath/> <!-- lookup parent from repository -->
       </parent>
       <groupId>com.example</groupId>
       <artifactId>demo</artifactId>
       <version>0.0.1-SNAPSHOT</version>
       <name>demo</name>
       <description>Demo project for Spring Boot</description>
   
       <properties>
           <java.version>1.8</java.version>
       </properties>
   
       <dependencies>
           <!-- web 依赖： tomcat dispatcherServlet xml-->
           <dependency>
               <groupId>org.springframework.boot</groupId>
               <artifactId>spring-boot-starter-web</artifactId>
           </dependency>
           <!-- 测试单元 相当于 junit-->
           <dependency>
               <groupId>org.springframework.boot</groupId>
               <artifactId>spring-boot-starter-test</artifactId>
               <scope>test</scope>
               <exclusions>
                   <exclusion>
                       <groupId>org.junit.vintage</groupId>
                       <artifactId>junit-vintage-engine</artifactId>
                   </exclusion>
               </exclusions>
           </dependency>
       </dependencies>
   
       <!--打jar 依赖-->
       <build>
           <plugins>
               <plugin>
                   <groupId>org.springframework.boot</groupId>
                   <artifactId>spring-boot-maven-plugin</artifactId>
               </plugin>
           </plugins>
       </build>
   
   </project>
   ```

   程序主入口：![image-20200220122352187](C:\Users\Administrator\AppData\Roaming\Typora\typora-user-images\image-20200220122352187.png)

2. ```java
   //自动装配
   @RestController
   public class HelloController {
   
       @RequestMapping("/t")
       public String Hello(){
           return "hello world";
       }
   }
   ```

## 原理初探

狂神博客：https://www.cnblogs.com/hellokuangshen/p/11256189.html

自动配置：

pom.xml

+ sping-boot-dependencies: 核心依赖在父工程中(其中有资源过滤，还要加载配置文件的名称必须为*/applicatioin**.xml也有标明)

+ 我们引入或者写入一下springboot依赖不需要指定版本，就是因为有这些版本仓库。（父依赖中）

+ 有大量的启动器：`srping-boot-starter`

  ```xml
  <dependency>
    <groupId>org.springframework.boot</groupId>
      <artifactId>spring-boot-starter-web</artifactId>
  </dependency>
  ```

  + spingboot会将所有的功能场景，变成一个个的启动器
  + 要使用什么，就只要找到对应的启动器即可

`@conditionalOnxxx`: 根据不同的条件，来判断当前配置是否生效

可以通过 `debug: true` 来查看哪些自动配置类生效，哪些没有；

#### 主程序：

```java
@SpringBootApplication //标注这个类是一个springboot应用
public class SpringbootMywebApplication {

    public static void main(String[] args) {
        //将应用启动
        SpringApplication.run(SpringbootMywebApplication.class, args);
    }
}
```

将`@SpringBootApplication`点进去后有如下注解:

```java
@Target({ElementType.TYPE})
@Retention(RetentionPolicy.RUNTIME)
@Documented
@Inherited
@SpringBootConfiguration (重点)
@EnableAutoConfiguration (重点)
@ComponentScan(
    excludeFilters = {@Filter(
    type = FilterType.CUSTOM,
    classes = {TypeExcludeFilter.class}
), @Filter(
    type = FilterType.CUSTOM,
    classes = {AutoConfigurationExcludeFilter.class}
)}
)
```

其他都是 普通的注解，`@ComponentScan` ： 自动扫描并加载符合条件的组件或者bean ， 将这个bean定义加载到IOC容器中 ；

`@SpringBootConfiguration (重点)` 

+ `@Configuration` : sping配置类（相当于配置文件）
  + `@Component` ： 说明这是一个spring组件

`@EnableAutoConfiguration (重点)` ：自动导入配置的注解**以前我们需要配置的东西，SpringBoot可以自动帮我们配置 ； @EnableAutoConfiguration告诉SpringBoot开启自动配置功能，这样自动配置才能生效；**

+ @AutoConfigurationPackage ： 自动配置包，其中有`@Import({Registrar.class})` ： 由{Registrar.class} 将主配置类 所在包及包下面所有子包里面的所有组件扫描到Spring容器 ；
+ `@Import({AutoConfigurationImportSelector.class})` ： 导入组件的选择器。**将需要导入的组件，以全类名的方式返回，这些组件就会被添加到容器中；** 会给容器导入很多的自动配置类（xxxAutoConfiguration）, 就是给容器中导入这个场景需要的所有组件 ， 并配置好这些组件 ；

**SpringFactoriesLoader.loadFactoryNames（EnableAutoConfiguation.class，classLoader）** ：从中发现 META-INF/Spring.factories 的文件 。 

这个文件就是自动配置的根源所在。

<font color="red">**SpringBoot在启动的时候从类路径下的META-INF/spring.factories中获取EnableAutoConfiguration指定的值，将这些值作为自动配置类导入容器 ， 自动配置类就生效 ， 帮我们进行自动配置工作；** </font>



整个J2EE的整体解决方案和自动配置都在springboot-autoconfigure的jar包中；

结论：

+ 所以，真正实现是从classpath中搜寻所有的META-INF/spring.factories配置文件 ，并将其中对应的 org.springframework.boot.autoconfigure. 包下的配置项通过反射实例化为对应标注了 @Configuration的JavaConfig形式的IOC容器配置类 ， 然后将这些都汇总成为一个实例并加载到IOC容器中。

## application.yaml

`@component`: 将类注入spring。

`@ConfigurationProperties(prefix = "person")`：从配置文件中获得 person的参数

`Person`类输出结果：

```txt
Person{name='小三', 
		age=22, 
		beautiful=true, 
		maps={aaa=asdf, ss=金钱}, 
		list=[code, 帅气, 傻逼], 
		dog=Dog{name='大黄', age=2}}
```



`application.yaml`文件配置

```yaml
Person:
  name: 小三
  age: 22
  beautiful: true
  maps: {aaa: "asdf",ss: "金钱"}
  list:
    - code
    - 帅气
    - 傻逼
  Dog:
    name: 大黄
    age: 2
```

## JSR-303

后端中，对属性的约束:

@Validated : 数据约束

## application.yaml位置优先级

根据官网可知：

1. file: ./config/

 	2. file: ./
 	3. classpath: /config/
 	4. classpath:/

file : 为项目，classpath ： 相对于resources的位置

## springboot web开发

可通过webjars:拿到web资源：localhost:8081/webjars/

```txt
"classpath:/resources/",
"classpath:/static/",
"classpath:/public/",
```

可以在resources根目录下新建对应的文件夹，都可以存放我们的静态文件；

模板引擎：thymleaf只要导入对应依赖即可。

```java
public class ThymeleafProperties {
    private static final Charset DEFAULT_ENCODING;
    public static final String DEFAULT_PREFIX = "classpath:/templates/";
    public static final String DEFAULT_SUFFIX = ".html";
}
```

#### thymeleaf

thymeleaf 命名空间; `xmlns:th="http://www.thymeleaf.org"`

在thymeleaf中相当于配置了视图解析器；

在springboot中，有非常多的xxxx Configuration 帮助我们进行扩展配置，只要看见这个东西，我们就要注意了。

三个必须掌握的语法：

```html
<div th:text="${msg}"></div>
<div th:utext="${h1}"></div>
<h1 th:each="user:${list}" th:text="${user}"></h1>
```

其他语法:

```html
<link th:href="@{/css/dashboard.css}" rel="stylesheet"> ： 导入静态资源
```



thymeleaf关闭缓存：`spring.thymeleaf.cache=false;`

####  扩展springMVC

给扩展MVC加上注解 ： `@EnableWebMvc`  由于其引入了一个类：`@Import({DelegatingWebMvcConfiguration.class})` 此类继承了`WebMvcConfigurationSupport`。

而Webmvc自动配置里面写了`@ConditionalOnMissingBean({WebMvcConfigurationSupport.class})`

不能有此类，故如果添加了 `@EnableWebMvc` 则会使全部的webmvc失效；



#### 页面国际化

url  : @{};

1. 配置i18文件

   ![image-20200223115358793](C:\Users\Administrator\AppData\Roaming\Typora\typora-user-images\image-20200223115358793.png)

2. 若想按钮进行切换，则要自定义一个组件`LocalResolver` （国际化的组件）

   ```java
   public class MyLocalResolver implements LocaleResolver {
       @Override
       public Locale resolveLocale(HttpServletRequest httpServletRequest) {
           String language = httpServletRequest.getParameter("l");
           Locale local = Locale.getDefault();
           if(language != null && language != ""){
               String[] s = language.split("_");
   
               local = new Locale(s[0], s[1]);
           }
           return local;
       }
   }
   
   ```

   

3. 更改国际化的basename属性值 `spring.messages.basename=i18n.login`

4. 记得将组件配置到spring容器中`@Bean`

5. #{}

**后台模板 x-admin官网推荐**

#### 页面拦截器

```java
public class LoginHandleInterceptor implements HandlerInterceptor {
    @Override
    public boolean preHandle(HttpServletRequest request, HttpServletResponse response, Object handler) throws Exception {
        Object username = request.getSession().getAttribute("loginUser");
        if(username == null){
            request.setAttribute("error","未登录，请先登录");
            request.getRequestDispatcher("/index.html").forward(request,response);
            return false;
        }else return true;
    }
}
```

从sesssion中获取bean，若没有，则代表没登录，让其返回。

在配置类中写入：拦截的和允许通过的。

```java
public void addInterceptors(InterceptorRegistry registry) {
        registry.addInterceptor(new LoginHandleInterceptor()).addPathPatterns("/**").excludePathPatterns("/index.html","/","/user/login",
                "/css/*","/js/**","/img/**");
    }
```



## 数据库连接

#### 1、jdbc

先配置好数据源：

```yacas
spring:
  datasource:
    driver-class-name: com.mysql.cj.jdbc.Driver
    url: jdbc:mysql://localhost:3306/mybatis?useUnicode=true&characterEncoding=utf-8&serverTimezone=UTC
    username: root
    password: root
```

jdbc老格式：

```java
@Autowired
DataSource dataSource;
@Test
void contextLoads() throws SQLException {
    System.out.println(dataSource.getClass());

    Connection connection = dataSource.getConnection();
    System.out.println(connection);

    String sql = "select * from user";
    PreparedStatement preparedStatement = connection.prepareStatement(sql);
    //        preparedStatement.setString(1,"1");
    ResultSet resultSet = preparedStatement.executeQuery();
    while (resultSet.next()){
        int id = resultSet.getInt(1);
        String name = resultSet.getString("name");
        String pwd = resultSet.getString(3);
        System.out.println(id + " " + name + " " + pwd);
    }
```

使用容器中的JDBC模板`JdbcTemplate` ：org.springframework.jdbc.core.JdbcTemplate。

```java
@Autowired
JdbcTemplate jdbcTemplate;
@Test
void test(){
    String sql = "select * from user";
    List<Map<String, Object>> maps = jdbcTemplate.queryForList(sql);
    for(Map<String, Object> ma : maps){
        System.out.println("------------");
        for(Map.Entry<String,Object> entry : ma.entrySet()){
            System.out.println(entry.getKey() + ":" + entry.getValue());
        }
    }
    //        System.out.println(maps);
}
```

预防SQL注入版本：

```java
@Test
void test1(){
    String sql = "update user set name = ?,pwd = ? where id = ?";
    Object[] ob= new Object[3];

    ob[0] = "sss";
    ob[1] = "123";
    ob[2] = 2;

    int update = jdbcTemplate.update(sql, ob);
    System.out.println(update);
}
```

#### 数据库整合springboot

application.yaml配置文件：

1. 配置数据源
2. 配置mapper映射文件位置

```yaml
spring:
  datasource:
    driver-class-name: com.mysql.cj.jdbc.Driver
    url: jdbc:mysql://localhost:3306/mybatis?useUnicode=true&characterEncoding=utf-8&serverTimezone=UTC
    username: root
    password: root
mybatis:
  type-aliases-package: com.zhu.pojo
  mapper-locations: classpath:Mapper/*.xml

server:
  port: 8081
```

数据库接口：

```java
@Mapper
@Repository
public interface UserMapper {

    List<User> selectUser();
    //根据id选择用户
    User selectUserById(int id);
    //添加一个用户
    int addUser(User user);
    //修改一个用户
    int updateUser(User user);
    //根据id删除用户
    int deleteUser(int id);

}
```

## springSecurity(安全)

可以对拥有不同权限的账户进行不同的管理。

当遇到：`PasswordEncoder` 时，要将其密码进行编码。

这里需要用到`WebSecurityConfigurerAdapter`:

```java
@EnableWebSecurity //必不可少的注解
public class SecurityConfig extends WebSecurityConfigurerAdapter {
    //设置不同页面的权限
    @Override
    protected void configure(HttpSecurity http) throws Exception {
        http.authorizeRequests()
                .antMatchers("/").permitAll()
                .antMatchers("/level1/**").hasRole("level1")
                .antMatchers("/level2/**").hasRole("level2")
                .antMatchers("/level3/**").hasRole("level3");

        http.formLogin();
    }
    //对不同的账户进行不同的权限设置  将其密码进行编码
    @Override
    protected void configure(AuthenticationManagerBuilder auth) throws Exception {
        auth.inMemoryAuthentication().passwordEncoder(new BCryptPasswordEncoder())
                .withUser("admin").password(new BCryptPasswordEncoder().encode("123456")).roles("level1","level2","level3")
                .and()
                .withUser("admin1").password(new BCryptPasswordEncoder().encode("123456")).roles("level1","level2")
                .and()
                .withUser("admin2").password(new BCryptPasswordEncoder().encode("123456")).roles("level1");
    }

}
```




