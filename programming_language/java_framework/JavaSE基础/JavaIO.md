## `JavaIO`

#### 节点流和处理流分类

按节点流和处理流不同的功能分类：

1. 节点流：主要是涉及对数据的读写
2. 处理流：是对一个已存在的流的连接和封装，通过所封装的流的功能调用实现数据读写。如BufferedReader处理流的构造方法总是要带一个其他的流对象做参数。一个流对象经过其他流的多次包装，称为流的链接

![image-20200302175118654](C:\Users\Administrator\AppData\Roaming\Typora\typora-user-images\image-20200302175118654.png)

还有打印流:`PrintStream``PrintWriter`

对象序列化反序列化：`ObjectInputStream`、`ObjectOutputStream`

**字节和字符的不同:**

+ 字节是以一个字节为单位运输（单个字节）
+ 字符是以多个字节为单位运输（一个字符，不同编码对应不同的字节）

#### 根据字节流和字符流分类：

![image-20200302152313022](C:\Users\Administrator\AppData\Roaming\Typora\typora-user-images\image-20200302152313022.png)

#### 磁盘操作

递归输出一个目录下的所有文件：

```java
public static void listAllFile(File file) {
    if(file == null || !file.exists())return ;
    if(file.isDirectory()) {
        for(File f : file.listFiles()) {
            if(f.isFile())System.out.println(f.getName());
            else listAllFile(f);
        }
    }
}
```

单个文件的拷贝:

```java
public static boolean copy(String srcFile,String desFile) throws Exception {

    FileInputStream input = new FileInputStream(srcFile);
    FileOutputStream output = new FileOutputStream(desFile);

    byte[] buffer = new byte[20*1024];
    int cnt;
    while((cnt = input.read(buffer, 0, buffer.length))!= -1) {
        output.write(buffer,0,cnt);
    }
    input.close();
    output.close();
    return true;
}
```

整个目录的拷贝：

```java
File srcf = new File(srcFile); //准备拷贝的目录
File desf = new File(desFile);// 放到的地址

Main.copyDir(srcf, desf,srcf.getPath());  // head: 要替换掉的地址
```



```java
public static void copyDir(File srcf,File desf,String head) throws Exception {
    if(srcf.isDirectory()) {
        for(File s : srcf.listFiles()) {
            copyDir(s,desf,head);
        }
    }
    else {
        String src = srcf.getPath();
        System.out.println(src);
        String des = desf.getPath() + src.replace(head, "");
        System.out.println(des);
        copy(src,des);
    }
}
```

**编码和解码：**编码就是把字符转换为字节，而解码是把字节重新组合成字符。

#### 序列化`Serializable`

序列化就是将一个对象转换成字节序列，方便存储和传输。

- 序列化：`ObjectOutputStream.writeObject()`
- 反序列化：`ObjectInputStream.readObject()`

不会对静态变量进行序列化，因为序列化只是保存对象的状态，静态变量属于类的状态。



#### `NOI`

提供了高速的、面向块的 I/O。

`NIO` 和 `IO`的区别：

+ I/O以流的方式处理数据，而`NIO`以块的方式处理数据
+ I/O一次以一个字节为单位，`NIO`以一个块为单位，`NIO`速度更快。
+ `NIO` 是非阻塞的；