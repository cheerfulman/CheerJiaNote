# JSP
1. jsp的作用
+ Servlet:
    > 缺点：不适合设置html的响应体，需要大量的response.getWriter().print("<html>");
    > 优点: 动态资源，可以编程
+ html:
    > 缺点：html静态页面，不能包含动态信息
    > 优点: 不用输出html标签而发愁
+ jsp:(java server pages)
    > 优点： 在原有的html基础上添加java脚本，构成jsp页面；


2. jsp和Servlet的分工:
    + jsp:
        > 作为请求发起页面，例如显示表单、超链接。
        > 作为请求结束页面，例如显示数据。
    + Servlet
        > 作为请求中处理数据的环节

3. jsp的组成
    + jsp = html + java脚本 + jsp标签(指令)
    + jsp中无需创建即可使用对象一共由9个，被称为9大内置对象。例如：request对象、out对象。
    + 3中java脚本：
        > `<%...%>:` java代码片段(常用)，用于定义0-n条java语句！(只能写方法内能写的)

        > `<%=...%>:` java表达式，用于输出(常用),用于输出一条表达式(或变量)的结果。

        > `<%!...%>:` 声明，用来创建类的成员变量和成员方法(基本不用，但容易被考到)；（类体中可以放什么就能放什么）
        class c{成员，方法，构造器，构造代码块，静态块，内部类}

### jsp和servlet的分工
在index.jsp中写一个表单用于用户提交:
```java
<html>
  <head>
    <title>$Title$</title>
  </head>
  <body>
  //action = 进入提交后的地址->Servlet
  <form action="/jsp/AServlet" method="post">
    整数1：<input type="text" name="name1"/><br>
    整数2：<input type="text" name="name2"/><br>
    <input type="submit" value="提交"/>
  </form>
  </body>
</html>
```

经过Servlet处理后：
```java
public class AServlet extends HttpServlet {
    @Override
    protected void doPost(HttpServletRequest req, HttpServletResponse resp) throws ServletException, IOException {
        String s1 = req.getParameter("name1"); //使用HttpServletRequest 的 getParameter()函数，得到提交名为"name1"的值
        String s2 = req.getParameter("name2");

        int num1 = Integer.parseInt(s1); //转型
        int num2 = Integer.parseInt(s2);
        int sum = num1 + num2;

        req.setAttribute("result",sum); //设置域 result，sum,

        req.getRequestDispatcher("/result.jsp").forward(req,resp);//转发，将另一个路径为"/result.jsp"的转入，默认起始点为 ---- web；
    }
}
```

req获取数据 ： ```Object getAttribute(String name)``` 返回的是Object 通常需要 转型；

req存数据 ： ```void setAttribute(String name, Object o) ``` 

## jsp 原理
---
+ jsp是一种特殊的Servlet
    > 当页面第一次被访问时，服务器会把jsp编译成java文件(这个java其实是一个Servlet类)
    > 然后再把java编译成.class
    > 然后创建该类的对象
    > 最后调用它的Service()方法
    > 第二次请求同一jsp时，直接调用service()方法

### Cookie
---
+ Cookie是HTTP协议制定的
+ 一个Cookie最大4KB
+ 一个服务器最多向一个浏览器保存20个Cookie
+ 一个浏览器(客户端)最多保存300个Cookie

javaWeb使用Cookie
使用response.addCookie()向浏览器保存Cookie
使用response.getCookies()获取浏览器归还的Cookie----(得到一个数组)

对Cookie设置年龄:setMaxAge(); 
`>0`代表时间为多少秒，例如： 一年 -> setMaxAge(60*60*24*360)

`<0` ： 则只在浏览器内存中存在，当用户关门浏览器时，浏览器进程结束，Cookie死亡；

`=0` ： 浏览器将马上删除这个Cookie；

## session
重定向：response.sendRedirect(带项目名)
转发：request.getRequestDispatcher(不要带项目名)

HttpSession原理:
+ request.getSession()方法
访问jsp就相当于自动创建了session；
而Servlet就不一定了，必须要在里面有创建；


jsp三大指令：
## page指令
---
一个jsp页面中可以有多个page指令
page指令格式：`<%@page language="java"%>`
+ pageEncoding和contentType: 
+ pageEncoding表示当时的编码格式。在服务器tomcat要把jsp编译成.java时需要使用pageEncoding
+ contentType : 表示一个响应头；等同域`response.setContentType("text/html;cahrset=utf-8");`

+ import : 用来导包，可以出现多次；
+ errorPage和 isErrorPage
    > errorPage : 如果页面出错，调转到此页面
    > isErrorPage : 它指定当前页面是否为处理错误的页面；这个页面会设置状态码为500，且这个页面可以使用9大内置对象的exception!

web-xml的配置
```java
<error-page>
    <error-code>404</error-code>
    <location>/session2/login.jsp</location>
</error-page>
```

+ autoFlush和buffer
> autoFlush : 指定jsp的输出流缓冲区满时，是否自动刷新；默认为true，如果为false，那么在缓冲区满时抛出异常；
> buffer指定缓冲区大小，默认为8kb，通常不需要修改


+ isELIgnored : 是否忽略el表达式，默认为false，不忽略，即支持

+ language : 指定jsp编译后语言的类型，默认java

+ info : 信息
+ isTreadSafe : 当前的jsp是否支持并发访问
+ session : 当前页面是否支持session，如果false，那么当前页面就没有session这个内置对象；

+ extends : 让jsp生成的servlet去继承属性指定的类

九大内置对象：
+ out --> jsp的输出流，用来向客户端响应
+ page --> 当前jsp对象！ 它的引用类型是Object
+ config --> 它对应真身中的ServletConfig
+ pageContext --> 一个顶九个
+ request   --> HttpServleRequest
+ response  --> HttpServleResponse
+ exception --> Throwable
+ session   --> HttpSession 
+ application   --> ServletContext


