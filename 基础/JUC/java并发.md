# java并发基础

## Callable

首先我们来理一理Future和Runable的关系：

`public class FutureTask<V> implements RunnableFuture<V>`

```java
public interface RunnableFuture<V> extends Runnable, Future<V> {       
    void run(); 
}
```

也就是说`FutureTask` 间接的 实现了`Runable`

而利用`FutureTask`:我们就有了线程进行，取消、判断线程运行状态、获取结果等功能。实现如下：

```java
public interface Future<V> {       

		boolean cancel(boolean mayInterruptIfRunning);       

		boolean isCancelled();       

		boolean isDone();       

		V get();       

		V get(long timeout, TimeUnit unit); 

}
```



而`FutureTask` 间接的 实现了`Runable` ： 故我们调用 的 `Callable` 中的`call`方法，其实是在`Runable`里面的`run()`方法中执行的；

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

接下来是HelloCallabe的简单操作：

```java
public class HelloThread{
    public static void main(String[] args) throws ExecutionException, InterruptedException {
        CallableTest callableTest = new CallableTest();
        FutureTask<Integer> ft = new FutureTask<Integer>(callableTest);

        Thread thread = new Thread(ft);
        thread.start();
        System.out.println(ft.get());
    }
}

class CallableTest implements Callable<Integer>{
    @Override
    public Integer call() throws Exception {
        return 123;
    }
}
```

## 线程的停止stop()

想要线程停止，我们最好在内部调用使其停止，简单实现如下：

结合`Thred.sleep()`的一个倒计时功能；

**具体还是`stop()`方法**

```java
public class ThreadStop {
    public static void main(String[] args) throws InterruptedException {
        Test test = new Test();
        new Thread(test).start();
        for(int i = 0; i < 10; i++){
            Thread.sleep(1000);
            System.out.println(Thread.currentThread().getName() + i);
        }
        test.stop();
    }
}
class Test implements Runnable{
    boolean flag = true;
    @Override
    public void run() {
        while(flag){
            try {
                Thread.sleep(1000);
                Date date = new Date();
                System.out.println(new SimpleDateFormat("HH:mm:ss").format(date));
            } catch (InterruptedException e) {
                e.printStackTrace();
            }
        }
    }
    public void stop(){
        this.flag = false;
    }
}
```



## 线程的礼让`yield()`

对静态方法 Thread.yield() 的调用声明了当前线程已经完成了生命周期中最重要的部分，可以切换给其它线程来执行。该方法只是对线程调度器的一个建议，而且也只是建议具有相同优先级的其它线程可以运行。

```JAVA
public class Yield {
    public static void main(String[] args) {
        new Thread(new R(),"a").start();
        new Thread(new R(),"b").start();
    }
}

class R implements Runnable{

    @Override
    public void run() {
        System.out.println("+++++++++开始了++++++++++++" + Thread.currentThread().getName());
        Thread.yield();
        System.out.println("+++++++++结束++++++++++++" + Thread.currentThread().getName());
    }
}
```

## 线程的强制插入`Join()`

```java
public class Join {
    public static void main(String[] args) throws InterruptedException {
        Thread thread = new Thread(new R1());
        thread.start();
        for (int i = 0; i < 100; i++) {
            System.out.println(Thread.currentThread().getName() + i);
            if(i == 20)thread.join();
        }
    }
}
class R1 implements Runnable{

    @Override
    public void run() {
        int i = 100;
        while(i-- > 0){
            System.out.println("+++++++++开始了++++++++++++" + Thread.currentThread().getName());
            System.out.println("+++++++++结束++++++++++++" + Thread.currentThread().getName());
        }
    }
}
```

**使用state观察线程状态：**

```java
Therad.getState();
System.out.println(state);
```

## 线程中断Interrupt()

`Interrupt()`:中断线程，如果此线程正在使用`Object.wait()`,`Thread.join(),`   `Thread.slepp()` 则会抛出`InterruptException`异常。

+ `void interrupt()`方法：  当线程A运行时，线程B可以调用线程A的Interrupt方法设置线程A的标志true并立即返回。**设置标志仅仅是设置标志它并不会中断该线程，也就是说它会线程A会继续执行下去。如果线程没有被阻塞，这时调用 interrupt()将不起作用，直到执行到wait(),sleep(),join()时,才马上会抛出 InterruptedException。**

+ `boolean isinterrupt()` : 判断当前线程是否被中断。
+ `boolean interrupt() `:  判断当前线程是否被中断，如果是中断则清除标记。

 

