## JUC简单学习

并发和并行：

+ 并发：几个线程快速交替，轮转时间片；
+ 并行：多线程同时执行；线程池；

java 默认启动两个线程：

+ GC线程
+ man线程(主线程)

#### 线程的状态

```java
public static enum State {
    NEW, //新建状态
    RUNNABLE, // 运行状态
    BLOCKED, // 阻塞
    WAITING, // 等待
    TIMED_WAITING, // 超时等待
    TERMINATED; //终止

    private State() {
    }
}
```

#### wait/sleep区别

**1、来自不同的类**

wait --> Object

sleep --> Tread

**2、锁的释放**

wait会释放锁，sleep不会释放

**3、使用的范围**

wait必须在同步代码块中

sleep可以在任何地方

**4、是否需要异常**

wait不需要

sleep 必须捕获异常

## Lock

公平锁：十分公平，先来后到

**非公平锁：可以插队（默认）**

![image-20200414110914178](C:\Users\Administrator\AppData\Roaming\Typora\typora-user-images\image-20200414110914178.png)

**Lock使用方式：**

```java
private final ReentrantLock lock = new ReentrantLock();
lock.lock();
try{
    业务代码
}catch(){
    
}finally{
    lock.unlock();
}
```

> synchronized  和 Lock 的区别

+ synchronized 是内置关键字，Lock是java的一个类
+ synchronized  无法判断获取锁的状态，Lock 可以判断
+ synchronized 可以自动释放锁，lock必须手动释放，否则锁
+ synchronized  可重入锁，不可以中断，非公平； Lock，可重入锁，可以判断锁，非公平(可以自己设置)
+ synchronized 适合少量代码同步问题，Lock大量；

**使用Lock完成数字加减实例：**

**Condition可以实现精准唤醒**

```java
public class Nums {
    public static void main(String[] args) {
        num num = new num();
        // 四个线程
        new Thread(() ->{
            for(int i = 0; i < 20; i++)num.increment();
        },"A").start();
        new Thread(() ->{
            for(int i = 0; i < 20; i++)num.decrement();
        },"B").start();

        new Thread(() ->{
            for(int i = 0; i < 20; i++)num.increment();
        },"C").start();
        new Thread(() ->{
            for(int i = 0; i < 20; i++)num.decrement();
        },"D").start();
    }
}
class num{
    int num = 0;
    ReentrantLock lock = new ReentrantLock();
    Condition condition = lock.newCondition();
    void increment(){
        lock.lock();
        try {
            // 业务代码  if存在虚假唤醒
            while(num != 0)condition.await();
            num ++;
            System.out.println(Thread.currentThread().getName() + "当前数字为:" + num);
            // 唤醒
            condition.signalAll();
        }catch (Exception e){
            e.printStackTrace();
        }finally {
            lock.unlock();
        }
    }
    void decrement(){
        lock.lock();

        try {
            while(num == 0)condition.await();
            num --;
            System.out.println(Thread.currentThread().getName() + "当前数字为:" + num);
            condition.signalAll();
        }catch (Exception e){
            e.printStackTrace();
        }finally {
            lock.unlock();
        }
    }
}
```

## 安全的集合

**CopyOnWriteArrayList<>()** : add源码

+ 写入时复制
+ 读写分离

```java
public boolean add(E e) {
    synchronized(this.lock) {
        Object[] es = this.getArray();
        int len = es.length;
        es = Arrays.copyOf(es, len + 1);
        es[len] = e;
        this.setArray(es);
        return true;
    }
}
```

**Collections.synchronizedList(new ArrayList<String>())** 



> HashSet底层是什么

**new HashSet()**源码：S

```java
public HashSet() {
    this.map = new HashMap();
}
// 底层就是 map

private static final Object PRESENT = new Object();
public boolean add(E e) {
    return this.map.put(e, PRESENT) == null;
}
// add的时候，就是利用了map的 put，value为常量。
// 也就是说set，就是利用map的 key 原理的。
```

