## springMVC

![1570167567494.png](https://blog.kuangstudy.com/usr/uploads/2019/10/1958583181.png)

更改MAVEN的JDK版本为1.8，在pom.xml中添加：

```xml
<properties>
    <project.build.sourceEncoding>UTF-8</project.build.sourceEncoding>
    <maven.compiler.source>1.8</maven.compiler.source>
    <maven.compiler.target>1.8</maven.compiler.target>
</properties>

```



## springMVC原理简单解析：

![1570167751381.png](https://blog.kuangstudy.com/usr/uploads/2019/10/63825124.png)



![image-20200216155702363](C:\Users\Administrator\AppData\Roaming\Typora\typora-user-images\image-20200216155702363.png)

访问此页面： 相当于用户发出一个请求。springmvc执行步骤如下：

1. 寻找DispatcherServlet(调度Servlet)：

   ```xml
   <servlet>
       <servlet-name>springmvc</servlet-name>
       <servlet-class>org.springframework.web.servlet.DispatcherServlet</servlet-class>
   
       <!--        DispatcherServlet : 要绑定spring-mvc的配置文件-->
       <init-param>
           <param-name>contextConfigLocation</param-name>
           <param-value>classpath:spring.xml</param-value>
       </init-param>
   
       <!--        启动级别： 服务器启动，我就启动-->
       <load-on-startup>1</load-on-startup>
   </servlet>
   
   <!--
       <url-pattern>/</url-pattern> ： / 只匹配所有的请求,   不匹配 .jsp
       <url-pattern>/*</url-pattern> ： / 匹配所有的请求,   包括.jsp
       (如果 /* ，待会走到前端控制器会再 去到视图解析器，则会再次匹配.jsp进行一个无限循环)
       -->
   <servlet-mapping>
       <servlet-name>springmvc</servlet-name>
       <url-pattern>/</url-pattern>
   </servlet-mapping>
   ```

   通过绑定的spring.xml文件到步骤二：

2. `spring-servlet.xml`配置如下：

   ```xml
   <?xml version="1.0" encoding="UTF-8"?>
   <beans xmlns="http://www.springframework.org/schema/beans"
          xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
          xsi:schemaLocation="http://www.springframework.org/schema/beans
                              http://www.springframework.org/schema/beans/spring-beans.xsd">
   
   
       <!--添加处理映射器-->
       <bean class="org.springframework.web.servlet.handler.BeanNameUrlHandlerMapping"/>
   
   
       <!--添加处理适配器-->
       <bean class="org.springframework.web.servlet.mvc.SimpleControllerHandlerAdapter"/>
   
       <!--    模板引擎 Thymeleaf  Freemarker-->
       <!--添加视图解析器:DispatcherServlet给他的ModelAndView-->
       <bean class="org.springframework.web.servlet.view.InternalResourceViewResolver">
           <property name="prefix" value="/WEB-INF/jsp/"/>
           <property name="suffix" value=".jsp"/>
       </bean>
   
   
       <bean id="/hello" class="com.zhu.controller.HelloController"/>
   
   </beans>
   ```

   此时(调度Servlet) 通过 映射器，找到 对应的bean, 然后通过 适配器 从`class` 找到`controller`

3. Controller代码：

   ```java
   public class HelloController implements Controller {
   
       @Override
       public ModelAndView handleRequest(HttpServletRequest httpServletRequest, HttpServletResponse httpServletResponse) throws Exception {
           ModelAndView mv = new ModelAndView();
   
           //先是业务代码 --- 省略
           String msg = "springMVC";
   
           mv.addObject("msg",msg);
   
           mv.setViewName("text");
   
           return mv;
       }
   }
   ```

   处理相应的业务，然后返回一个`ModelAndView`留给解析器去做啦，给其加上相应的`prifix` 和 `suffix` 显示这个视图。

4. 此时就是用户所看见的页面啦。

---

记录一下资源过滤的配置：

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



## 使用注解

1、web中的配置一样：

```xml
<servlet>
    <servlet-name>springmvc</servlet-name>
    <servlet-class>org.springframework.web.servlet.DispatcherServlet</servlet-class>

    <init-param>
        <param-name>contextConfigLocation</param-name>
        <param-value>classpath:spring.xml</param-value>
    </init-param>

    <load-on-startup>1</load-on-startup>
</servlet>

<servlet-mapping>
    <servlet-name>springmvc</servlet-name>
    <url-pattern>/</url-pattern>
</servlet-mapping>
```

2、spring.xml中`<mvc:annotation-driven/>`使用这个，代替前面的适配器和映射器.

使用注解 和 mvc 要加入一些配置： `context` 注解配置 和 `mvc`配置：

```xml
xmlns:context="http://www.springframework.org/schema/context"
xmlns:mvc="http://www.springframework.org/schema/mvc"

http://www.springframework.org/schema/context
https://www.springframework.org/schema/context/spring-context.xsd
http://www.springframework.org/schema/mvc
https://www.springframework.org/schema/mvc/spring-mvc.xsd
```



```xml
<!-- 自动扫描包，让指定包下的注解生效,由IOC容器统一管理 -->
<context:component-scan base-package="com.zhu.controller"/>
<!-- 让Spring MVC不处理静态资源  .css .js .jtml -->
<mvc:default-servlet-handler/>
<!--    MVC注解驱动 相当于注入了 映射器和 适配器-->
<mvc:annotation-driven/>

<!--    视图解析器-->
<bean class="org.springframework.web.servlet.view.InternalResourceViewResolver" id="internalResourceViewResolver">
    <!-- 前缀 -->
    <property name="prefix" value="/WEB-INF/jsp/" />
    <!-- 后缀 -->
    <property name="suffix" value=".jsp" />
</bean>
```

3、Controller层：

`@Controller`：让适配器 找到它。

`@RequestMapping("/hello")` ： 代表其url路径为 /hello

`return "hello"` : 找到 `hello.jsp`的视图

```java
@Controller
public class HelloController {
    @RequestMapping("/hello")
    public String Hello(Model model){
        model.addAttribute("msg","hello SpringMVC!");
        return "hello"; //会被视图解析器处理
    }
}
```

以后可在 `Controller`里面写多个方法，即可相当于不同的`Servlet`



## RestFul风格

利用斜杠，传参，不是传统的?a=1&b=2的方式，暴露参数；

**更加安全**

```java
@RequestMapping(value = "/h/{a}/{b}",method = RequestMethod.GET)
public String add(@PathVariable int a, @PathVariable int b, Model model){
    int res = a + b;
    model.addAttribute("msg","结果为" + res);
    return "hello";
}
```

方法级别的注解：

```java
@GetMapping
@PostMapping
@PutMapping
@DeleteMapping
@PatchMapping
```

重定向：return "redirect ()";

或者是`response.sendRedirect(request.getContextPath()+"/项目名/forwardView");`

## 乱码问题

利用spring自带的过滤器

```xml
<filter>
    <filter-name>encoding</filter-name>
    <filter-class>org.springframework.web.filter.CharacterEncodingFilter</filter-class>
    <init-param>
        <param-name>encoding</param-name>
        <param-value>utf-8</param-value>
    </init-param>
</filter>
<filter-mapping>
    <filter-name>encoding</filter-name>
    <url-pattern>/</url-pattern>
</filter-mapping>
```

如果还不行：

修改tomcat配置文件 ： 

1. 设置编码！

```xml
<Connector URIEncoding="utf-8" port="8080" protocol="HTTP/1.1"
           connectionTimeout="20000"
           redirectPort="8443" />
```

2. 自定义过滤器

   ```java
   import javax.servlet.*;
   import javax.servlet.http.HttpServletRequest;
   import javax.servlet.http.HttpServletRequestWrapper;
   import javax.servlet.http.HttpServletResponse;
   import java.io.IOException;
   import java.io.UnsupportedEncodingException;
   import java.util.Map;
   
   /**
    * 解决get和post请求 全部乱码的过滤器
    */
   public class GenericEncodingFilter implements Filter {
   
       @Override
       public void destroy() {
       }
   
       @Override
       public void doFilter(ServletRequest request, ServletResponse response, FilterChain chain) throws IOException, ServletException {
           //处理response的字符编码
           HttpServletResponse myResponse=(HttpServletResponse) response;
           myResponse.setContentType("text/html;charset=UTF-8");
   
           // 转型为与协议相关对象
           HttpServletRequest httpServletRequest = (HttpServletRequest) request;
           // 对request包装增强
           HttpServletRequest myrequest = new MyRequest(httpServletRequest);
           chain.doFilter(myrequest, response);
       }
   
       @Override
       public void init(FilterConfig filterConfig) throws ServletException {
       }
   
   }
   
   //自定义request对象，HttpServletRequest的包装类
   class MyRequest extends HttpServletRequestWrapper {
   
       private HttpServletRequest request;
       //是否编码的标记
       private boolean hasEncode;
       //定义一个可以传入HttpServletRequest对象的构造函数，以便对其进行装饰
       public MyRequest(HttpServletRequest request) {
           super(request);// super必须写
           this.request = request;
       }
   
       // 对需要增强方法 进行覆盖
       @Override
       public Map getParameterMap() {
           // 先获得请求方式
           String method = request.getMethod();
           if (method.equalsIgnoreCase("post")) {
               // post请求
               try {
                   // 处理post乱码
                   request.setCharacterEncoding("utf-8");
                   return request.getParameterMap();
               } catch (UnsupportedEncodingException e) {
                   e.printStackTrace();
               }
           } else if (method.equalsIgnoreCase("get")) {
               // get请求
               Map<String, String[]> parameterMap = request.getParameterMap();
               if (!hasEncode) { // 确保get手动编码逻辑只运行一次
                   for (String parameterName : parameterMap.keySet()) {
                       String[] values = parameterMap.get(parameterName);
                       if (values != null) {
                           for (int i = 0; i < values.length; i++) {
                               try {
                                   // 处理get乱码
                                   values[i] = new String(values[i]
                                                          .getBytes("ISO-8859-1"), "utf-8");
                               } catch (UnsupportedEncodingException e) {
                                   e.printStackTrace();
                               }
                           }
                       }
                   }
                   hasEncode = true;
               }
               return parameterMap;
           }
           return super.getParameterMap();
       }
   
       //取一个值
       @Override
       public String getParameter(String name) {
           Map<String, String[]> parameterMap = getParameterMap();
           String[] values = parameterMap.get(name);
           if (values == null) {
               return null;
           }
           return values[0]; // 取回参数的第一个值
       }
   
       //取所有值
       @Override
       public String[] getParameterValues(String name) {
           Map<String, String[]> parameterMap = getParameterMap();
           String[] values = parameterMap.get(name);
           return values;
       }
   }
   ```

   

   ## json

   #### JackJson

    jar包:

   ```xml
   <!-- https://mvnrepository.com/artifact/com.fasterxml.jackson.core/jackson-core -->
   <dependency>
       <groupId>com.fasterxml.jackson.core</groupId>
       <artifactId>jackson-databind</artifactId>
       <version>2.9.8</version>
   </dependency>
   ```

   发现出现了乱码问题，我们需要设置一下他的编码格式为utf-8，以及它返回的类型；

   通过@RequestMaping的produces属性来实现，修改下代码

   ```java
   //produces:指定响应体返回类型和编码
   @RequestMapping(value = "/json1",produces = "application/json;charset=utf-8")
   ```

   spring统一解决json乱码：

   ```xml
   <mvc:annotation-driven>
       <mvc:message-converters register-defaults="true">
           <bean class="org.springframework.http.converter.StringHttpMessageConverter">
               <constructor-arg value="UTF-8"/>
           </bean>
           <bean class="org.springframework.http.converter.json.MappingJackson2HttpMessageConverter">
               <property name="objectMapper">
                   <bean class="org.springframework.http.converter.json.Jackson2ObjectMapperFactoryBean">
                       <property name="failOnEmptyBeans" value="false"/>
                   </bean>
               </property>
           </bean>
       </mvc:message-converters>
   </mvc:annotation-driven>
   ```

   

   `@ResponseBody`: 方法上的注解，该方法不会走解析器

   `@RestController` : 类上的注解，该类的方法都不会走解析器

web.xml

```xml
<?xml version="1.0" encoding="UTF-8"?>
<web-app xmlns="http://xmlns.jcp.org/xml/ns/javaee"
         xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:schemaLocation="http://xmlns.jcp.org/xml/ns/javaee http://xmlns.jcp.org/xml/ns/javaee/web-app_4_0.xsd"
         version="4.0">

    <!--1.注册servlet-->
    <servlet>
        <servlet-name>SpringMVC</servlet-name>
        <servlet-class>org.springframework.web.servlet.DispatcherServlet</servlet-class>
        <!--通过初始化参数指定SpringMVC配置文件的位置，进行关联-->
        <init-param>
            <param-name>contextConfigLocation</param-name>
            <param-value>classpath:springmvc-servlet.xml</param-value>
        </init-param>
        <!-- 启动顺序，数字越小，启动越早 -->
        <load-on-startup>1</load-on-startup>
    </servlet>

    <!--所有请求都会被springmvc拦截 -->
    <servlet-mapping>
        <servlet-name>SpringMVC</servlet-name>
        <url-pattern>/</url-pattern>
    </servlet-mapping>

    <filter>
        <filter-name>encoding</filter-name>
        <filter-class>org.springframework.web.filter.CharacterEncodingFilter</filter-class>
        <init-param>
            <param-name>encoding</param-name>
            <param-value>utf-8</param-value>
        </init-param>
    </filter>
    <filter-mapping>
        <filter-name>encoding</filter-name>
        <url-pattern>/</url-pattern>
    </filter-mapping>

</web-app>
```

使用JackJson抽取工具类

```xml
public class JsonUtils {
    
    public static String getJson(Object object) {
        return getJson(object,"yyyy-MM-dd HH:mm:ss");
    }

    public static String getJson(Object object,String dateFormat) {
        ObjectMapper mapper = new ObjectMapper();
        //不使用时间差的方式
        mapper.configure(SerializationFeature.WRITE_DATES_AS_TIMESTAMPS, false);
        //自定义日期格式对象
        SimpleDateFormat sdf = new SimpleDateFormat(dateFormat);
        //指定日期格式
        mapper.setDateFormat(sdf);
        try {
            return mapper.writeValueAsString(object);
        } catch (JsonProcessingException e) {
            e.printStackTrace();
        }
        return null;
    }
}
```

#### fastjson

pom依赖配置

```xml
<dependency>
    <groupId>com.alibaba</groupId>
    <artifactId>fastjson</artifactId>
    <version>1.2.60</version>
</dependency>
```

## Ajax

**AJAX = Asynchronous JavaScript and XML（异步的 JavaScript 和 XML）。**

（待定）