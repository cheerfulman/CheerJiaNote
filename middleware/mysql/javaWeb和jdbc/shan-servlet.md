# servlet

servlet是JavaWeb三大组件之一，属于动态资源

servlet有两种创建顺序，第一是在服务器启动时创建，第二是用户访问时创建。服务器启动时创建需要在web.xml中配置<load-on-startup>0</load-on-startup>来实现，其中，0就是创建的次序，数字越小，越先创建
## 实现 javax.servlet.Servlet  接口



生命周期方法：
- void init(ServletConfig servletconfig) servlet被创建之后立即调用，只执行1次 
- void service(ServletRequest requset,ServletResponse servletresponse) 执行多次
- void destory() servlet被销毁之前调用，只执行1次

特性：
+ 单例，一个类只有一个对象。可以存在多个servlet
+ 线程是不安全的，所以它的效率非常高

servlet由我们来写，但对象由服务器来创建，方法也由服务器来调用。

## 实现 javax.servlet.GenericServlet  类

## 实现 javax.servlet.HttpServlet  类

* void service(ServletRequest req, ServletResponse res) 
* protected void service(HttpServletRequest req, HttpServletResponse resp)  
* protected void doPut(HttpServletRequest req, HttpServletResponse resp)  
* protected void doPost(HttpServletRequest req, HttpServletResponse resp)  

HttpServlet类结构介绍：在实例化HttpServlet后，先调用service(ServletRequest, ServletResponse)方法，在这个方法中将ServletRequest和ServletResponse强转为HttpServletRequest和HttpServletResponse，最后根据HttpServletRequest中的参数决定调用doPost方法还是doGet方法

注意：如果没有覆写doPost或doGet方法，当这两个方法被调用时，浏览器会出现405错误，表示不支持该种请求方式。

# ServletContext

一个项目中只有一个ServletContext（也叫做application），所以可以在多个servlet中利用这个对象来传递数据。

服务器为每一个应用程序创建ServletContext对象，ServletContext对象在服务器启动时创建，在服务器关闭时销毁。

获取ServletContext
- ServletConfig#getServletContext()
- GenericServlet#getServletContext()
- HttpSession#getServletContext()
- ServletContextEvent#getServletContext()


# 获取类路径资源

如果在文件路径前写有‘/’，那么此时对准的文件路径起始点就是classes目录下，如果不写，那么对准的文件路径起始点就是 `.class`文件目录下。

两种方式：
- ClassLoader
- Class

## ClassLoader
如果资源文件存在于src目录下，那么直接使用getResourceAsStream("a.txt")，就可以获取文件资源；
如果资源文件存在于其它包目录下，假如存在于包web/day1_3下，那么将文件路径写全即可getResourceAsStream("web/day1_3/a.txt")。

代码示例：

```Java
public class BServlet extends HttpServlet {

    protected void doGet(HttpServletRequest request, HttpServletResponse response) throws ServletException, IOException {
       ClassLoader classLoader = this.getClass().getClassLoader();
       InputStream input = classLoader.getResourceAsStream("web/day1_3/a.txt");
        byte[] data = new byte[1024];
        int len = 0;
        while((len = input.read(data)) != -1) {
            String line = new String(data,0,len);
            System.out.println(line);
        }
        input.close();
    }
}
```

## Class
Class是相对于classes文件来说的。`.java`文件会被编译成`.class`文件存放于classes目录下。

如果资源文件与调用该文件的`.class`文件处于同一路径，那么直接使用getResourceAsStream("a.txt")即可；

如果处于不同路径，例如存放于src目录下，那么使用getResourceAsStream("/a.txt")；

如果处于其它包下，例如存放于/web/day1_2下，而`.class`文件位于/web/day1_3下，那么使用getResourceAsStream("/web/day1_2/a.txt")即可。

代码示例：

```Java
public class BServlet extends HttpServlet {

    protected void doGet(HttpServletRequest request, HttpServletResponse response) throws ServletException, IOException {
        
        Class cl = this.getClass();
        InputStream input = cl.getResourceAsStream("/web/day1_2/a.txt");
        byte[] data = new byte[1024];
        int len = 0;
        while((len = input.read(data)) != -1) {
            String line = new String(data,0,len);
            System.out.println(line);
        }
        input.close();
    }
}
```