**并发集合：**

![](C:\Users\Administrator\AppData\Roaming\Typora\typora-user-images\image-20200414131941217.png)

**ConcurrentHashMap<>()**





## Callabele

**FutureTask 和Callable 关系：**

首先FutureTask 继承 了**RunnableFuture<V>`** 而RunnableFuture 内部调用run()方法。 **故 FutureTask 也是调用run方法**

**在run中重新定义Callable；**

```java
public void run() {
    ......
        try {
            Callable<V> c = callable;
            if (c != null && state == NEW) {
                V result;
                boolean ran;
                try {
                    result = c.call();
                    ran = true;
                } catch (Throwable ex) {
                    ......
                }
                if (ran)
                    set(result);
            }
        } finally {
            ......
        }
}
```

![image-20200414151534602](C:\Users\Administrator\AppData\Roaming\Typora\typora-user-images\image-20200414151534602.png)

小demo:

```java
public static void main(String[] args) throws Exception {
    FutureTask<String> ft = new FutureTask<String>(() -> {
        System.out.println("执行 call() 方法");
        return "123";
    });

    new Thread(ft,"A").start();
    new Thread(ft,"B").start(); // 有缓存，不会直接输出
    System.out.println(ft.get());
}
```



## CountDownLatch 计数器(减法)

主要两个方法: countDown()和 await()

直接看demo：

```java
public static void main(String[] args) throws InterruptedException {
    CountDownLatch countDownLatch = new CountDownLatch(5);
    for(int i = 1; i <= 10; i++){
        new Thread(() -> {
            System.out.println("我爱你");
            countDownLatch.countDown(); // 数量减一
        }).start();
    }
    countDownLatch.await(); // 等待计数器归零，然后再向下执行
    System.out.println("你慢慢池");
}
```

## CyclicBarrier (加法)

```java
CyclicBarrier cyclicBarrier = new CyclicBarrier(7,() -> {
            System.out.println("迪迦奥特曼变身成功！！！");
        });
        for(int i = 0; i < 7; i ++){
            final int temp = i;
            new Thread(() -> {
                System.out.println("变身动画 第"  + temp  + "秒持续中");
                try {
                    cyclicBarrier.await();
                } catch (InterruptedException e) {
                    e.printStackTrace();
                } catch (BrokenBarrierException e) {
                    e.printStackTrace();
                }
            }).start();
        }