**当线程为了等待某个特定条件到来时，一般会使用wait(),join()函数来阻塞挂起，比如sleep(3000),如果这个条件在3秒内就到来了，那么后面的时间就浪费了，这时就可以使用`interrupt()` 抛出异常返回，线程恢复激活状态。**

```java
public class InterruptThread {
    public static void main(String[] args) throws InterruptedException {
        Thread thread = new Thread(() -> {
            while(!Thread.currentThread().isInterrupted()) System.out.println(Thread.currentThread() + " hello");
        });

        thread.start();

        Thread.sleep(1000);

        System.out.println("main thread is interrupt thread");
        thread.interrupt();

        thread.join();
        System.out.println("main is over");
    }
}
```



## 线程设置优先级`setPriority()`

```java
public class Priority {
    public static void main(String[] args) {
        Thread t1 = new Thread(new R3(),"t1");
        Thread t2 = new Thread(new R3(),"t2");
        Thread t3 = new Thread(new R3(),"t3");
        Thread t4 = new Thread(new R3(),"t4");
        Thread t5 = new Thread(new R3(),"t5");
        Thread t6 = new Thread(new R3(),"t6");
        t1.setPriority(1);
        t1.start();
        t2.setPriority(2);
        t2.start();
        t3.setPriority(3);
        t3.start();
        t4.setPriority(5);
        t4.start();
        t5.setPriority(7);
        t5.start();
        t6.setPriority(10);
        t6.start();
    }
}
class R3 implements Runnable{

    @Override
    public void run() {
        try {
            Thread.sleep(10);
            System.out.println(Thread.currentThread().getName() + "正在运行 " + Thread.currentThread().getPriority());
        } catch (InterruptedException e) {
            e.printStackTrace();
        }

    }
}
```

## 守护线程deamon

+ 线程分为**用户线程**和**守护线程**
+ 虚拟机必须确保用户线程执行完毕
+ 虚拟机不用等待守护线程执行完毕
+ 如，操作日志，监控内存，垃圾回收等

## 锁`synchronized` 

+ 锁方法
+ 索同步代码块（对象）

## 锁`Lock`

+ Lock只能锁代码块，但是其性能更好

```java
public class HelloTecket {
    public static void main(String[] args) throws InterruptedException {
        Tic tic = new Tic();
        new Thread(tic,"t1").start();
        new Thread(tic,"t2").start();
        new Thread(tic,"t3").start();


    }
}

class Tic implements Runnable{
    static int ticket = 10;
    static boolean flag = true;
    private final ReentrantLock lock = new ReentrantLock();
    @Override
    public void run() {
        while(flag){
            try{
                lock.lock();
                if(ticket > 0){
                    try {
                        Thread.sleep(100);
                    } catch (InterruptedException e) {
                        e.printStackTrace();
                    }
                    System.out.println("售出了第" + ticket-- + " 张票" + Thread.currentThread().getName());
                }else{
                    stop();
                }
            }finally {
                lock.unlock();
            }
        }
    }
    private static void stop(){
        flag = false;
    }
}
```

## 多线程生产者消费者简单模型

```java
public class Pro {
    public static void main(String[] args) throws InterruptedException {
        Food food = new Food();
        new Thread(new Producer(food)).start();
        new Thread(new Customer(food)).start();
    }

}

enum F{
    APPLE("苹果"),CHEERY("樱桃"),MANGO("芒果"),WATERMELON("西瓜");
    private String name;

    private F(String name){
        this.name = name;
    }
}

class Food{
    private String[] Fruit = new String[12];
    int count = 0;
    private Random random = new Random();
    public synchronized void add() throws InterruptedException {
        if(count >= 10) {
            System.out.println("缓冲区已满++++++无法生产");
            super.wait();
        }
        else{
            Thread.sleep(100);
            int rand = random.nextInt(4);
            switch (rand){
                case 0: Fruit[++count] = F.APPLE.toString();break;
                case 1: Fruit[++count] = F.CHEERY.toString();break;
                case 2: Fruit[++count] = F.MANGO.toString();break;
                case 3: Fruit[++count] = F.WATERMELON.toString();break;
            }
            System.out.println("已生成第" + count + " 个水果" + Fruit[count]);
            super.notifyAll();
        }
    }

    public synchronized String pop() throws InterruptedException {
        if(count <= 0){
            super.wait();
            return "没有食物 : " + count;
        }else{
            Thread.sleep(100);
            super.notifyAll();
            int temp = count;
            String fruit = Fruit[count--];
            return "得到食物 : " + fruit + "他是第 " + temp + "个";
        }
    }

    void test(){
        for (String s : Fruit) {
            System.out.println(s);
        }
    }
}

class Producer implements Runnable{
    private Food food;
    public Producer (Food food){
        this.food = food;
    }
    @Override
    public void run() {
        for (int i = 0; i < 100; i++) {
            try {
                food.add();
            } catch (InterruptedException e) {
                e.printStackTrace();
            }
        }

    }
}


class Customer implements Runnable{
    private Food food;
    public Customer(Food food){
        this.food = food;
    }
    @Override
    public void run() {
        for (int i = 0; i < 100; i++) {
            try {
                System.out.println(food.pop());
            } catch (InterruptedException e) {
                e.printStackTrace();
            }
        }
    }
}
```

