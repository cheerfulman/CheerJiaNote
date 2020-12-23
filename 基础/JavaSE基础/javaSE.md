### clone
---
```java
protected Object clone()	
Creates and returns a copy of this object.


protected Object clone() throws CloneNotSupportedException
```

所有类都继承Object，故所有类都有clone()方法。但并不是所有类都希望被克隆，其必须支持Cloneable接口。这个接口没有任何方法，它只是描述一种能力。

### Date
---
简单使用: 
```java 
long getTime()	
Returns the number of milliseconds since January 1, 1970, 00:00:00 GMT represented by this Date object.
```

返回毫秒级时间，从1970-1-1开始；就是返回的一个long的毫秒数，从1970到现在有多少毫米了;


```new Data()```查看当前时间，但是格式不规范;

此时可以借用SimpleDateFormat类
```java
Date date = new Date();
SimpleDateFormat sdf = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss:SSS");
String str = sdf.format(date);
System.out.println(str);
```

### 正则
---
1、字符匹配【数量:单个】

+  任意字符
+ '\\\\': 匹配"\\"；
+ '\n': 匹配换行
+ \t : 匹配制表符；

2、字符集匹配【数量:单个】
+ [abc] 可能是abc的任意一个
+ [^abc] 非可能是abc的任意一个
+ [a-zA-Z] : 表示一个任意字母所组成；
+ [0-9] 数字

3、简化的字符集

+ . 任意字符
+ \d 一个等价于[0-9]
+ \D 非一个等价于[0-9]
+ \s 匹配空格
+ \S 匹配非空格
+ \w 匹配字母，数字，下划线等价于[0-9a-z_A-Z]

4、边界匹配
+ ^ ： 匹配边界开始 
+ $ ： 匹配边界结束

5、默认情况下，只有添加了数量单位才可以匹配多位字符
+ 表达式？： 该正则可以出现0次或1次
+ 表达式+： 该正则可以出现1次或多次
+ 表达式*： 该正则可以出现0次或1次或多次

+ 表达式{n}： 该正则可以出现n次
+ 表达式{n,}： 该正则可以出现n次以上
+ 表达式{n,m}： 该正则可以出现n次到m次

6、逻辑表达式：可以连接多个正则
+ 表达式X|表达式Y
+ (表达式)： 为表达式设置一个整体描述，可以为整体描述设置数量单位;

## File
---
构造方法: `public File(String pathname)`
构造方法: `public File(File parent String child )`

创建新的方法： `public boolean	createNewFile() throws IOException`
判断文件是否存在 ： `public boolean exists()`
删除文件： `public boolean delete()`
获取父路径： `public File getParentFile()`
创建目录 ： `public boolean mkdirs()`

文件是否可读 ： `public boolean canRead()`
文件是否可写 ： `public boolean canWrite()`

获取文件长度 ： `public long length()`
最后一次修改的时间 ： `public long lastModified()`
是否是目录 ： `public boolean isDirected()`

是否是文件 ： `public boolean isFile()`

列出目录内容 ： `public File[] listFiles();`

修改一个目录下的所有文件：
```java
package File;

import java.io.File;
import java.io.IOException;
import java.text.SimpleDateFormat;

public class File1 {
    public static void main(String[] args) throws IOException {
        File f = new File("D:"+ File.separator + "mldn.txt");
        System.out.println(f.getName());
        System.out.println(f.length());
        System.out.println(new SimpleDateFormat("yyyy-MM-dd HH:mm:ss").format(f.lastModified()));
        System.out.println(f.canRead());
        System.out.println(f.canWrite());
        System.out.println(f.isDirectory());
        System.out.println(f.isFile());
        File f1 = new File("D:" + File.separator + "cn");
        listDir(f1);
//        File result[] = f1.listFiles();
//        for(int i = 0; i < result.length; i++){
//            System.out.println(result[i]);
//        }
    }
    private static void listDir(File f){
        if(f.isDirectory()){
            File files[] = f.listFiles();
            if(files != null) {
                for (int i = 0; i < files.length; i++) {
                    listDir(files[i]);
                }
            }
        }else if(f.isFile()){
            String fileName = f.getName();
            if(fileName.contains(".")){
                fileName = fileName.substring(0,fileName.lastIndexOf(".")) + ".txt";
            }else{
                fileName = fileName + ".txt";
            }
            File newFile = new File(f.getParentFile(),fileName);
            f.renameTo(newFile);
            System.out.println(fileName);
        }
    }
}

```