```

## Semaphore 信号量

使其最多只有固定的数量运行；

主要方法：

+ acquire(): 获得

+ release(): 释放

```java
Semaphore semaphore = new Semaphore(5);
for(int i = 0; i < 20; i++){
    new Thread(() -> {
        try {
            semaphore.acquire();
            System.out.println(Thread.currentThread().getName() + "在运行队列中");
            TimeUnit.SECONDS.sleep(1);
            System.out.println(Thread.currentThread().getName() + "执行完毕");

        } catch (InterruptedException e) {
            e.printStackTrace();
        }finally {
            semaphore.release();
        }

    }).start();
}
```



## ReentrantReadWriteLock 读写锁

独占锁（写锁） ： 一次只能被一个线程占有

共享锁（读锁）：多个线程可以同时占有

```java
public class ReetWriLoc {
    public static void main(String[] args) {
        MyCahe myCahe = new MyCahe();

        for(int i = 1; i <= 10; i++){
            int tt = i;
            new Thread(() -> {
                myCahe.put(tt,tt);
            },String.valueOf(i)).start();
            new Thread(() -> {
                myCahe.get(tt);
            },String.valueOf(i)).start();

        }
    }
}
class MyCahe{
    private final ReadWriteLock lock = new ReentrantReadWriteLock();
    HashMap<Object, Object> map = new HashMap<>();
    // 一人写
    void put(Object temp,Object temp1){
        lock.writeLock().lock();

        try {
            System.out.println(Thread.currentThread().getName() + " 正在写");
            map.put(temp,temp1);
            System.out.println(Thread.currentThread().getName() + " 写完啦");
        } catch (Exception e) {
            e.printStackTrace();
        } finally {
            lock.writeLock().unlock();
        }

    }
    // 所有人都可以读
    void get(Object key){
        lock.readLock().lock();

        try {
            System.out.println(Thread.currentThread().getName() + " 正在读");
            map.get(key);
            System.out.println(Thread.currentThread().getName() + " 读完啦");
        } catch (Exception e) {
            e.printStackTrace();
        } finally {
            lock.readLock().unlock();
        }

    }
}
```

## 阻塞队列

```java
ArrayBlockingQueue bque = new ArrayBlockingQueue<>(3);// 阻塞队列，设置值大小。
```

四组API：

| 方式 | 抛出异常  | 有返回值，不抛异常 | 阻塞等待 | 超时等待              |
| ---- | --------- | ------------------ | -------- | --------------------- |
| 添加 | add()     | offer()            | put()    | offer(值，时间，单位) |
| 异常 | remove()  | poll()             | take()   | poll(时间,单位)       |
| 队首 | element() | peek()             |          |                       |

![image-20200414193110663](C:\Users\Administrator\AppData\Roaming\Typora\typora-user-images\image-20200414193110663.png)

```
SynchronousQueue<String> b = new SynchronousQueue<String>(); // 大小为1
```

## 线程池

线程池：三大方法、七大参数、四种拒绝策略

> 池化技术	

程序运行的本质：占用系统资源，优化资源的使用 => 池化技术

线程池、连接池、内存池、对象池////....

池化技术：事先准备好，有人要用，就来拿，用完后还给我；



**线程池的好处：**

1. 降低资源的消耗
2. 提高响应速度
3. 方便管理

**线程复用、控制最大并发数、管理线程**

> 线程池三大方法

```java
ExecutorService ThreadPoll = Executors.newSingleThreadExecutor(); // 单个线程
ExecutorService ThreadPoll = Executors.newCachedThreadPool(); // 尽可能多
ExecutorService ThreadPoll = Executors.newFixedThreadPool(5); // 自定义
```

> 七大参数

![image-20200414202826506](C:\Users\Administrator\AppData\Roaming\Typora\typora-user-images\image-20200414202826506.png)

线程池创建源码：

```java
public static ExecutorService newFixedThreadPool(int nThreads) {
    return new ThreadPoolExecutor(nThreads, nThreads, 0L, TimeUnit.MILLISECONDS, new LinkedBlockingQueue());
}

public static ExecutorService newCachedThreadPool() {
    return new ThreadPoolExecutor(0, 2147483647, 60L, TimeUnit.SECONDS, new SynchronousQueue());
}

public static ExecutorService newSingleThreadExecutor() {
    return new Executors.FinalizableDelegatedExecutorService(new ThreadPoolExecutor(1, 1, 0L, TimeUnit.MILLISECONDS, new LinkedBlockingQueue()));
}

其都调用了new ThreadPoolExecutor（七参数）;


public ThreadPoolExecutor(int corePoolSize,  // 核心线程池数
                          int maximumPoolSize, // 最大线程池
                          long keepAliveTime,  // 多长时间不调用，则释放(多余的线程池)
                          TimeUnit unit, // 时间单位
                          BlockingQueue<Runnable> workQueue,  // 阻塞队列
                          ThreadFactory threadFactory,  // 线程工厂，创建线程用的
                          RejectedExecutionHandler handler) // 拒绝策略