## 模拟数字的加减，使Num保持在0，1中

```java
public class MathThread {
    public static void main(String[] args) {
        Resource re = new Resource();
        Add add = new Add(re);
        Sub sub = new Sub(re);
        new Thread(add, "加法线程--A").start();
        new Thread(add, "加法线程--B").start();

        new Thread(sub, "减法线程---A").start();
        new Thread(sub, "减法线程---B").start();
    }
}
class Resource {
    private volatile int num = 0;
    private boolean flag = true;
    public synchronized void add() throws Exception{
        while(this.flag == false){
            super.wait();
        }
        Thread.sleep(10);
        this.num++;
        System.out.println(Thread.currentThread().getName() + " " + this.num);

        this.flag = false;
        super.notify();
    }

    public synchronized void sub() throws Exception{
        while(this.flag == true){
            super.wait();
        }
        Thread.sleep(10);
        this.num--;
        System.out.println(Thread.currentThread().getName() + " " + this.num);

        this.flag = true;
        super.notify();
    }
}

class Add implements Runnable{
    private Resource resource;
    public Add(Resource resource){
        this.resource = resource;
    }
    @Override
    public void run() {
        for (int i = 0; i < 30; i++) {
            try {
                this.resource.add();
            } catch (Exception e) {
                e.printStackTrace();
            }
        }

    }
}


class Sub implements Runnable{
    private Resource resource;
    public Sub(Resource resource){
        this.resource = resource;
    }
    @Override
    public void run() {
        for (int i = 0; i < 30; i++) {
            try {
                this.resource.sub();
            } catch (Exception e) {
                e.printStackTrace();
            }
        }

    }
}
```

<font color = "red">被唤醒的线程，应该被继续检测，所以永远在while循环而不是if语句中使用wait！</font>

## volatile

+ 主要定义在属性上的，表示此属性为直接操作，不进行副本处理

普通变量操作过程：

1. 获取原有变量的副本
2. 对副本进行操作
3. 将操作后的结果，保存到原始空间中

加上volatile的变量，表示直接操作原始变量，节约了拷贝副本的时间。

# 并发深入理解

## `synchronized`

实现原理：保证方法或者代码块在运行时，同一时刻只有一个方法可以进入到临界区

使用方式：**（重点）**

1. 定义在普通方法上： 表示进入同步代码前，要获得当前实例的锁。
2. 静态同步方法： 表示是当前的class类。
3. 同步方法块： 表示锁的是 括号里的对象。

#### synchronized作用于普通方法

```JAVA
public class TestTread {

    public static void main(String[] args) {
        final syncTest test = new syncTest();

        /**
         * 在普通方法上加锁，锁的是同一个实例
         * 故操作同一个实例，有效。
         */
        new Thread(()->test.method1()).start();
        new Thread(()->test.method2()).start();


        /**
         * 不同的实例，失效。
         */
//        new Thread(()->new syncTest().method1()).start();
//        new Thread(()->new syncTest().method2()).start();
    }
}
class syncTest{
    public synchronized void method1() {
        System.out.println("Method 1 start");
        try {
            System.out.println("Method 1 execute");
            Thread.sleep(3000);
        } catch (InterruptedException e) {
            e.printStackTrace();
        }
        System.out.println("Method 1 end");
    }
    public synchronized void method2() {
        System.out.println("Method 2 start");
        try {
            System.out.println("Method 2 execute");
            Thread.sleep(1000);
        } catch (InterruptedException e) {
            e.printStackTrace();
        }
        System.out.println("Method 2 end");
    }
}
```

#### synchronized作用于静态方法

