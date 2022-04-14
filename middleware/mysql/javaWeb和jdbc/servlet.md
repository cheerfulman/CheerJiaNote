# servlet

每一个servlet相当于一个客服，但是不同的客服只能做对应的事情，列如：客服A--只能拉屎，客服B--只能擦pp，当你要做某种事情的时候只能让对应的客服为你服务；

有五个方法:
```java
    public void destroy() {
        System.out.println("12313");
    }

    @Override
    public ServletConfig getServletConfig() {
        System.out.println("getServletConfig()");
        return null;
    }
    /**
     * 获取信息
     * @return
     */
    @Override
    public String getServletInfo() {
        System.out.println("getServletInfo()....");
        return "getServletInfo().. 的 return";
    }

    // 出生
    @Override
    public void init(ServletConfig servletConfig) throws ServletException {
        System.out.println("init()");
    }


    /*
    调用多次
    每次处理请求都执行；
     */
    @Override
    public void service(ServletRequest servletRequest, ServletResponse servletResponse) throws ServletException, IOException {
        System.out.println("service()");
    }
```

生命周期的方法:
```java
void init (ServletConfig): 创建之后立即执行的初始化
void service(ServletRequest servletRequest, ServletResponse servletResponse)
void destroy() : 销毁之前释放资源的方法
```

特性:
+ 是单列类，一个类只有一个对象；可能存在多个Servlet类
+ 线程不安全，所以效率是高的；

> 线程安全是以牺牲效率为代价的，线程安全多了个加锁和解锁的操作故效率相对低下；

<font color = red>**例如**: StringBuffer 是线程安全的 而 StringBuilder是不安全的 故 StringBuilder 效率高于 StringBuffer;</font>

HashMap是线程非安全的，HashTable是线程安全的。

同理：HashMap的效率高于HashTable。

Servlet类由我们来写，对象由服务器来创建，并调用相应的方法；

## ServletConfig对象
---

一个ServletConfig对象，对应一段web.xml中的Servlet的配置信息；

API：

```String getServlet()```: 获取<servlet-name></servlet-name>中的内容

```ServletContext getServletContext()```: 获取Servlet上下文对象；

```Srting getInitParameter(String name)``` : 通过名称获取指定初始化参数的值；

```Enumeration getInitParameterNames()``` : 获取所有初始化参数的名称；

---
出现405，可能没重现doGet(),doPost()，不重写它们自动返回405；

<load-on-startup>0(非负数)觉得创建顺序</load-on-startup>: 使其启动时，就完成创建


<url-pattern></url-pattern>： 访问路径；


<url-pattern>/servlet/*</url-pattern>：路径匹配

<url-pattern>*.do</url-pattern>：后缀名匹配

<url-pattern>/*</url-pattern>：匹配所有URL
*()只能出现在两端，不能出现在中间；
## ServletContext
---


+ ServletContext只有一个；随着tomcat生产和消失；
+ ServletContext是javaWeb四大域对象之一；
+ 域对象必须有存对象和取对象的功能；
+ getServletContext()来获取ServletContext；
+ 一个项目只有一个ServletContext对象
```void setAttribute(Srting name,Object value) ``` : 存对象

```Object getAttribute(Srting name) ``` : 取对象

```void removeAttribute(Srting name) ``` : 用来移除ServletContext中的域属性，都是一对一对的；

```Enumeration getAttributeNames() ``` : 获取所有域属性对象；


``` getResourcePaths("/WEB-INF")``` ： 获取该文件夹下的文件；

``` getRealPaths("/index.jsp")``` ： 获取该文件的路径；

+ 一个Servlet对应一个ServletConfig(配置Servlet配置信息的对象)
+ 一个Web项目对应一个ServletContext


获取类路径下的资源:
+ class
+ classLoader

使用 ClassLoader:
```java
ClassLoader c1 = this.getClass().getClassLoader();

InputStream input = c1.getResourceAsStream("a.txt"); 
```

使用 Class:
```java
Class c1 = this.getClass();

InputStream input = c1.getResourceAsStream("a.txt");    
```