1. pageContext
    + Servlet中有三大域，JSP有四大域，它就是最后一个域对象；
        > ServletContext: 整个应用程序

        > session : 整个会话(一个会话只有一个用户)(用户关闭一次浏览器后，再打开登录则是一个新的session)

        > request: 一个请求链

        > pageContext : 一个jsp页面，这个域是在当前jsp页面和当前jsp页面中使用的标签之间共享数据；
    + 是域对象且可以代理其他域；
    + 可以获取其他8大内置对象
    + 全域查找 ： `pageContext.findAttribute("xxx")`; 从小到大；

## include指令和taglib指令
---

include: 静态包含
与RequestDispatcher的include()方法相似；
+ <%@include%>他是jsp编译成java文件时完成的！，它们共同生产一个java(就是一个servlet)文件，然后再生成一个class!
+ RequestDispatcher的include()是一个方法，包含和被包含的两个servlet，即两个.class！ 它们只是把响应内容在运行时合并了


tablib---> 导入标签库
+ prefix : 指定标签库在本页面的前缀，由我们自己起名字
+ uri : 指定标签库的位置！
```java
<%@taglib prefix = "pre" uri = "/struts-tags"%> <pre:text>
```

jsp注释:<%-- --%>
## jsp标签
---
<jsp:forword> : 转发
<jsp:include> ：包含

<jsp:param> ： 它用来作为forward 和 include 的子标签！用来给转发或包含的页面传递参数；



<jsp:useBean> --> 创建或查找bean,有则查，无则创
这样就new了一个User类型的对象，对象名为user。在page属性中。
> <jsp:useBean id = "user1" class = "cn.itcast.domain.person" scope = "page"/> //scope指定域


<jsp:setProperty> --> 设置名为user1这个javaBean的username属性为admin
> <jsp:setProperty property = "username" name = "user1" value = "admin"/>
<jsp:getProperty> --> 获取名为user1这个javabean名为username的属性值
> <jsp:getProperty property = "username" name = "user1" />


## El表达式
---
1. EL是jsp内置的表达式语言
+ `${xxx}`; --> 全域查找
+ `${pageScope.xxx}`   -- > 特别域查找
+ `${requestScope.xxx}`
+ `${sessionScope.xxx}`
+ `${applicationScope.xxx}`  

+ `param `: 对应参数，他是一个map，Key表示参数名，value是参数值，适用于单值的参数；
+ `paramValues`:对应参数，他是一个map，Key表示参数名，value是参数值，适用于多值的参数；

+ header : 对应请求头，也是map，key表示头名称，value是单个头值，适用于单值请求头；

+ headerValues: 对应请求头，也是map，key表示头名称，value是单个头值，适用于多值请求头；

+ initParam : 获取<content-param>内的参数
    ```
    <param-name>
    <param-value>
    ```
+ cookie : Map<String,Cookie>类型，其中key是Cookie的name， value 是Cookie 对象。 `${Cookie,uername.value}`

+ pageContext : 他是PageCntext类型！`${pageContext.request}`

## EL函数库
---


## JSTL 标签库
+ core : 核心库
+ fmt  : 格式化 ：日期、数字
+ sql  : 数据库标签，不需要学习，过时
+ xml  : xml标签库，过时

1、core --> c标签
---
1. out和set
    + <c:out> ： 输出
    + value: 可以是字符串常量，也可以是EL表达式
    + default ： 当输出为null时会输出default指定值
    + escapeXml : 转义，默认为true；


    + <c:set> : 创建一个域的属性，默认域为pageContext
    ```
    <c:set var = "a" value = "hello" scope = "request"/>   在request域中添加一个创建一个名字为a，名为hello的数据
    ```
2. remove
    + 删除域变量(默认所有域)
    ```
    <c:remove var = "a" scope = "page"> 在page域中删除属性名为a的域
    ```
3. url
    + value:会指定一个路径! 它会在路径前面自动添加项目名。
        ```java
        <c:url value = "/index.jsp">
        ```
        他会输出    项目名/index.jsp
    + 可以定义子标签：`<c:param>`用来给url后面添加参数，例如：
        ```java
            <c:url value = "/index.jsp">
                <c:param name = "username" value = "张三"/>
            <c:url>
        ```

        结果为/项目名/index.jsp?username=%EKISJJJN%EKISJJJN(自动编码)
    + var: 指定变量名，一旦添加这个属性，那么url标签就不会输出到页面，而是把生产的url保存到域中
    + scope : 它与var一起使用，保存的域;
4. if
    + 
    ```java
    <c:set var = "a" value = "hello">
    <c:if test = "${not empty a}">(test = “(boolean类型)”)
        <c:out value = "${a}">
    </c:if>
    ```
5. choose
    ```java
    <c:choose>
    <c:when test="${(Person.age) >= 10}">帅哥靓女</c:when>
    <c:otherwise>kao</c:otherwise>
    </c:choose>
    ```
6. forEach --> 一个for循环
    ```java
    <c:forEach var = "i" begin = "1" end = "10" step = "1">

    <c:forEach items = "${requesScope}" var = "str">
        ${str}
    </c"forEach>
    ```
    + var : 循环变量
    + items : 要循环的值，可以是数组或者集合
    + varStaus来创建循环状态变量
    1. count ： 循环元素的个数
    1. index ： 循环元素的下标
    1. first ： 是否为第一个
    1. last  ： 是否为最后一个
    1. current  ： 当前元素

7. fmt 格式化库
```
<fmt:formatData value = "" pattern = "yyyy-MM-dd HH:mm:ss">
```