```java
public class StaticThread{
    //共享资源
    static int i = 0;

    /**
     * synchronized 修饰实例方法
     */
    public static void main(String[] args) throws InterruptedException {
        Thread t1 = new Thread(new St(),"s1");
        Thread t2 = new Thread(new St(),"s2");
        t1.start();
        t2.start();
        t1.join(); //加上join的使 主线程进入等待池，需要等待t1,t2执行完毕，方便后面的St.i 静态变量的输出
        t2.join();
        Thread.sleep(2000);
        System.out.println(St.i);
    }
}
class St implements Runnable {
    static int i = 0;
    public static synchronized void increase(){
        try {
            Thread.sleep(10);
        } catch (InterruptedException e) {
            e.printStackTrace();
        }
        i++;
    }

    @Override
    public void run() {
        for (int j = 0; j < 100; j++) {
            System.out.println(Thread.currentThread().getName() + " " +  i);
            increase();
        }
    }
}
```

#### synchronized作用于同步代码块

```java
public class BlockThread implements Runnable {
    static BlockThread instance=new BlockThread();
    static int i=0;
    @Override
    public void run() {
        //省略其他耗时操作....
        //使用同步代码块对变量i进行同步操作,锁对象为instance
        synchronized(instance){  // 可修改为 this 或者 当前类的 class
            for(int j=0;j<10000;j++){
                i++;
            }
        }
    }
    public static void main(String[] args) throws InterruptedException {
        Thread t1=new Thread(instance);
        Thread t2=new Thread(instance);
        t1.start();
        t2.start();
        t1.join();
        t2.join();
        System.out.println(i);
    }
}
```

## `join()方法`

大部分看起来好像是：t.join()方法会使所有其它线程都暂停并等待t的执行完毕。

经过上面 ：**synchronized作用于静态方法** 代码看到结果。

```java
t1.start();
t2.start();
t1.join(); 
t2.join();
```

上述顺序中，t1,t2交替运行。

而若改成:

```java
t1.start();
t1.join(); 
t2.start();
t2.join();
```

则是 t1先执行完毕，t2再执行完毕。

join()源代码：

```java
/**
     * Waits for this thread to die.
     *
     * <p> An invocation of this method behaves in exactly the same
     * way as the invocation
     *
     * <blockquote>
     * {@linkplain #join(long) join}{@code (0)}
     * </blockquote>
     *
     * @throws  InterruptedException
     *          if any thread has interrupted the current thread. The
     *          <i>interrupted status</i> of the current thread is
     *          cleared when this exception is thrown.
     */
public final void join() throws InterruptedException {
    join(0);            //join()相当于调用了join(0)
}

public final synchronized void join(long millis) throws InterruptedException {
    long base = System.currentTimeMillis();
    long now = 0;

    if (millis < 0) {
        throw new IllegalArgumentException("timeout value is negative");
    }

    if (millis == 0) {
        while (isAlive()) {
            wait(0);           //join(0)等同于wait(0)，即wait无限时间直到被notify
        }
    } else {
        while (isAlive()) {
            long delay = millis - now;
            if (delay <= 0) {
                break;
            }
            wait(delay);
            now = System.currentTimeMillis() - base;
        }
    }
}
```



借鉴其他博客得知：在哪个线程中调用 `x.join()` 则该线程挂起。

上述代码在 主线程中调用`t1.join()`,故主线程挂起，等待t1执行完毕，才继续执行下面代码，所以若是这样：

```java
t1.start();
t2.start();
t1.join(); 
t2.join();
```

则t1,t2交互进行。

参考博文：https://blog.csdn.net/zjy15203167987/article/details/82531772

## `notify()`和`notifyAll()`

锁池和等待池：

+ 锁池:假设线程A已经拥有了某个对象(注意:不是类)的锁，而其它的线程想要调用这个对象的某个synchronized方法(或者synchronized块)，由于这些线程在进入对象的synchronized方法之前必须先获得该对象的锁的拥有权，但是该对象的锁目前正被线程A拥有，所以这些线程就进入了该对象的锁池中。

+ 等待池:假设一个线程A调用了某个对象的wait()方法，线程A就会释放该对象的锁后，进入到了该对象的等待池中

**锁池：没有得到锁的线程，进入锁池，等待释放后，可以去竞争锁。**

**等待池：被调用`wait()`进入等待池，不能去竞争锁，必须先被唤醒，才能去竞争锁。**

notify唤醒一个等待的线程；notifyAll唤醒所有等待的线程。

参考博文：https://blog.csdn.net/djzhao/article/details/79410229

