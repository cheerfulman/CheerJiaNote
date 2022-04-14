## Java内存溢出OOM

### 常见JavaOOM

+ StackOverflowError: 栈溢出
+ Java heap space : 堆溢出

![image-20200819093841638](https://cdn.jsdelivr.net/gh/cheerfulman/PigGo-img/img/20200819093841.png)

### 架构

OutOfMemoryError和StackOverflowError是属于Error，不是Exception，即使有时候我们口语化：报**栈溢出异常**了；

![image-20200819094521090](https://cdn.jsdelivr.net/gh/cheerfulman/PigGo-img/img/20200819094521.png)

### StackoverFlowError

栈溢出很简单，我们只要不断的调用函数递归，没有出口即可爆栈；

一般栈大小为 512K ~ 1024K;

```java
/**
 * @title: StackoverFlowTest
 * @Author CheerJia
 * @Date: 2020/8/19 9:47
 * @Version 1.0
 */
public class StackOverFlowTest {
    private static void f(){
        f();
    }
    // Exception in thread "main" java.lang.StackOverflowError
    public static void main(String[] args) {
        f();
    }
}
```

运行结果：

```text
Exception in thread "main" java.lang.StackOverflowError
	at jvm.StackoverFlowError.StackOverFlowTest.f(StackOverFlowTest.java:11)
```

### OutOfMemoryError

###  java heap space

创建对象等，会在堆中开辟一段空间，我们只需创建一个大对象即可；

先设置VM参数调整堆内存大小：

![image-20200819095546167](https://cdn.jsdelivr.net/gh/cheerfulman/PigGo-img/img/20200819095627.png)

代码：

```java
/**
 * @title: OutOfMemoryErrorTest
 * @Author CheerJia
 * @Date: 2020/8/19 9:51
 * @Version 1.0
 */
public class OutOfMemoryErrorTest {
    public static void main(String[] args) {
        // 直接11M撑爆 堆
        byte[] bytes = new byte[11 * 1024 * 1024];
    }
}
```

运行结果：

```text
Exception in thread "main" java.lang.OutOfMemoryError: Java heap space
	at jvm.OutOfMemoryError.OutOfMemoryErrorTest.main(OutOfMemoryErrorTest.java:11)
```

###  GC overhead limit exceeded

我们的应用程序主要用在处理业务上的，而不是大量的时间都花在GC上；

GC回收时间过长时会抛出OutOfMemoryError，过长的定义是，超过了98%的时间用来做GC，并且回收了不到2%的堆内存

连续多次GC都只回收了不到2%的极端情况下，才会抛出。假设不抛出GC overhead limit 错误会造成什么情况呢？

那就是GC清理的这点内存很快会再次被填满，迫使GC再次执行，这样就形成了恶性循环，CPU的使用率一直都是100%，而GC却没有任何成果。

![image-20200819100134460](https://cdn.jsdelivr.net/gh/cheerfulman/PigGo-img/img/20200819100141.png)

设置VM参数：

```text
-Xms10m -Xmx10m -XX:+PrintGCDetails -XX:MaxDirectMemorySize=5m
```

代码演示：

```java
/**
 * @title: GCOverheadLimitDemo
 * @Author CheerJia
 * @Date: 2020/8/19 10:06
 * @Version 1.0
 */
public class GCOverheadLimitDemo {
    public static void main(String[] args) {
        int i = 0;
        List<String> list = new ArrayList<>();
        try {
            while(true) {
                list.add(String.valueOf(++i).intern());
            }
        } catch (Exception e) {
            System.out.println("***************i:" + i);
            e.printStackTrace();
            throw e;
        } finally {

        }
    }
}
```

运行结果：

```text
[Full GC (Ergonomics) [PSYoungGen: 2047K->2047K(2560K)] [ParOldGen: 7106K->7106K(7168K)] 9154K->9154K(9728K), [Metaspace: 3504K->3504K(1056768K)], 0.0311093 secs] [Times: user=0.13 sys=0.00, real=0.03 secs] 
[Full GC (Ergonomics) [PSYoungGen: 2047K->0K(2560K)] [ParOldGen: 7136K->667K(7168K)] 9184K->667K(9728K), [Metaspace: 3540K->3540K(1056768K)], 0.0058093 secs] [Times: user=0.00 sys=0.00, real=0.01 secs] 
Heap
 PSYoungGen      total 2560K, used 114K [0x00000000ffd00000, 0x0000000100000000, 0x0000000100000000)
  eden space 2048K, 5% used [0x00000000ffd00000,0x00000000ffd1c878,0x00000000fff00000)
  from space 512K, 0% used [0x00000000fff80000,0x00000000fff80000,0x0000000100000000)
  to   space 512K, 0% used [0x00000000fff00000,0x00000000fff00000,0x00000000fff80000)
 ParOldGen       total 7168K, used 667K [0x00000000ff600000, 0x00000000ffd00000, 0x00000000ffd00000)
  object space 7168K, 9% used [0x00000000ff600000,0x00000000ff6a6ff8,0x00000000ffd00000)
 Metaspace       used 3605K, capacity 4540K, committed 4864K, reserved 1056768K
  class space    used 399K, capacity 428K, committed 512K, reserved 1048576K

Exception in thread "main" java.lang.OutOfMemoryError: GC overhead limit exceeded
```

可以看到一次Full GC根本就没清理多少内存，[PSYoungGen: 2047K->2047K(2560K)] 甚至不到1K（前后都是2047K）；

###  Direct buffer memory

一般是由Netty NIO引起的；

NIO程序我们会使用ByteBuffer来读取或写入数据，这是一种基于通道(Channel) 与 缓冲区(Buffer)的I/O方式，它可以使用Native函数库直接分配堆外内存，然后存储在**Java堆**里面的**DirectByteBuffer**对象作为这块内存的引用进行操作。这样可以提高性能，避免Java堆和Native堆中来回复制数据；

**ByteBuffer.allocate(capability)**：第一种方式是分配JVM堆内存，属于GC管辖范围，由于需要拷贝所以速度相对较慢

**ByteBuffer.allocteDirect(capability)**：第二种方式是分配OS本地内存，不属于GC管辖范围，由于不需要内存的拷贝，所以速度相对较快

如果一直分配堆外内存，JVM不需要GC,DirectByteBuffer对象就不会被回收,如果使用完的话就会出现**OutOfMemoryError**；

VM参数配置： 调整堆外物理内存为5M

```text
-Xms10m -Xmx10m -XX:+PrintGCDetails -XX:MaxDirectMemorySize=5m
```

代码演示：

```java
/**
 * @title: DirectBufferMemoryDemo
 * @Author CheerJia
 * @Date: 2020/8/19 10:55
 * @Version 1.0
 */
public class DirectBufferMemoryDemo {
    public static void main(String[] args) {
        // 创建一个6M对象
        ByteBuffer byteBuffer = ByteBuffer.allocateDirect(6 * 1024 * 1024);
    }
}
```

运行结果：

```text
Exception in thread "main" java.lang.OutOfMemoryError: Cannot reserve 6291456 bytes of direct buffer memory (allocated: 8192, limit: 5242880)
	at java.base/java.nio.Bits.reserveMemory(Bits.java:178)
	at java.base/java.nio.DirectByteBuffer.<init>(DirectByteBuffer.java:119)
	at java.base/java.nio.ByteBuffer.allocateDirect(ByteBuffer.java:320)
	at jvm.OOM.DirectBufferMemory.DirectBufferMemoryDemo.main(DirectBufferMemoryDemo.java:13)
```

###  unable to create new native thread

在高并发场景的时候，会应用到

高并发请求服务器时，经常会出现如下异常`java.lang.OutOfMemoryError:unable to create new native thread`，准确说该native thread异常与对应的平台有关

导致原因:

+ 一个应用创建太多的线程了，超过系统的承载极限
+ 你的服务器不允许你的应用程序创建这么多线程，Linxu默认是单个进程创建1024个线程；

解决办法：

1. 降低线程的数量，思考是否真需要这么多线程；
2. 修改Linux服务器配置，扩大Linux最大线程限制数；

代码：

```java
/**
 * @title: UnableCreateNewThreadDemo
 * @Author CheerJia
 * @Date: 2020/8/19 11:11
 * @Version 1.0
 */
public class UnableCreateNewThreadDemo {
    public static void main(String[] args) {
        for (int i = 1;  ; i++) {
            new Thread(){
                public void run(){
                    try {
                        Thread.sleep(Integer.MAX_VALUE);
                    } catch (InterruptedException e) {
                        e.printStackTrace();
                    }
                }
            }.start();
        }
    }
}
```

运行结果：

```text
Exception in thread "main" java.lang.OutOfMemoryError: unable to create native thread: possibly out of memory or process/resource limits reached
	at java.base/java.lang.Thread.start0(Native Method)
	at java.base/java.lang.Thread.start(Thread.java:799)
	at jvm.OOM.UnableCreateNewThread.UnableCreateNewThreadDemo.main(UnableCreateNewThreadDemo.java:22)
```

查看当前用户的线程限制：

```
ulimit -u
```

###  Metaspace

元空间内存不足，Matespace元空间应用的是本地内存

元空间就是我们的方法区，存放的是**类模板，类信息，常量池**等

Metaspace是方法区HotSpot中的实现，它与持久代最大的区别在于：Metaspace并不在虚拟内存中，而是使用本地内存，也即在java8中，class metadata（the virtual machines internal presentation of Java class），被存储在叫做Matespace的native memory

永久代（java8后背元空间Metaspace取代了）存放了以下信息：

- 虚拟机加载的类信息
- 常量池
- 静态变量
- 即时编译后的代码

VM参数：默认一般为20M，我们调小一点

```text
-XX:MetaspaceSize=8m -XX:MaxMetaspaceSize=8m
```

代码：

```java
public class MetaspaceOutOfMemoryDemo {

    // 静态类
    static class OOMTest {

    }

    public static void main(final String[] args) {
        // 模拟计数多少次以后发生异常
        int i =0;
        try {
            while (true) {
                i++;
                // 使用Spring的动态字节码技术
                Enhancer enhancer = new Enhancer();
                enhancer.setSuperclass(OOMTest.class);
                enhancer.setUseCache(false);
                enhancer.setCallback(new MethodInterceptor() {
                    @Override
                    public Object intercept(Object o, Method method, Object[] objects, MethodProxy methodProxy) throws Throwable {
                        return methodProxy.invokeSuper(o, args);
                    }
                });
            }
        } catch (Exception e) {
            System.out.println("发生异常的次数:" + i);
            e.printStackTrace();
        } finally {

        }

    }
}
```

运行结果:

```text
发生异常的次数: 243
java.lang.OutOfMemoryError:Metaspace
```

