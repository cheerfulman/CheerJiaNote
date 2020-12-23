## JVM参数调优

### JVM参数查看

通过jps 和 jinfo进行查看

```text
-Xms: 初始堆空间  ---- 等价于 -XX:InitialHeapSize
-Xmx: 堆最大值	  ---- 等价于 -XX:MaxHeapSize
-Xss: 栈空间	   ---- 等价于 -XX:ThreadStackSize
```

-Xms 和 -Xmx最好调整一致，防止JVM频繁进行收集和回收

### JVM参数类型

+ 标配参数

  + -version --- java -version (查看Java版本)
  + -help    ---- java -help （java命令帮助）
  + java -showversion

+ X参数

  + -Xint: 解释执行
  + -Xcomp: 第一次使用就编译成本地代码
  + -Xmixed：混合模式

+ **XX参数**

  + Boolean类型

    + -XX: + 表示开启，-表示关闭； 例如-XX：-PrintGCDetails: 表示关闭GC输出详情

    +PrintGCDetails表示开启。（下图为开启）

    ![image-20200817100947600](https://cdn.jsdelivr.net/gh/cheerfulman/PigGo-img/img/20200817100947.png)

  + key-value类型

    + 公式：-XX:属性Key=属性值value(比如设置堆内存等)
    + Case: -XX: MetaspaceSize=1024m

    修改前：

    ![image-20200817101303243](https://cdn.jsdelivr.net/gh/cheerfulman/PigGo-img/img/20200817101303.png)

    修改后：-XX: MetaspaceSize=1024m

    ![image-20200817101507885](https://cdn.jsdelivr.net/gh/cheerfulman/PigGo-img/img/20200817101507.png)

```text
jinfo -flag 参数 进程号
jinfo -flags 进程号  （查看所有参数）
```

如：查询默认多大年龄去老年区

![image-20200817101729366](https://cdn.jsdelivr.net/gh/cheerfulman/PigGo-img/img/20200817101729.png)

如上图 ---- 默认 15 次；

### JVM默认参数

可通过-XX:+PrintFlagsInitial查看；

> java -XX:+PrintFlagsInitial（重要参数）
>
> java -XX:+PrintFlagsInitial -version

![image-20200817103100505](https://cdn.jsdelivr.net/gh/cheerfulman/PigGo-img/img/20200817103100.png)

如果其为**:=** 则表示**修改后的值**，若为**=** 则表示**初始值**

### JVM常用配置

> 回顾JVM

![image-20200817104246733](https://cdn.jsdelivr.net/gh/cheerfulman/PigGo-img/img/20200817104246.png)

>  jdk1.8后元空间不再属于虚拟机，而是使用本地内存，故只受限于本地内存，为防止频繁实例化对象元空间出现OOM,可把元空间设置大一点；

#### 查看堆内存

通过代码查看堆内存大小，可用-Xms和-Xmx设置

```java
public class Heap {
    public static void main(String[] args) {
        long l = Runtime.getRuntime().maxMemory();
        long l1 = Runtime.getRuntime().totalMemory();

        System.out.println("虚拟机最大内存 ：" + l/(double)1024/1024 + "MB");
        System.out.println("虚拟机初始化内存 ：" + l1/(double)1024/1024 + "MB");
    }
}
```

执行结果：

```text
虚拟机最大内存 ：3824.0MB
虚拟机初始化内存 ：240.0MB
```

**一般情况下**： -Xms 初始堆内存为：物理内存的1/64 -Xmx 最大堆内存为：系统物理内存的 1/4

#### 常用参数(必记)

+ -Xms: 默认为物理内存的1/64，等价于 -XX:initialHeapSize
+ -Xmx: 最大堆内存，默认为物理内存的1/4，等价于-XX:MaxHeapSize
+ -Xss: 设计单个线程栈的大小，一般默认为512K~1024K，等价于 -XX:ThreadStackSize
+ -Xmn: 设置年轻代大小
+ -XX:MetaspaceSize: 设置元空间大小
+ -XX:PrintGCDetails： 详细输出GC日志信息

#### 初始栈空间

```java
public class Heap {
    public static void main(String[] args) {
        int i = 3;
        while(i == 3){}
    }
}
```

通过：

```text
jps -l 查看进程号
D:\project\Annotation>jps -l
10352 jvm.Heap
13008 sun.tools.jps.Jps
18180 org.jetbrains.idea.maven.server.RemoteMavenServer36
13768
1432 org.jetbrains.jps.cmdline.Launcher
19260 org.jetbrains.kotlin.daemon.KotlinCompileDaemon
------------------------------------------------------
然后通过 
D:\project\Annotation>jinfo -flag ThreadStackSize 10352
-XX:ThreadStackSize=0
```

发现**-XX:ThreadStackSize=0**  为什么等于0呢？

> 这个值的大小是取决于平台的
>
> Linux/x64:1024KB
>
> OS X：1024KB
>
> Oracle Solaris：1024KB
>
> Windows：取决于虚拟内存的大小

#### -XX:SurvivorRatio

调整新生代eden 和 S0、S1的空间比例，默认为 -XX:SuriviorRatio=8，Eden:S0:S1 = 8:1:1

```text
-XX:SurvivorRatio=4 则为4:1:1
-XX:SurvivorRatio=8(默认) 则为8:1:1
```

#### -XX:NewRatio

配置新生区和老年区大小默认 1：2

```text
-XX:NewRatio=2(默认) 则新生代老年代比例为 1:2
-XX:NewRatio=4 则新生代老年代比例为 1:4
```

#### -XX:MaxTenuingThreshold

设置垃圾最大年龄，如果-XX:**MaxTenuingThreshold=0** 则不经过年轻代直接进入老年代，对于老年代较多的应用可以提高效率；如果设置较大值，则对象会在Survivor区进行多次复制增加存活时间；

```text
-XX:MaxTenuingThreshold=15 (默认) 15次 --- 只能在0-15次之间
```