## OutputStream类-字节流
---
OutputStream的方法:
![在这里插入图片描述](https://img-blog.csdnimg.cn/2020011615593257.png?x-oss-process=image/watermark,type_ZmFuZ3poZW5naGVpdGk,shadow_10,text_aHR0cHM6Ly9ibG9nLmNzZG4ubmV0L2Zvb2xpc2hwaWNoYW8=,size_16,color_FFFFFF,t_70)

OutputStream结构：
![在这里插入图片描述](https://img-blog.csdnimg.cn/2020011616015043.png?x-oss-process=image/watermark,type_ZmFuZ3poZW5naGVpdGk,shadow_10,text_aHR0cHM6Ly9ibG9nLmNzZG4ubmV0L2Zvb2xpc2hwaWNoYW8=,size_16,color_FFFFFF,t_70)

FileOutputStream构造方法:
`public FileOutputStream(File file)throws FileNotFoundException`(覆盖)

FileOutputStream构造方法:
`public FileOutputStream(File file boolean append)throws FileNotFoundException`(追加)


## InputStream类-字节流
---
方法:
`public int read(byte[] b)throws IOException`

## Writer-字符流
---
输出操作方法：
输出字符数组：`public void write(char[] cbuf)throws IOException`
输出字符串：`public void write(String str)throws IOException`

## Reader-字符流
---
`public int read(char[] byte) throws IOException``



OutputStream不用close()也可以写入，而Writer不行，但是Writer可以利用flush()强制进行刷新；
close()里面有刷新的功能；
Writer利用了缓冲区，速度更快;
+ 字节流没有使用缓冲器，而字节流使用的缓冲区

## 对文件的拷贝
---
```java
package FileCopy;

import java.io.*;

class FileCo{
    public static boolean copy(String srcFile,String desFile) throws Exception {
        File srcf = new File(srcFile);
        File desf = new File(desFile);
        if(!srcf.exists())return false;
        if(!desf.getParentFile().exists())desf.getParentFile().mkdirs();
        InputStream inputStream = new FileInputStream(srcFile);
        OutputStream outputStream = new FileOutputStream(desFile);
        inputStream.transferTo(outputStream);
//        byte[] bytes = new byte[1024];
//        int len = 0;
//        while((len = inputStream.read(bytes))!= -1){
//            outputStream.write(bytes,0,len);
//        }
        inputStream.close();
        outputStream.close();
        return true;
    }
}
public class FileCopy {
    public static void main(String[] args) throws Exception {
        String srcFile = "I:"+ File.separator + "study.jpg";
        String desFile = "C:" + File.separator + "Users" + File.separator + "Administrator" + File.separator + "Desktop" + File.separator + "carton.jpg";
        long start = System.currentTimeMillis();
        System.out.println(FileCo.copy(srcFile,desFile));
        long end = System.currentTimeMillis();
        System.out.println(end - start);
    }
}

```
其中有拷贝函数`public long transferTo(OutputStream out)throws IOException`
在写拷贝文件夹时，遇到了一个错误，该错误是递归中省写了一个list，导致

<font color = 'red' size = "50">拒绝访问</font>

<font color = 'red' size = "4">此else如果省略，文件夹变成文件去复制，就会发生拒绝访问的错误，例如InputStream输入流里面的File要为文件</font>

```java
String head,File file2) throws Exception {
        //final String head = file.getPath();
        if(file.isDirectory()){
            File[] results = file.listFiles();
            if(results != null){
                for(int i = 0; i < results.length; i++)listDir(results[i],head,file2);
            }
        }else {
            String src = file.getPath();
            String des = file2.getPath() + file.getPath().replace(head,"");
            fileCopy(src,des);
        }
    }
```
```java
package FileCopy;

import java.io.*;

class FileCo{


    public static boolean dirCopy(String srcFile,String desFile) throws Exception {
        File srcf = new File(srcFile);
        File desf = new File(desFile);
        listDir(srcf,srcFile,desf);
        try {
            return true;
        }catch (Exception e){
            return false;
        }
    }
    public static void listDir(File file,String head,File file2) throws Exception {
        //final String head = file.getPath();
        if(file.isDirectory()){
            File[] results = file.listFiles();
            if(results != null){
                for(int i = 0; i < results.length; i++)listDir(results[i],head,file2);
            }
        }else {
            String src = file.getPath();
            String des = file2.getPath() + file.getPath().replace(head,"");
            fileCopy(src,des);
        }
    }
    public static boolean fileCopy(String srcFile,String desFile) throws Exception {
        File srcf = new File(srcFile);
        File desf = new File(desFile);
        if(!srcf.exists())return false;
        if(!desf.getParentFile().exists())desf.getParentFile().mkdirs();
        InputStream inputStream = new FileInputStream(srcf);
        OutputStream outputStream = new FileOutputStream(desf);
        inputStream.transferTo(outputStream);
        inputStream.close();
        outputStream.close();
        return true;



//        byte[] bytes = new byte[1024];
//        int len = 0;
//        while((len = inputStream.read(bytes))!= -1){
//            outputStream.write(bytes,0,len);
//        }

    }
}
public class FileCopy {
    public static void main(String[] args) throws Exception {
        String srcFile = "I:"+ File.separator + "study.jpg";
        String desFile = "C:" + File.separator + "Users" + File.separator + "Administrator" + File.separator + "Desktop" + File.separator + "carton.jpg";

        String srcFile1 = "I:" + File.separator + "快乐";
        String desFile1 = "I:" + File.separator + "快";
        long start = System.currentTimeMillis();
        //System.out.println(FileCo.fileCopy(srcFile,desFile));
//        File f = new File("I:" + File.separator + "QQ");
//        FileCo.listDir(f,f.getPath());

        System.out.println(FileCo.dirCopy(srcFile1,desFile1));
        long end = System.currentTimeMillis();
        System.out.println(end - start);
    }
}

```

## 内存操作流
`ByteArrayOutputStream`:构造方法-->`public ByteArrayInputStream(byte[] buf)`
`ByteArrayInputStream` :构造方法-->`public ByteArrayOutputStream()`
+ 获取数据 ：public byte[] toByteArry();
+ 使用字符串的形式获取: public String toString();


## 管道流和RandomAccessFile
字节管道流：`PipedOutputStream` 和 `PipedInputStream`。
字符管道流：`PipedWriter` 和 `PipedReader`。

`PipedOutputStream`、`PipedWriter` 是写入者/生产者/发送者；
`PipedInputStream`、`PipedReader` 是读取者/消费者/接收者。

**字节管道流**
这里我们只分析字节管道流，字符管道流原理跟字节管道流一样，只不过底层一个是 byte 数组存储 一个是 char 数组存储的。

java的管道输入与输出实际上使用的是一个循环缓冲数来实现的。输入流PipedInputStream从这个循环缓冲数组中读数据，输出流PipedOutputStream往这个循环缓冲数组中写入数据。当这个缓冲数组已满的时候，输出流PipedOutputStream所在的线程将阻塞；当这个缓冲数组为空的时候，输入流PipedInputStream所在的线程将阻塞。

**注意事项**
在使用管道流之前，需要注意以下要点：

+ 管道流仅用于多个线程之间传递信息，若用在同一个线程中可能会造成死锁；
+ 管道流的输入输出是成对的，一个输出流只能对应一个输入流，使用构造函数或者connect函数进行连接；
+ 一对管道流包含一个缓冲区，其默认值为1024个字节，若要改变缓冲区大小，可以使用带有参数的构造函数；
+ 管道的读写操作是互相阻塞的，当缓冲区为空时，读操作阻塞；当缓冲区满时，写操作阻塞；
+ 管道依附于线程，因此若线程结束，则虽然管道流对象还在，仍然会报错“read dead end”；
+ 管道流的读取方法与普通流不同，只有输出流正确close时，输出流才能读到-1值。

**RandomAccessFile**:
构造方法：RandomAccessFile raf = newRandomAccessFile(File file, String mode);
    其中参数 mode 的值可选 "r"：可读，"w" ：可写，"rw"：可读性；

成员方法:
​    seek(int index);可以将指针移动到某个位置开始读写;
​    setLength(long len);给写入文件预留空间：

## BufferedReader
---
读取一行数据： public String readLine() throw IOException

## Scanner
sc.useDelimiter("\n")设置换行

### 对象序列化
![在这里插入图片描述](https://img-blog.csdnimg.cn/20200118103756935.png?x-oss-process=image/watermark,type_ZmFuZ3poZW5naGVpdGk,shadow_10,text_aHR0cHM6Ly9ibG9nLmNzZG4ubmV0L2Zvb2xpc2hwaWNoYW8=,size_16,color_FFFFFF,t_70)


实现java.io.Serializable父接口，作为序列化接口，这个接口和（cloneable一样）没有任何方法，仅表示一种能力；

**序列化和反序列化**
序列化：`ObjectOutputStream oos = new ObjectOutputStream(new FileOutputStream(SAVE_FILE))`;
反序列化：`ObjectInputStream oos = new ObjectInputStream(new FileInputStream(SAVE_FILE))`;

如果加了tarnsient就不会被序列化进文档，会赋其null；

```java
package File;

import java.io.*;
import java.time.LocalDate;
import java.time.LocalDateTime;
import java.util.Scanner;

public class Sc {
    private static final File SAVE_FILE = new File("I:" + File.separator + "mldn.txt");
    public static void main(String[] args) throws Exception {
        saveObject(new Person("xiao",22));
        System.out.println(load());
    }
    public static void saveObject(Object obj) throws Exception {
        ObjectOutputStream oos = new ObjectOutputStream(new FileOutputStream(SAVE_FILE));//序列化用到的流
        oos.writeObject(obj);
        oos.close();
    }
    public static Object load() throws Exception {
        ObjectInputStream oos = new ObjectInputStream(new FileInputStream(SAVE_FILE));//反序列化用到的流
        Object obj = oos.readObject();
        oos.close();
        return obj;
    }
}
class Person implements Serializable{//实现该接口，代表该类可以被对象序列化
    private String name;
    private int age;

    public Person(String name, int age) {
        this.name = name;
        this.age = age;
    }

    @Override
    public String toString() {
        return "Person{" +
                "name='" + name + '\'' +
                ", age=" + age +
                '}';
    }
}
```


### Collection 接口

`public boolean add(E e)`: 追加一个数据

`public boolean add(E e)`: 追加一组数据

`public void clear()`:    清空集合

`public boolean contatins(Object o)`: 查询数据是否存在，需要equals()支持

`public boolean contatins(Object o)`: 查询数据是否存在，需要equals()支持

`public boolean remove(Object o)`: 删除数据，需要equals()支持

`public Object[] toArray()`: 将集合变为数组返回


`public Iterator<E>iterator()`: 将集合变为Iterator接口返回
### list 接口

`public E get(int index)`: 获取指定索引上的数字

`public E set(int index, E element)`: 修改指定索引数据

`public ListIterator<E>listIterator()`: 返回ListIterator接口


三个子类，ArrayList、 LinkedList 、 Vector

### ArrayList implements List<E>,RandomAccess,Cloneable,Serializable -- > 数组

![在这里插入图片描述](https://img-blog.csdnimg.cn/2020012216085241.png?x-oss-process=image/watermark,type_ZmFuZ3poZW5naGVpdGk,shadow_10,text_aHR0cHM6Ly9ibG9nLmNzZG4ubmV0L2Zvb2xpc2hwaWNoYW8=,size_16,color_FFFFFF,t_70)

1. 保存数据就是其存储数据
2. 可以存储重复数据

<font color = "red">当ArrayList集合里保存的长度不够时，会开辟一个新的数组，并将原始的数据拷贝过来；</font>

实例化ArrayList类对象时 ： 并没有传递初始的长度，则默认情况下会使用一个空数组。若此时进行数据添加，则会判断当前增长的容量（grow（）方法）与默认容量的大小（10），使用较大的一个进行新的数组开辟；

若容量不足时，会采用成倍的方式进行增长，`int newCapacity = oldCapacity + (oldCapacity >> 1);

当估计大小 大于 10 时应该使用有参构造；

### LinkedList implements List<E>,RandomAccess,Cloneable,Serializable -- > 链表存储形式

![在这里插入图片描述](https://img-blog.csdnimg.cn/20200122164026956.png?x-oss-process=image/watermark,type_ZmFuZ3poZW5naGVpdGk,shadow_10,text_aHR0cHM6Ly9ibG9nLmNzZG4ubmV0L2Zvb2xpc2hwaWNoYW8=,size_16,color_FFFFFF,t_70)

LinkdList可以存储null，并且在LinkedList中都是用Node保存的节点。

### Vector implements List<E>,RandomAccess,Cloneable,Serializable -- > 数组

Vector 初始化时 就创建为10的大小；
Vector 的操作方法都是线程 安全，所以效率没有ArrayList高
### Set extends Collection<E>
当使用of()时，发现集合中存在有重复的元素则会直接抛出异常。

一般用子类实列化，HashSet、TreeSet


HashSet : 保存是无序的
TreeSet : 可排序，自定义类必须继承comparable接口

输出：
ListIterator可以倒序输出，双向的；
    `public boolean hasPrevious():` ： 判断是否有前一个元素
    `public E previous():` ： 获取当前元素

Enumeration : 专门为Vector 服务 的。
    `public Enumeration<E> elements():` ： 获取Enumeration
    `public boolean hasMoreElements():`  判断是否有下一个元素
    `public E nextElement():` ： 获取当前元素
### Map extends Map<K,V>,Cloneable,Serializbale,AbstractMap<K,V>

`public V put(K key, V value)` ： 向集合中保存数据
`public V get(Object key)` ： 根据Key查询数据
`public Set<Map.Entry<K,V>> entrySet` ： 将Map转为Set集合
`public boolean contatinsKey(Object k)` ： 查询指定的key是否存在；
`public boolean contatinsValue(Object value)` ： 查询指定的Value是否存在；
`public Set<K>KeySet()` ： 将Map中的key转为Set集合；

HashMap : 无序，可以存null
    使用put方法 : 如果数据存在就返回 旧 Value，如果不存在就返回null； 
    Put 方法源代码: 会调用一个putVal()方法，并调用hash()进行hash编码；并有一个Node节点类进行保存；

```java
public V put(K key, V value) {
    return putVal(hash(key), key, value, false, true);
}
```

使用无参构造时 : `loadFactor`属性，默认内容为0.75f(`static final float DEFAULT_LOAD_FACTOR = 0.75f`)


容量扩充： 
`static final int DEFAULT_INITIAL_CAPACITY = 1 << 4; // aka 16`常量，默认大小为16

+ 当保存的常量超过阈值(`static final float DEFAULT_LOAD_FACTOR = 0.75f`) 也就是16*0.75 = 12时 就会进行容量扩充，而不是等满了（16）再扩充。  每次扩大1倍、16、32、64。

工作原理：
+ HashMap数据的保存是用Node类完成的，那么就证明此时的数据结构只能是链表或者是二叉树
+ 故此时里面有一个常量： `static final int TREEIFY_THRESHOLD = 8;` 代表当数据个数大于8时会将链表转为红黑树存储，小于8时使用链表；

当使用 LinkedHashMap时，所有数据存储是 有序的，并且可以保存 null键值。

<font color = "red ">Hashtable : 同步方法（线程安全），不能存null</font>

### Stack<E> extends Vector
+ 入栈 : push(E item);
+ 出栈 : pop();

### Properties exdents Hashtable<Object,Object>

只允许是string
`public String getPorperty(String key)` : 取得属性

`public void store(OutputStream out,String commments) throws IOException` : 输出属性内容

`public void load(InputStream inStream)throws IOException` :  通过输入流读取属性内容

所有的中文将会进行自动的转码处理


# 反射
类加载的过程：
当程序主动使用某个类时，如果该类还未被加载到内存中，则系统会通过如下三个步骤进行初始化：
![在这里插入图片描述](https://img-blog.csdnimg.cn/20200204115641694.png?x-oss-process=image/watermark,type_ZmFuZ3poZW5naGVpdGk,shadow_10,text_aHR0cHM6Ly9ibG9nLmNzZG4ubmV0L2Zvb2xpc2hwaWNoYW8=,size_16,color_FFFFFF,t_70)
![在这里插入图片描述](https://img-blog.csdnimg.cn/20200204115918863.png?x-oss-process=image/watermark,type_ZmFuZ3poZW5naGVpdGk,shadow_10,text_aHR0cHM6Ly9ibG9nLmNzZG4ubmV0L2Zvb2xpc2hwaWNoYW8=,size_16,color_FFFFFF,t_70)

反射与工厂设计模式：
```java
import static java.lang.Class.forName;

interface Imessage{
    void send();
}
interface IService{
    void service();
}
class Good implements IService{
    public void service(){
        System.out.println("商品服务");
    }
}
class StuMessage implements Imessage{
    @Override
    public void send() {
        System.out.println("学生要上课!!");
    }
}

class TeaMessage implements Imessage{
    @Override
    public void send() {
        System.out.println("老师要教书!!");
    }
}

class Factory {
    private Factory(){}
    public static <T>T getInstance(String className) throws Exception {
        T instance = null;
        instance = (T) Class.forName(className).getDeclaredConstructor().newInstance();
        return instance;
    }
}
public class Reflect {
    public static void main(String[] args) throws Exception {
        Imessage msg = Factory.getInstance("Reflect.TeaMessage");
        msg.send();
        IService ser = Factory.getInstance("Reflect.Good");
        ser.service();
    }
}
```

反射与单例设计模式:

饿汉式： 在类被加载时就会实例化一个对象
```java
class Single1{
    private Single1(){}
    private static Single1 instance = new Single1();

    public Single1 getInstance(){
        return instance;
    }
}
```
懒汉式：只有在你需要对象时，才会生产单例对象
```java
class Single{
    private Single(){}
    private static Single instance = null;
    public static Single getInstance(){
        if(instance == null)
            instance = new Single();
        return instance ;
    }
}
```

饿汉式 ： 
+ <font color = "red">饿汉式是线程安全的(只有在类加载时才会初始化，以后都不会)</font>
+ 没有加锁，执行效率高
+ 这种方式较常用，但容易产生垃圾对象
+ 类加载时就初始化，浪费内存
+ 基于 classloder 机制避免了多线程的同步问题
+ instance 在类装载时就实例化，虽然导致类装载的原因有很多种，在单例模式中大多数都是调用 getInstance 方法，但是也不能确定有其他的方式（或者其他的静态方法）导致类装载，这时候初始化 instance 显然没有达到 lazy loading 的效果。

懒汉式：
+ 非线程安全
+ 这种方式是最基本的实现方式，这种实现最大的问题就是不支持多线程。因为没有加锁 synchronized，所以严格意义上它并不算单例模式。这种方式 lazyloading 很明显，不要求线程安全，在多线程不能正常工作


修改后懒汉式：
```java
class Single{
    private Single(){}
    private static Single instance = null;
    public static synchronized Single getInstance(){
        if(instance == null)
            instance = new Single();
        return instance ;
    }
}
```
在 getInstance 方法 上加上同步即可。
不过此时，效率会很低下，比如 多线程操作系统，我想64个线程却只有1个可以访问。故只要在实例化上加 同步即可；
```java
class Single{
    private Single(){}
    private static Single instance = null;
    public static Single getInstance(){
        synchronized(Single.class){
            if(instance == null){
                instance = new Single();
            }
        }
        return instance ;
    }
}
```

获取构造方法：
实例化方法替代： `clazz.getDeclaredConstructor().newInstance()`

获取所有构造方法： `public Constructor<?>[] getDeclaredConstructors()throws SecurityException`

获取指定构造方法： `public Constructor<T> getDeclaredConstructor​(Class<?>...parameterTypes)throws NoSuchMethodException,SecurityException`

获取所有构造方法： `public Constructor<?>[] getConstructors()throws SecurityException`

获取指定构造方法： `public Constructor<T> getConstructor​(Class<?>... parameterTypes)throws NoSuchMethodExceptionSecurityException`

invoke()


### 类加载器
ClassLoader类:
`public final ClassLoader getClassLoader`

![在这里插入图片描述](https://img-blog.csdnimg.cn/20200123195716553.png?x-oss-process=image/watermark,type_ZmFuZ3poZW5naGVpdGk,shadow_10,text_aHR0cHM6Ly9ibG9nLmNzZG4ubmV0L2Zvb2xpc2hwaWNoYW8=,size_16,color_FFFFFF,t_70)


### 网络编程

简单Echo模型：

服务器端
```java
public class Service {
    public static void main(String[] args) throws Exception{
        ServerSocket server = new ServerSocket(999);
        System.out.println("客户端连起来吧======");
        Socket client = server.accept(); // 等待连接
        //客户端输入数据（InputStream） 以及向客户端输出数据（OutputStream）
        Scanner sc = new Scanner(client.getInputStream());
        PrintStream out = new PrintStream(client.getOutputStream());


        boolean flag = true;
        while(flag){
            if(sc.hasNext()){
                String str = sc.next().trim(); // 得到客户端的消息
                if (str.equalsIgnoreCase("byebye")){
                    out.println("bye~~~~~~~~");
                    flag = false;
                }else out.println("【Echo】" + str); // 向客户端输出数据
            }
        }
        sc.close();
        out.close();
        client.close();
        server.close();
    }
}
```
客户端
```java
public class Client {
    public static void main(String[] args) throws Exception {
        Socket client = new Socket("localhost",999);//连接服务器 主机号加端口号
        Scanner input = new Scanner(System.in); //取得键盘输入对象
        /**
         * 从外界传数据到本程序，是输入
         * 从本程序传数据到外界，是输出
         */
        Scanner sc = new Scanner(client.getInputStream()); //取得输入流(得到服务器的输出)
        PrintStream out = new PrintStream(client.getOutputStream());//（输出到服务器）
        input.useDelimiter("\n");
        sc.useDelimiter("\n");

        boolean flag = true;
        while(flag){
            System.out.println("请输入要发送的数据");
            if(input.hasNext()){
                String str = input.next().trim(); // 输入字符串str
                out.println(str); //将字符串 写到 服务器
                if(str.equalsIgnoreCase("byebye"))flag = false;

                if(sc.hasNext())System.out.println(sc.next()); // 从服务器得到 输出
            }
        }
        input.close();
        sc.close();
        out.close();
        client.close();
    }
}
```

允许多个客户端访问的服务器（多线程）：
```java
class EchoThread implements Runnable{
    private Socket client;
    public EchoThread(Socket client){
        this.client = client;
    }
    @Override
    public void run() {
        Scanner sc = null;
        PrintStream out = null;
        try {
            sc = new Scanner(client.getInputStream());
            out = new PrintStream(client.getOutputStream());
            boolean flag = true;
            while(flag){
                if(sc.hasNext()){
                    String str = sc.next().trim();
                    if(str.equalsIgnoreCase("byebye")){
                        out.println("bye~~~~~~");
                        flag = false;
                    }else out.println("Echo" + str);
                }
            }
        } catch (IOException e) {
            e.printStackTrace();
        }finally{
            if(sc != null)sc.close();
            if(out != null)out.close();
            if(client != null) {
                try {
                    client.close();
                } catch (IOException e) {
                    e.printStackTrace();
                }
            }
        }
    }
}
public class Service {
    public static void main(String[] args) throws Exception{
       ServerSocket server = new ServerSocket(999);
       System.out.println("等待客户端连接======");
       boolean flag = true;
       while(flag){
           Socket client = server.accept();
           new Thread(new EchoThread(client)).start();
       }
    }
}
```

## Annotation

三大内置注解:
`@Override `: 定义在java.lang.Override中，表示一个方法声明打算重写超类的另一个方法声明；

`@Deprecated `: 定义在java.lang.Deprecated中，表示不鼓励程序员使用这样的元素，通常是因为它很危险或者有更好的选择；

`@SuppressWarnings `: 定义在java.lang.SuppressWarnings中，用来抑制编译时的警告；

`@SuppressWarnings ("all")` : 压制全部警告

元注解meta-annotation：
`@Target` : 表示我们的注解可以用在哪些地方；

`@Retention` : 表示我们的注解在什么地方才有效。 默认 value = RUNTIME;
```java
public @interface Retention {
    RetentionPolicy value();
}

public enum RetentionPolicy {
    SOURCE, //源码
    CLASS,  // 类
    RUNTIME; //所有

    private RetentionPolicy() {
    }
}
```

`@Documented` : 表示是否将我们的注解生成在JAVAdoc中

`@inherited` : 子类可以继承父类

自定义注解：
```java
public class An {
    @MyAnnotion(age = 100,hoppy = {"吃饭","睡觉","打豆豆"},value = "asdf")
    public void test(){

    }
}

@Target(value = {ElementType.TYPE,ElementType.METHOD})
@Retention(RetentionPolicy.RUNTIME)
@interface MyAnnotion{
    String name() default "";
    int age() default 0;
    String[] hoppy() default {""};
    String value(); //不成文规矩，当只有value时，可以省略value = ,而其他不行
}
```



