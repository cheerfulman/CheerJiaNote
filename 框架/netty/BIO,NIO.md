

## BIO

BIO编程流程： Socket ---> read/write ---- Thread

1. 服务端启动ServerSocket
2. 客户端启动Socket进行通信，默认情况下服务端需要对每个客户端建立一个线程通讯
3. 客户端发出请求后先咨询服务器是否有线程响应，没有则等待或者拒绝
4. 如果有响应，客户端线程等待请求结束，再继续执行；

Demo:

```java
public class BIOServer {
    public static void main(String[] args) throws IOException {
        ThreadPoolExecutor threadPoolExecutor = new ThreadPoolExecutor(3, 5, 100, TimeUnit.SECONDS,
                                                                       new LinkedBlockingDeque<>(5), new ThreadPoolExecutor.CallerRunsPolicy());

        ServerSocket serverSocket = new ServerSocket(6666);

        while (true){
            System.out.println(Thread.currentThread().getName() + "\t服务端正在等待连接");
            Socket socket = serverSocket.accept();
            System.out.println("连接成功");

            threadPoolExecutor.execute(() -> {
                try {
                    System.out.println("服务端分配了 " + Thread.currentThread().getName() + "线程进行通讯");
                    handler(socket);
                    System.out.println("通讯完毕");
                } catch (IOException e) {
                    e.printStackTrace();
                }
            });
        }

    }

    static void handler(Socket socket) throws IOException {
        byte[] bytes = new byte[1024];
        InputStream inputStream = socket.getInputStream();
        try {
            while(true){
                System.out.println("等待客户端发送请求");
                int read = inputStream.read(bytes);
                if(read != -1){
                    System.out.println(new String(bytes,0,read));
                    System.out.println("发送成功");
                }else break;
            }
        } finally {
            System.out.println("客户端端关闭");
            socket.close();
        }


    }

```

**BIO问题**

1. 每个请求都需要创建独立的线程，与客户端进行数据Read,业务处理，数据Write
2. 当并发大的时候，需要创建大量的线程处理连接，资源占用较大
3. 连接建立后，如果当前线程没有数据可读会阻塞，造成资源浪费

其实在accept(),Read(),Write()上都会阻塞；

## NIO

三大核心组件：

+ Channel(通道)
+ Buffer(缓冲区)： 通过Buffer实现非阻塞
+ Selector(选择器)

拥有缓冲区，可以说是面向缓冲器或者面向块编程的；

下图只画了**选中部分**，其他地方相同；