```

常用方式创建

```java
ThreadPoolExecutor ThreadPool = new ThreadPoolExecutor(
    3,   // 核心池
    5,  // 最大池
    2,  // 多长时间后 没有线程 处理则关闭
    TimeUnit.SECONDS,new ArrayBlockingQueue<>(3), //阻塞队列
    Executors.defaultThreadFactory(), // 默认处理工厂
    new ThreadPoolExecutor.AbortPolicy() // 拒绝方式,  会抛出异常
);
```

> 四种拒绝策略

```java
new ThreadPoolExecutor.AbortPolicy() //  会抛出异常
new ThreadPoolExecutor.CallerRunsPolicy() // 哪来回哪去， mian线程处理
new ThreadPoolExecutor.DiscardPolicy() // 不会抛出异常 丢弃任务
new ThreadPoolExecutor.DiscardOldestPolicy() // 不会抛出异常，尝试竞争，成功则执行，失败则抛弃 
```

#### 最大线程池如何设置？

1、CPU密集型：几核的cpu就定义为几，可以保证效率最高；

2、I/O 密集型： 比如 一个程序中有 15 个大型任务， io十分占资源，所以要判断耗io的线程，要大于此数。



## 四大函数式接口

lambda表达式、链式编程、函数式接口、Stream流式计算



> function 函数型接口

```java
// 函数型 接口
Function<String, String> function = new Function<>() {
    @Override
    public String apply(String s) {
        return s;
    }
};
Function<String, String> function1 = (s) -> s;
System.out.println(function1.apply("aaaaaaasss"));
```



> predicate 断言型接口

```java
// 断言型接口
//        Predicate<String> predicate = new Predicate<String>() {
//            @Override
//            public boolean test(String s) {
//                return s.isEmpty();
//            }
//        };
Predicate<String> predicate = (s) -> s.isEmpty();
System.out.println(predicate.test("aaa"));
```



> Consumer 消费型接口 

```java
//消费型接口 没有返回值
//        Consumer<String> consumer = new Consumer<>() {
//            @Override
//            public void accept(String s) {
//                System.out.println(s);
//            }
//        };
Consumer<String> consumer = (s) -> System.out.println(s);
consumer.accept("qwerqwer");
```



> Supplier 供给型接口

```java
//        Supplier<String> supplier = new Supplier<>() {
//            @Override
//            public String get() {
//                return "我对你";
//            }
//        };
Supplier<String> supplier = () -> "kkk";

System.out.println(supplier.get());
```



## Stream 流式计算

```java
ArrayList<User> list = new ArrayList<User>();
list.add(new User(1,"a",18));
list.add(new User(2,"b",28));
list.add(new User(3,"c",29));
list.add(new User(4,"d",30));
list.add(new User(9,"o",13));