![image-20200821101849696](https://cdn.jsdelivr.net/gh/cheerfulman/PigGo-img/img/20200821101849.png)

buffer操作：

```java
/**
 * @title: BasicBuffer
 * @Author CheerJia
 * @Date: 2020/8/21 10:24
 * @Version 1.0
 */
public class BasicBuffer {
    public static void main(String[] args) {
        // 创建一个大小为5 的 buffer， capacity为5， 故首先可以写5个 故Limit为5
        IntBuffer intBuffer = IntBuffer.allocate(5);
        System.out.println("初始化");
        System.out.println("position：" + intBuffer.position());
        System.out.println("limit：" + intBuffer.limit());
        System.out.println("capacity：" + intBuffer.capacity());


        // 存放数据
        System.out.println("存入数据");

        for (int i = 0; i < 2; i++) { // 读取了2个 Int intBuffer.flip(); limit就变成 2;
            intBuffer.put(i * 3);
            System.out.println("position：" + intBuffer.position());
            System.out.println("limit：" + intBuffer.limit());
            System.out.println("capacity：" + intBuffer.capacity());
        }



        // 进行转换，读写切换，将其中的标志进行切换
        intBuffer.flip();
        System.out.println("切换读取数据模式");
        while(intBuffer.hasRemaining()){
            System.out.println(intBuffer.get());
            System.out.println("position：" + intBuffer.position());
            System.out.println("limit：" + intBuffer.limit());
            System.out.println("capacity：" + intBuffer.capacity());
        }
    }
}
```

运行结果：

```text
初始化
position：0
limit：5
capacity：5
存入数据
position：1
limit：5
capacity：5
position：2
limit：5
capacity：5
切换读取数据模式
0
position：1
limit：2
capacity：5
3
position：2
limit：2
capacity：5
```

postion一直代表我们能够操作的角标，但切换到读取模式的时候，那么就会从0开始，并且limit限制我们能够读取的范围。

### NIO和BIO比较：

1. BIO以流的方式处理数据，NIO以块的方式处理，I/O效率NIO更高
2. BIO阻塞，NIO非阻塞
3. BIO以字节流和字符流进行操作，而NIO基于Channel和Buffer进行操作。Selector选择器监听多个通道的事件，（一个线程可以通过选择器监听多个通道）

![image-20200821103449009](https://cdn.jsdelivr.net/gh/cheerfulman/PigGo-img/img/20200821103522.png)

### channel和Buffer

1. 每个channel都对应一个Buffer
2. 一个Selector对应一个线程，一个线程对应多个channel(连接)
3. 程序切换到哪个channel是由事件决定的。
4. Selector会根据不同的事件在通道上切换
5. Buffer是一个内存块，底层是一个索引维护的数组
6. 数据的读取写入是通过Buffer这个BIO,BIO中要么是输入流，要么是输出流，不能双向，但是NIO中的Buffer可以读和写，但是要通过**filp()方法**切换
7. channel是双向的，可以返回底层操作系统的情况。如Linux底层操作系统的通道就是双向的。

#### 利用Channel进行文件操作

利用NIO模式将读写文件；

```java
/**
 * @title: NIOFileChannel
 * @Author CheerJia
 * @Date: 2020/8/21 13:11
 * @Version 1.0
 */
public class NIOFileChannel {
    public static void main(String[] args) throws Exception {
        
        String str = "Hello CheerJia";
        File file = new File("E:\\dcc.txt");
        // 得到文件输入流
        FileOutputStream fileOutputStream = new FileOutputStream(file);
        // 获取channel
        FileChannel writeChannel = fileOutputStream.getChannel();
        // 通过buffer 读和写
        ByteBuffer byteBuffer = ByteBuffer.allocate(1024);
        // 将字符串写入buffer
        byteBuffer.put(str.getBytes());
        // 切换读写模式
        byteBuffer.flip();
        // 从buffer中 读到 Channel中
        writeChannel.write(byteBuffer);
        fileOutputStream.close();

        
        // 从文件中读取数据
        FileInputStream fileInputStream = new FileInputStream(file);
        FileChannel readChannel = fileInputStream.getChannel();
        byteBuffer.flip();
        readChannel.read(byteBuffer);
        byteBuffer.flip();
        System.out.println(new String(byteBuffer.array()));
		fileInputStream.close();
    }
}
```

运行结果：

![image-20200821133445283](https://cdn.jsdelivr.net/gh/cheerfulman/PigGo-img/img/20200821133445.png)

#### 利用Transfer方法操作文件

代码：

```java
FileInputStream fileInputStream = new FileInputStream("d:\\NIOFileDem.txt");
FileChannel channel = fileInputStream.getChannel();

FileOutputStream fileOutputStream = new FileOutputStream("d:\\2.txt");
FileChannel channel1 = fileOutputStream.getChannel();

channel1.transferFrom(channel,0,channel.size());

fileInputStream.close();
fileOutputStream.close();
```

### ByteBuffer

ByteBuffer可以放很多类型，但是放什么类型，就必须按什么类型取出，否则就会有`java.nio.BufferUnderflowException`

代码演示：

```java
ByteBuffer byteBuffer = ByteBuffer.allocate(100);

// 将不同类型放入byteBuffer
byteBuffer.putInt(3);
byteBuffer.putLong(3);
byteBuffer.putShort((short) 3);
byteBuffer.putChar('L');

byteBuffer.flip();
// 按什么顺序放就要按什么顺序取
System.out.println(byteBuffer.getInt());
System.out.println(byteBuffer.getLong());
System.out.println(byteBuffer.getShort());
System.out.println(byteBuffer.getChar());
```

运行结果:

```text
3
3
3
L
```

如果没有报异常，他会按字节长度读取，如`getLong（）`往后读取8字节；

初次之外，我们还有只读的`buffer`  ---- > `ByteBuffer.allocate(100).asReadOnlyBuffer()` 只能`get`，不能`put`

### MappedBuffer

堆外内存  ----> 可以让文件直接在内存中修改，操纵系统不需要再拷贝一次；

代码：

```java
// rw读写模式
RandomAccessFile randomAccessFile = new RandomAccessFile("1.txt", "rw");
// 得到Channel
FileChannel channel = randomAccessFile.getChannel();
// 获取MappedByteBuffer， 读写模式，可以直接修改的起始位置，可以修改的大小
MappedByteBuffer map = channel.map(FileChannel.MapMode.READ_WRITE, 0, channel.size());
// 修改第二个位置 为H
map.put(1,(byte)'H');

randomAccessFile.close();
```

### ScatteringAndGatheringBuffer

**Scattering** : 将数据写入buffer时，可以采用buffer数组，依次写入（分散）

**Gathering** ：从buffer读取数据时，可以采用buffer数组，依次读入(聚合)

> 当一个Buffer数组不够时，可以采用数组模式，方便读写

代码：

```java
/**
 * @Author CheerJia
 * @Description 采用ScatteringAndGathering 读写Buffer
 * @Date 2020/8/23 18:34
 * @Version 1.0
 */
public class ScatteringAndGatheringTest {
    public static void main(String[] args) throws Exception {
        // 获得serverSocketChannel 绑定接口
        ServerSocketChannel serverSocketChannel = ServerSocketChannel.open();
        InetSocketAddress inetSocketAddress = new InetSocketAddress(7000);
        serverSocketChannel.bind(inetSocketAddress);
        // 创建buffer数组
        ByteBuffer[] buffers = new ByteBuffer[2];
        buffers[0] = ByteBuffer.allocate(3);
        buffers[1] = ByteBuffer.allocate(5);
        // 阻塞监听
        SocketChannel socketChannel = serverSocketChannel.accept();
        // buffers 共8个字节
        long len = 8;
        while(true){
            long readAll = 0;
            // 使其读满8个字节
            while(readAll < len){
                readAll += socketChannel.read(buffers);
                System.out.println("read\t" + readAll);
                // 采用流的形式 输出
//                Arrays.stream(buffers).map(buffer -> "position: " + buffer.position() + "\tlimit: " + buffer.limit())
//                        .forEach(System.out::println);
                // 采用循环的方式 输出
                for(ByteBuffer buffer : buffers){
                    System.out.println("position: " + buffer.position() + "\tlimit: " + buffer.limit());
                }
            }

            // 切换读写模式
            Arrays.stream(buffers).forEach(byteBuffer -> byteBuffer.flip());
            long writeAll = 0;
            while(writeAll < len){
                writeAll += socketChannel.write(buffers);
            }
            // 读取模式并且打印信息
            Arrays.stream(buffers).forEach(byteBuffer -> byteBuffer.flip());
            Arrays.stream(buffers).map(byteBuffer -> new String(byteBuffer.array())).forEach(System.out::println);
            // 将所有buffer进行clear()
            Arrays.stream(buffers).forEach(byteBuffer -> byteBuffer.clear());
            System.out.println("读取: " + readAll + "\t" + "写入: " + writeAll + " " + len);
        }
    }
}

```

输出：利用Cmd的telnet协议 模拟客户端

```text
telnet 127.0.0.1 7000
send okhello === 输入 okhello


输出：
read	7
position: 3	limit: 3
position: 4	limit: 5

send ok === 输入 ok
输出：
read	2
position: 2	limit: 3
position: 0	limit: 5
```

由次可得会自动按buffer[0]、buffer[1]读取；

### Selector选择器

+ Selector能够检测多个注册的通道上是否由事件发生

![image-20200823193551296](https://i.loli.net/2020/08/23/AQwCIdqm7U3RPVk.png)

> Selector（选择器）调用select() 返回一个SelectionKey集合一个SelectionKey对应一个Channel；

**Selector** 可以通过open()得到；

**Selector**中的方法：

- selector.select() : 阻塞
- selector.select(long timeout) : 阻塞 的毫秒，在规定时间后返回
- selector.wakeup() : 唤醒selector
- selector.selectNow() : 不阻塞，立马返回（直接判断channel中有没有读写，没有则直接返回）。

#### NIO 非阻塞网络编程原理分析

![image-20200823200056382](https://i.loli.net/2020/08/23/ZhqGHjwduXY1lI5.png)

####  使用NIO完成网络通信的三个核心

- 通道（Channel）：负责连接

  - ```
    java.nio.channels.Channel
    ```

    - SelectableChannel
      - SocketChannel
      - ServerSocketChannel：TCP
      - DatagramChannel：UDP
    - Pipe.SinkChannel
    - Pipe.SourceChannel

- 缓冲区（Buffer）：负责数据的存取

- 选择器（Selector）：SelectableChannel的多路复用器，用于监控SelectorableChannel的IO状况

代码Server端：

```java
// 创建serverSocketChannel --> ServerSocket
ServerSocketChannel serverSocketChannel = ServerSocketChannel.open();
// 创建 Selector对象
Selector selector = Selector.open();
// 绑定端口
serverSocketChannel.socket().bind(new InetSocketAddress(6666));
// 设置为非阻塞
serverSocketChannel.configureBlocking(false);
// 把 severSocketChannel注册到 Selector 事件为 op_accept
serverSocketChannel.register(selector, SelectionKey.OP_ACCEPT);

while(true){
    // 一秒后 如果没有事件就返回
    if(selector.select(1000) == 0){
        System.out.println("服务器等待了1s ---- 无连接");
        continue;
    }
    // 存在事件, 获取相关的selectKeys 集合
    // 通过selectionKeys 反向获取通道
    Set<SelectionKey> selectionKeys = selector.selectedKeys();

    // 使用迭代器 遍历
    Iterator<SelectionKey> keyIterator = selectionKeys.iterator();
    while(keyIterator.hasNext()){
        // 获得 selectionKey
        SelectionKey key = keyIterator.next();
        // 根据key，对通道做相应的处理
        if(key.isAcceptable()){ // 如果是op_accept 代表有新的的客户端连接
            SocketChannel socketChannel = serverSocketChannel.accept();
            socketChannel.configureBlocking(false); // 设置非阻塞

            System.out.println("连接成功 : " + socketChannel.hashCode());
            // 关联一个buffer
            socketChannel.register(selector,SelectionKey.OP_READ, ByteBuffer.allocate(1024));
        }
        if(key.isReadable()){ // OP_READ
            // 通过Key反向获取 channel
            SocketChannel channel = (SocketChannel)key.channel();
            // 获取该 channel关联的buffer
            ByteBuffer buffer = (ByteBuffer)key.attachment();
            channel.read(buffer);
            System.out.println("from 客户端 " + new String(buffer.array()));
        }
        // 手动删除 SelectionK防止重复操作
        keyIterator.remove();
    }
}
```

客户端：

```java
// 创建Channel 绑定端口
SocketChannel socketChannel = SocketChannel.open(new InetSocketAddress("127.0.0.1",6666));
socketChannel.configureBlocking(false);

ByteBuffer byteBuffer = ByteBuffer.allocate(1024);
Scanner scanner = new Scanner(System.in);
// 读入
while(scanner.hasNext()){
    String str = scanner.next();
    byteBuffer.put((new Date().toString() + "\n" +str).getBytes());

    byteBuffer.flip();
    socketChannel.write(byteBuffer);
    byteBuffer.clear();
}
scanner.close();
socketChannel.close();
```

## 基于NIO的群聊系统

服务端代码:

```java
public class GroupChatServer {
    private Selector selector;
    private ServerSocketChannel listenChannel;
    private static final int PORT = 6667;
    public GroupChatServer(){
        try{
            // 获得选择器
            selector = Selector.open();
            // 获得ServerSocketChannel
            listenChannel = ServerSocketChannel.open();
            // 绑定端口
            listenChannel.socket().bind(new InetSocketAddress(PORT));
            // 设置非阻塞模式
            listenChannel.configureBlocking(false);
            listenChannel.register(selector, SelectionKey.OP_ACCEPT);
        }catch (Exception e){
            e.printStackTrace();
        }
    }

    public void listen(){
        try{
            // 循环监听
            while(true){
                int count = selector.select();
                if(count > 0){ // 有事件处理
                    // 遍历 得到selectionKeys
                    Iterator<SelectionKey> selectionKeyIterator = selector.selectedKeys().iterator();
                    while (selectionKeyIterator.hasNext()){
                        SelectionKey key = selectionKeyIterator.next();
                        if(key.isAcceptable()){
                            SocketChannel accept = listenChannel.accept();
                            accept.configureBlocking(false);
                            // 将 此通道注册到 selector
                            accept.register(selector, SelectionKey.OP_READ);
                            // 提示
                            System.out.println(accept.getRemoteAddress() + " 上线");
                        }
                        if(key.isReadable()){ // 通道发生read事件
                            // 处理读
                            readData(key);

                        }
                        // 删除 ，防止重复操作
                        selectionKeyIterator.remove();
                    }
                }else{
                    System.out.println("服务器等待中，没有客户端连接");
                }

            }
        }catch (Exception e){
            e.printStackTrace();
        }finally{

        }
    }

    private void readData(SelectionKey key){
        // 定义一个 socketChannel
        SocketChannel channel = null;
        try{
            // 取到关联的Channel
            channel = (SocketChannel)key.channel();
            ByteBuffer buffer = ByteBuffer.allocate(1024);
            int count = channel.read(buffer);
            // 根据count 值做处理
            if(count > 0){
                // 把缓存区的数据转换为字符串
                String message = new String(buffer.array());
                System.out.println("from 客户端 " + message);

                // 向其它客户端转发消息 专门写个方法
                sendInfoToOtherClient(channel,message);

            }
            //            channel.register(selector,SelectionKey.OP_READ,)
        }catch(IOException e){
            try{
                System.out.println(channel.getRemoteAddress() + " 离线了");
                // 取消注册
                key.cancel();
                // 关闭通道
                channel.close();
            }catch (IOException e1){
                e1.printStackTrace();
            }

        }
    }

    private void sendInfoToOtherClient(SocketChannel self,String msg) throws IOException {
        System.out.println("服务器转发消息 ！");
        // 遍历 所有注册到selector上的 SocketChannel 排除自己
        for(SelectionKey key : selector.keys()){
            // 取出 key对应的 channel
            Channel targetChannel = key.channel();
            // 排除自己
            if(targetChannel instanceof SocketChannel && targetChannel != self){
                // 转型
                SocketChannel des = (SocketChannel) targetChannel;
                ByteBuffer buffer = ByteBuffer.wrap(msg.getBytes());
                des.write(buffer);
            }
        }
    }

    public static void main(String[] args) throws IOException {
        // 创建服务器对象
        GroupChatServer groupChatServer = new GroupChatServer();
        groupChatServer.listen();
    }
}

```

客户端代码：

```java
public class GroupChatClient {
    private final String HOST = "127.0.0.1";// 服务器ip
    private final int PORT = 6667;
    private Selector selector;
    private SocketChannel socketChannel;
    private String username;
    // 完成初始化工作
    public GroupChatClient() throws IOException {
        selector = Selector.open();
        // 连接服务器
        socketChannel = SocketChannel.open(new InetSocketAddress(HOST,PORT));
        // 设置成 非阻塞
        socketChannel.configureBlocking(false);
        socketChannel.register(selector, SelectionKey.OP_READ);
        // 得到username, localAddress
        username = socketChannel.getLocalAddress().toString().substring(1);
        System.out.println(username + " is OK");
    }

    // 向服务器发送消息
    public void sendInfo(String info){
        info = username + " 说: " + info;
        try {
            socketChannel.write(ByteBuffer.wrap(info.getBytes()));
        } catch (IOException e) {
            e.printStackTrace();
        }
    }
    // 从服务器端回复的消息
    public void readInfo(){
        try{
            int readChannels = selector.select();
            if(readChannels > 0){ // 有可用的通道
                Iterator<SelectionKey> iterator = selector.selectedKeys().iterator();
                while(iterator.hasNext()){
                    SelectionKey key = iterator.next();
                    if(key.isReadable()){ // 如果可读的 就得到相关的通道
                        SocketChannel sc = (SocketChannel) key.channel();
                        ByteBuffer buffer = ByteBuffer.allocate(1024);
                        sc.read(buffer);
                        // 把缓冲区的数据(buffer)， 转换成字符串
                        String msg = new String(buffer.array());
                        System.out.println(msg.trim());
                    }
                    iterator.remove();
                }
            }else{
                //                System.out.println("没有可以读的数据!");
            }
        }catch (Exception e){
            e.printStackTrace();
        }
    }

    public static void main(String[] args) throws IOException {
        GroupChatClient chatClient = new GroupChatClient();

        // 启动一个线程, 每隔三秒，读取从服务端发送的数据
        new Thread(){
            public void run(){
                while (true){
                    chatClient.readInfo();
                    try {
                        Thread.currentThread().sleep(3000);
                    } catch (Exception e) {
                        e.printStackTrace();
                    }
                }
            }
        }.start();

        Scanner sc = new Scanner(System.in);
        while(sc.hasNextLine()){
            String s = sc.nextLine();
            chatClient.sendInfo(s);
        }
    }
}
```

运行结果：

![image-20200824095222343](https://i.loli.net/2020/08/24/abjXLI2wWHVOfnA.png)