list.stream().filter(u -> u.age % 2 == 0).filter(u -> u.age > 19).map(u -> u.name.toUpperCase()).sorted((u1,u2) -> u2.compareTo(u1)).forEach(System.out::println);
```

## ForkJoin

ForkJoin 在jdk1.7，并发执行任务！ 提高效率。大量数据！

大数据： Map Reduce （把大任务分成小任务）



**ForkJoin特点：工作窃取**

![image-20200414215927706](C:\Users\Administrator\AppData\Roaming\Typora\typora-user-images\image-20200414215927706.png)

## 理解CAS

CAS： 比较当前工作内存中的值，如果这个值是期望的，那么就执行该操作，否则就会一直循环（自旋锁）

缺点：

1、循环会耗时

2、一次只能保证一个共享变量的原子性

3、ABA问题



## 原子引用

有版本号Stamp防止aba问题

```java
static AtomicStampedReference<Integer> atomicReference = new AtomicStampedReference<>(1, 0);
public static void main(String[] args) {
    new Thread(() -> {
        int stamp = atomicReference.getStamp();
        System.out.println("a1=>" + atomicReference.getStamp());

        try {
            TimeUnit.SECONDS.sleep(1);
        } catch (InterruptedException e) {
            e.printStackTrace();
        }

        atomicReference.compareAndSet(1,5,
                                      atomicReference.getStamp(),atomicReference.getStamp() + 1);
        System.out.println("a2=>" + atomicReference.getStamp());

        System.out.println(atomicReference.compareAndSet(5, 1,
                                                         atomicReference.getStamp(), atomicReference.getStamp() + 1));

        System.out.println("a3=>" + atomicReference.getStamp());

    }).start();


    new Thread(() -> {
        int stamp = atomicReference.getStamp();
        System.out.println("b1=>" + atomicReference.getStamp());
        try {
            TimeUnit.SECONDS.sleep(2);
        } catch (InterruptedException e) {
            e.printStackTrace();
        }
        System.out.println(atomicReference.compareAndSet(1, 4, stamp,
                                                         stamp + 1));

        System.out.println("b1=>" + atomicReference.getStamp());

    }).start();
}
```

## 各种锁

**公平锁**：不能插队 就是说A 获得了锁，B来了，C来了，A释放锁后，一定是B拿到锁。

**非公平锁**：可插队，A释放后，B和C进行竞争。



**可重入锁**：可以进入自己锁内的锁。 **锁内维护了一个线程标记示，标记是否是同一个线程，关联了一个计数器，每进入一个锁计数器+1，释放一个锁-1，最后为0；**



**自旋锁**：未抢到锁时，不会马上阻塞会尝试去竞争（默认10次）**如果一直未竞争到锁则会被阻塞。（自旋锁利用cpu时间换取线程阻塞与调度的开销，也很有可能这些cpu的时间白白浪费）**



**乐观锁** ：访问记录前不会加锁，数据更新时会正式对数据冲突与否进行检验

**悲观锁** ：在数据被处理之前先对数据进行加锁。

## synchronized 和 volatile

**synchronized** : 既保证了内存可见性，也保证了原子性

**volatile** ： 轻量级synchronized，只保证内存可见性，防止指令重排

---

**synchronized 内存语义**：解决共享变量内存可见性问题

**进入synchronized的语义**：把在synchronized块内使用的变量从线程工作内存中清除，直接从主内存中获取

**进入synchronized的语义**：把synchronized块内的对共享变量的修改刷新到主内存中。

**Volatile**：不在是从前的把值缓存到寄存器或其它地方，而是直接刷新回贮存，而其他线程读取时，直接从内存读取。





![image-20200415232434189](C:\Users\Administrator\AppData\Roaming\Typora\typora-user-images\image-20200415232434189.png)

jps -l     ： 查看进程号

jstack 进程号 ：查看死锁信息



## 伪共享

> 什么是伪共享

为了解决cpu之间运行速度差的问题，会在cpu和主内存之间添加一级或多级高速缓冲器。

Cache内部是按行存储的，每一行称为Cache行。Cache行是Cache与主内存进行数据交换的单位



当cpu访问某个变量时，会先去cpu Cache查看是否有该变量，如果有则直接获取，否则去主内存中获取。由于存放在Cache行中的是多个变量，多个线程同时修改一个缓存行里的多个变量时，由于同时只能由一个线程操作缓存行，所以相比**每个变量放到一个缓存行中，性能会下降，这就是伪共享。**



> 假设由线程A和B，它们都有自己私有的cache 1，和共享的cache2,和主内存
>
> 主内存中有x,y。
>
> cache1 （cpu1-- 线程A,cpu2--线程B ）和cache2 (线程A,线程B)也有。
>
> A在使用cpu1对X进行更新时，会修改cpu1 中的缓存x，这是 cpu2变量x对应的缓存行失效，就要从cache2中读，就破坏了一级缓存，而一级缓存比二级缓存快。
>
> 这也就说明多个线程不能同时修改自己所使用Cpu中相同缓存行的变量。
>
> **更坏的情况就是，当只有一级缓存时 ：频繁的访问主内存。**

地址连续会放到同一个缓存行，例如数组执行会更快，因为连续，访问更快。

**如何避免伪共享：** 字节填充，保证一个缓存行放一个

