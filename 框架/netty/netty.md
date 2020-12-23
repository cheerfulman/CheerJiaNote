# NIO

基于网络编程有三种模式:

+ BIO : 传统同步并阻塞， 一个服务器对应一个线程。（如果客户端连接但不做事，就浪费了资源）

  ![image-20200428171640860](C:\Users\Administrator\AppData\Roaming\Typora\typora-user-images\image-20200428171640860.png)

+ NIO ：同步非阻塞

  ![image-20200428171714614](C:\Users\Administrator\AppData\Roaming\Typora\typora-user-images\image-20200428171714614.png)

+ AIO ： 异步非阻塞



> BIO简单demo

```java
public static void main(String[] args) throws IOException {
    ExecutorService ThreadPool = Executors.newCachedThreadPool();
    ServerSocket serverSocket = new ServerSocket(6666);
    System.out.println("服务器启动了.....");

    while(true){
        System.out.println("服务器正在等待连接......" + Thread.currentThread().getName());
        // 没有客户端连接时，会阻塞在这里
        Socket socket = serverSocket.accept();
        System.out.println("连接到一个 客户端");
        ThreadPool.execute(() -> {
            try {
                handler(socket);
            } catch (IOException e) {
                e.printStackTrace();
            }
        });

    }

}
static void handler(Socket socket) throws IOException {
    try{
        byte[] bytes = new byte[1024];
        // 通过socket 获取输入流
        final InputStream inputStream = socket.getInputStream();

        while(true){
            // 连接成功没有数据，会阻塞在这
            int read = inputStream.read(bytes);
            if(read != -1) {
                System.out.println(new String(bytes,0,read));
            }else break;
        }

    }catch(Exception e){
        e.printStackTrace();
    }finally{
        System.out.println("关闭连接");
        socket.close();
    }
}
```

NIO三大核心：

- Selector : 一个选择器维护多个管道（监听多个通道的事件），可以减少线程 	
- channel ： 
- Buffer :  实现非阻塞

![image-20200501100714571](C:\Users\Administrator\AppData\Roaming\Typora\typora-user-images\image-20200501100714571.png)

1. 每个channel都对应一个Buffer
2. 一个Selector对应一个线程，一个线程对应多个channel(连接)
3. 程序切换到哪个channel是由事件决定的。
4. Selector会根据不同的事件在通道上切换
5. Buffer是一个内存块，底层是一个索引维护的数组
6. 数据的读取写入是通过Buffer这个BIO,BIO中要么是输入流，要么是输出流，不能双向，但是NIO中的Buffer可以读和写，但是要通过**filp()方法**切换
7. channel是双向的，可以返回底层操作系统的情况。如Linux底层操作系统的通道就是双向的。

## ScatteringAndGatheringTest

```java
public static void main(String[] args) throws Exception {
    // 使用ServerSocketChannel 网络
    ServerSocketChannel serverSocketChannel = ServerSocketChannel.open();
    InetSocketAddress inetSocketAddress = new InetSocketAddress(6666);

    serverSocketChannel.socket().bind(inetSocketAddress);

    // 创建Buffer数组
    ByteBuffer[] byteBuffers = new ByteBuffer[2];
    byteBuffers[0] = ByteBuffer.allocate(3);
    byteBuffers[1] = ByteBuffer.allocate(5);

    SocketChannel socketChannel = serverSocketChannel.accept();

    int messageLength = 8;
    while(true){
        int byteRead = 0;
        while (byteRead < messageLength){
            long l = socketChannel.read(byteBuffers);
            byteRead += l;

            System.out.println("byteRead :" + byteRead);

            Arrays.asList(byteBuffers).stream().map(byteBuffer -> "position="
                                                    + byteBuffer.position() + ", limit :" +
                                                    byteBuffer.limit()).forEach(System.out::println);
        }

        Arrays.asList(byteBuffers).stream().forEach(byteBuffer -> byteBuffer.flip());
        //            System.out.println(new String(byteBuffers[0].array()) + new String(byteBuffers[1].array()));
        // 显示回客户端
        long byteWrite = 0;
        while(byteWrite < messageLength){
            long l = socketChannel.write(byteBuffers);
            byteWrite += l;
        }

        Arrays.asList(byteBuffers).stream().forEach(byteBuffer -> byteBuffer.clear());
        //            System.out.println("ByteRead :" + byteRead + " byteWrite :" + byteWrite );

        System.out.println(new String(byteBuffers[0].array()) + new String(byteBuffers[1].array()));

    }

}
```

- MappedByteBuffer : 直接在内存中修改
- ReadOnlyBuffer : 只能读的Buffer

## selector

- selector.select() : 阻塞
- selector.select(long timeout) : 阻塞 的毫秒，在规定时间后返回
- selector.wakeup() : 唤醒selector
- selector.selectNow() : 不阻塞，立马返回（直接判断channel中有没有读写，没有则直接返回）。

NIOServer:

```java
public static void main(String[] args) throws Exception{
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
}
```

NIOClient:

```java
public static void main(String[] args) throws IOException {
    // 得到一个 网络通道 Channel
    SocketChannel socketChannel = SocketChannel.open();
    // 设置成非阻塞的
    socketChannel.configureBlocking(false);
    // 提供服务器端的ip 和 端口
    InetSocketAddress inetSocketAddress = new InetSocketAddress("127.0.0.1", 6666);
    // 连接 失败，阻塞在while中，因为 这个函数本身不会阻塞，手写while 模拟阻塞
    if(!socketChannel.connect(inetSocketAddress)){
        while(!socketChannel.finishConnect()){
            System.out.println("因为连接需要时间，客户端不会阻塞，可以做其它工作");
        }
    }
    // 如果连接成功，发送数据
    String str = "hello ,CheerJia最帅";
    ByteBuffer buffer = ByteBuffer.wrap(str.getBytes());
    // 发送数据，将buffer 数据写入 channel
    socketChannel.write(buffer);
    System.in.read();
}
```

**SelectorKey**的几个属性

- OP_ACCEPT : 1<< 1;
- OP_READ : 1 << 2;
- OP_WRITE: 1 << 3
- OP_CONNET :  1 << 4;



## 群聊系统

Server:

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

Client:

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

## 零拷贝

## Netty

异步的、基于事件驱动的网络应用程序框架，用以快速开发高性能，高可靠的网络IO程序；

- 传统阻塞IO：

![image-20200504134214132](C:\Users\Administrator\AppData\Roaming\Typora\typora-user-images\image-20200504134214132.png)

1. 并发数大就会产生大量的线程，占用很大的系统资源
2. 连接创建后，如果当前线程暂时没有数据可以读，就会阻塞在read操作，造成线程资源浪费

无法处理大并发情况；

### Reactor模型

**解决方案**：多个连接共用一个阻塞对象，无需阻塞所有连接。

基于线程池复用的线程资源：不必为每个连接创建线程，将连接完成后的业务处理任务分配给线程进行处理，一个线程可以处理多个连接业务；

![image-20200504135905852](C:\Users\Administrator\AppData\Roaming\Typora\typora-user-images\image-20200504135905852.png)

- 通过一个或多个输入同时传递给服务器的模式（基于事件驱动）

- 服务器端程序处理传入的多个请求，并将它们同时分派到相应的处理线程，因此Reactor模式也叫Dispatcher模式

- Reactor模式使用IO复用监听事件，收到事件后，分发给某个线程（进程），这就是网络服务高并发处理的关键；

1、Reactor单线程模型

![image-20200504160241508](C:\Users\Administrator\AppData\Roaming\Typora\typora-user-images\image-20200504160241508.png)

优点: 模型简单，没有多线程、进程通信竞争的问题，全部在一个线程中完成。

缺点：单线程会产生阻塞。

2、Reactor多线程模型

![image-20200504161022587](C:\Users\Administrator\AppData\Roaming\Typora\typora-user-images\image-20200504161022587.png)

1. Reactor对象通过select监控客户端请求事件，收到事件后，通过dispatch分发；
2. 如果建立连接请求，则Acceptor通过accept处理连接请求，然后创建一个handler对象处理完成连接后的各种事件
3. 如果不是连接的请求，由reactor分发调用连接对应的hanler来处理
4. handler只负责响应事件，不做具体的业务处理，通过read读取数据后，会分发给后面的worker线程池的某个线程处理业务
5. worker线程池会分配独立的线程完成真正的业务，并将结果返回handler
6. handler收到响应后，通过send将结果返回给client

> 优缺点

1. 优点： 可以充分利用多核cpu的处理能力
2. 缺点: 多线程数据共享和访问比较复杂，reactor处理所有的事件的监听和响应是单线程运行的，如果在高并发场景肯定会有性能瓶颈

3、**主从Reactor多线程**

![image-20200504161924893](C:\Users\Administrator\AppData\Roaming\Typora\typora-user-images\image-20200504161924893.png)

在单Reactor多线程基础上相当于将Reactor分了个层

主reactor只处理连接，子reactor分发业务请求

1. reactor主线程MainReactor对象通过select监听连接事件，收到后通过acceptor处理连接事件
2. 当Acceptor处理连接事件后，MainReactor将连接事件分配给SubReactor
3. SubReactor将连接加入连接队列进行监听，并创建handler进行各种事件的处理
4. 当有新的事件发生时，subreactor会调用对应的handler处理
5. handler通过read读写数据，分发给worker线程池进行处理
6. worker线程池分配独立的worker线程进行业务处理，处理后会返回给handler
7. handler收到响应处理后，再通过send将结果返回给client
8. Reactor主线程可以关联多个SubReactor子线程

> 优点

1. 最大程度避免多线程及同步问题，并且避免了多线程的切换开销
2. 扩展性好，可以方便的增加Reactor实例个数来充分利用cpu的资源
3. 复用性好，Reactor模型本身与具体事件处理的逻辑无关，具有很高的复用性

### Netty模型

Netty主要基于Reactor多线程模型做了一定的改进

**简单版**：

![image-20200504163407005](C:\Users\Administrator\AppData\Roaming\Typora\typora-user-images\image-20200504163407005.png)



1. BossGroup线程维护Selector，只关注Accept
2. 当接受到Accept事件，获取到相应的SocketChannel，封装成NIOSocketChannel并注册到Worker线程（事件循环）并维护
3. 当Woker线程监听到selector中通道发生自己感兴趣的事件后，就进行处理，注意handler已经加入到通道了。

**进阶版**：

![image-20200504163748903](C:\Users\Administrator\AppData\Roaming\Typora\typora-user-images\image-20200504163748903.png)

1. Netty抽象除两组线程池 BossGroup专门进行客户端的连接，WorkerGroup专门负责网络的读写
2. BossGroup和WorkerGroup类型都是NioEventLoopGroup
3. NioEventLoopGroup相当于一个事件循环组，这个组包含多个事件循环，每个事件循环都是NioEventLoop
4. NioEventLoop表示一个不断循环执行处理的线程每个NioEventLoop都有一个selector，用于监听绑定其上的socket网络通讯
5. NioEventLoopGroup可以有多个线程，即可以含有多个NioEventLoop（可以指定）
6. 每个BossNioEventLoop执行的步骤有三部
   1. 轮询accept事件
   2. 处理accept事件，与Client建立连接，生产NioSocketChannel，并将其注册到某个WorkerNIOEventLoop上的selector
   3. 处理任务队列的任务，即runAllTasks
7. 每个WorkerNioEventLoop 循环执行的步骤
   1. 轮询read,write事件
   2. 处理i/o事件，即read,write事件，在对应NioSocketChannel处理
   3. 处理队列的其它任务，即runAllTasks
8. 每个Worker NioEventLoop处理业务时，会使用pipeline（管道)，pipeline包含了channel即通过pipeline可以获得对应的管道，管道中维护了很多的处理器。

---

Netty简单实现：

**sever****端**：

```java
public class NettyServer {
    public static void main(String[] args) throws Exception {
        // 创建BossGroup 和 WorkGroup
        // BossGroup 处理连接，WorkGroup 处理客户端业务处理
        // 两个都是 无线循环
        // BossGroup和WorkGroup 含有的子线程数（NioEvenLoop）的个数 默认是 实际cpu 核数 * 2
        NioEventLoopGroup bossGroup = new NioEventLoopGroup();
        NioEventLoopGroup workGroup = new NioEventLoopGroup();

        try {
            // 创建服务器端启动对象
            ServerBootstrap bootstrap = new ServerBootstrap();
            // 使用链式编程进行设置
            bootstrap.group(bossGroup,workGroup)// 设置两个线程组
                .channel(NioServerSocketChannel.class) // 使用NioSocketChannel作为服务器通道实现
                .option(ChannelOption.SO_BACKLOG,128) // 设置线程队列得到的连接个数
                .childOption(ChannelOption.SO_KEEPALIVE,true) // 设置保持活动连接状态
                .childHandler(new ChannelInitializer<SocketChannel>() { // 创建一个通道初始化对象（匿名对象）
                    // 给 pipeline设置处理器
                    @Override
                    protected void initChannel(SocketChannel ch) throws Exception {
                        ch.pipeline().addLast(new NettyServerHandler());
                    }
                }); // 给我们WorkGroup 的 EventLoop对应的管道设置处理器。
            System.out.println("服务器已经准备好了");

            // 绑定一个端口，并且同步，生产一个ChannelFuture 对象
            ChannelFuture cf = bootstrap.bind(6668).sync();

            // 对关闭对象进行监听
            cf.channel().closeFuture().sync();
        } finally {
            bossGroup.shutdownGracefully();
            workGroup.shutdownGracefully();
        }
    }
}
```

**handler**:

```java
// 自定义的Handler 需要继承 netty规定的类
public class NettyServerHandler extends ChannelInboundHandlerAdapter {
    // 可以读取客户端发送的消息
    // ChannelHandlerContext ctx ： 上下文对象，含有pipeline,通道channel,地址
    // Object msg： 就是客户端发送的数据，默认Object
    @Override
    public void channelRead(ChannelHandlerContext ctx, Object msg) throws Exception {
        System.out.println("服务器读的线程是 ： " + Thread.currentThread().getName());
        System.out.println("server ctx =" + ctx);

        ByteBuf buf = (ByteBuf)msg;
        System.out.println("客户端发送的消息是： " + buf.toString(CharsetUtil.UTF_8));
        System.out.println("客户端地址 = " + ctx.channel().remoteAddress());

        Channel channel = ctx.channel();
        ChannelPipeline pipeline = ctx.pipeline(); // 本质是一个双向链表

    }

    // 数据读取完毕
    @Override
    public void channelReadComplete(ChannelHandlerContext ctx) throws Exception {
        // 数据写入缓存并刷新
        // 一般讲我们对发送的数据进行编码
        ctx.writeAndFlush(Unpooled.copiedBuffer("hello,客户端~",CharsetUtil.UTF_8));

        // 处理异常

    }
    // 处理异常一般要关闭通道
    @Override
    public void exceptionCaught(ChannelHandlerContext ctx, Throwable cause) throws Exception {
        ctx.close();
    }
}
```

**Client****端**：

```java
public class NettyClient {
    public static void main(String[] args) throws InterruptedException {
        // 客户端只需要一个循环事件组
        NioEventLoopGroup group = new NioEventLoopGroup();

        try {
            // 创建客户端启动对象
            // 客户端不说ServerBootStrap,而是BootStrap
            Bootstrap bootstrap = new Bootstrap();

            bootstrap.group(group) // 设置线程组
            .channel(NioSocketChannel.class) // 设置客户端通道实现类（反射）
            .handler(new ChannelInitializer<SocketChannel>() {

                @Override
                protected void initChannel(SocketChannel ch) throws Exception {
                    ch.pipeline().addLast(new NettyClientHandler());
                }
            });
            System.out.println("客户端 ok ...");
            //启动 客户端
            // 关于channelFuture 要分析，设计到netty异步模型
            ChannelFuture channelFuture = bootstrap.connect("127.0.0.1", 6668).sync();
            // 关闭通道 进行监听
            channelFuture.channel().closeFuture().sync();
        } finally {
            group.shutdownGracefully();
        }
    }
}
```

**handler**:

```java
public class NettyClientHandler extends ChannelInboundHandlerAdapter {
    // 当通道就绪，就会触发该方法
    @Override
    public void channelActive(ChannelHandlerContext ctx) throws Exception {
        System.out.println("client " + ctx);
        ctx.writeAndFlush(Unpooled.copiedBuffer("hello, server: (>^w^<)喵", CharsetUtil.UTF_8));
    }
    // 当通道有读取事件时，会触发
    @Override
    public void channelRead(ChannelHandlerContext ctx, Object msg) throws Exception {

        ByteBuf buf = (ByteBuf)msg;
        System.out.println("服务器回复的消息:" + buf.toString(CharsetUtil.UTF_8));
        System.out.println("服务器的地址： " + ctx.channel().remoteAddress());


    }

    @Override
    public void exceptionCaught(ChannelHandlerContext ctx, Throwable cause) throws Exception {
        cause.printStackTrace();
        ctx.close();
    }
}
```

### Task使用场景

- 用户程序自定义的普通任务

  ```java
  ctx.channel().eventLoop().execute(new Runnable() {
      @Override
      public void run() {
          try {
              Thread.sleep(10 * 1000);
              ctx.writeAndFlush(Unpooled.copiedBuffer("hello, 客户端~喵4",CharsetUtil.UTF_8));
          } catch (InterruptedException e) {
              System.out.println("发生异常 ~！~~~~！！！！" + e.getMessage());
  
          }
      }
  });
  ```

  

- 用户自定义的定时任务 --- 该任务是提交到scheduleTaskQueue

- 非当前Reactor线程调用Channel的各种方法

  例如在**推送系统**的业务线程里面，根据**用户标识**，找到对应的**Channel引用**，然后调用Write类方法向该用户推送消息，就会进入到这种场景。最终Write会提交到任务队列后被**异步消费**；

### 异步模型

- 调用者不能立即获得结果，而是通过Future-Listener机制，用户可以方便的主动获取或者通过通知机制获得IO操作结果。

- Netty的异步模型建立在future和callback之上。**核心思想**：假设一个方法fun非常耗时，等待fun返回显然不合适。那么可以调用fun的时候，立即返回一个Future，后续通过Future去监控方法fun的处理过程

ChannelFuture是一个接口：

在使用netty进行编程时，拦截操作和转换出入站数据只需提供callback或利用future即可。使链式操作简单、搞笑

Netty框架目标：让业务逻辑从网络基础应用编码中分离出来。

#### Future-Listener机制

1. 当Future 对象刚刚创建时，处于非完全状态，调用者，可以通过返回ChannelFuture来获取操作执行的状态，注册监听函数来执行完成后的操作。

- isDone方法判断操作是否完成
- isSuccess方法来判断已完成的当前操作是否成功
- getCause方法来获取已完成的当前操作失败的原因
- isCancelled方法来判断已完成的操作是否被取消
- addListener方法来注册监听器，当前操作已完成（isDone方法返回完成），将通知指定的监听器；如果Future对象已完成，则通知指定的监听器

### HttpDemo

**TestServer**:

```java
public class TestHttpServerHandler extends SimpleChannelInboundHandler<HttpObject> {
    // 读取客户端数据
    @Override
    protected void channelRead0(ChannelHandlerContext ctx, HttpObject msg) throws Exception {
        // 判断msg 是是不是httpRequest请求
        if(msg instanceof HttpRequest){
            System.out.println("pipeline hasCode = " + ctx.pipeline().hashCode()  +
                    "  TestHttpSeverHandler hash = " + this.hashCode());
            System.out.println("msg 类型=" + msg.getClass());
            System.out.println("客户端地址" + ctx.channel().remoteAddress());
            HttpRequest httpRequest = (HttpRequest)msg;
            URI uri = new URI(httpRequest.uri());
            if("/favicon.ico".equals(uri.getPath())){
                System.out.println("请求了 favicon.ico， 不作响应");
                return ;
            }

            // 回复信息给浏览器 [http协议]
            ByteBuf content = Unpooled.copiedBuffer("hello, 我是服务器", CharsetUtil.UTF_8);
            // 构造一个http 相应的 即httpResponse
            FullHttpResponse response = new DefaultFullHttpResponse(HttpVersion.HTTP_1_1, HttpResponseStatus.OK, content);
            response.headers().set(HttpHeaderNames.CONTENT_TYPE,"text/plain");

            response.headers().set(HttpHeaderNames.CONTENT_LENGTH,content.readableBytes());
            // 将构建好的response返回
            ctx.writeAndFlush(response);
        }
    }
}
```

**TestServerInitializer** :

```java
public class TestServerInitializer extends ChannelInitializer<SocketChannel> {
    // 向管道加入处理器
    // 得到管道
    @Override
    protected void initChannel(SocketChannel ch) throws Exception {
        ChannelPipeline pipeline = ch.pipeline();
        // 加入netty 提供httpServerCode codec => [coder - decoder]
        pipeline.addLast("MyHttpServerCode",new HttpServerCodec());

        // 增加自定义的处理器
        pipeline.addLast("MyTestHttpServerHandler",new TestHttpServerHandler());

    }
}
```

**TestHttpServerHandler** :

```java'
public class TestHttpServerHandler extends SimpleChannelInboundHandler<HttpObject> {
    // 读取客户端数据
    @Override
    protected void channelRead0(ChannelHandlerContext ctx, HttpObject msg) throws Exception {
        // 判断msg 是是不是httpRequest请求
        if(msg instanceof HttpRequest){
            System.out.println("pipeline hasCode = " + ctx.pipeline().hashCode()  +
                    "  TestHttpSeverHandler hash = " + this.hashCode());
            System.out.println("msg 类型=" + msg.getClass());
            System.out.println("客户端地址" + ctx.channel().remoteAddress());
            HttpRequest httpRequest = (HttpRequest)msg;
            URI uri = new URI(httpRequest.uri());
            if("/favicon.ico".equals(uri.getPath())){
                System.out.println("请求了 favicon.ico， 不作响应");
                return ;
            }

            // 回复信息给浏览器 [http协议]
            ByteBuf content = Unpooled.copiedBuffer("hello, 我是服务器", CharsetUtil.UTF_8);
            // 构造一个http 相应的 即httpResponse
            FullHttpResponse response = new DefaultFullHttpResponse(HttpVersion.HTTP_1_1, HttpResponseStatus.OK, content);
            response.headers().set(HttpHeaderNames.CONTENT_TYPE,"text/plain");

            response.headers().set(HttpHeaderNames.CONTENT_LENGTH,content.readableBytes());
            // 将构建好的response返回
            ctx.writeAndFlush(response);
        }
    }
}
```

### Unpooled类

- Netty专门用来操作缓冲区的工具类
- readerIndex,writerIndex,capcity

```java
public static void main(String[] args) {
    // 创建一个 ByteBuf
    // 创建对象，该对象包含一个数组，是一个byte[10]
    // 在netty 的buffer中 不需要进行flip()翻转
    // 底层维护了 一个readerIndex , writerIndex

    // 0 --- readIndex 已读区域
    // readIndex -- writerIndex --- 可读区域
    // writerIndex --- capacity --- 可写区域
    ByteBuf buffer = Unpooled.buffer(10);
    for (int i = 0; i < 10; i++) {
        buffer.writeByte(i);
    }
    System.out.println(buffer.capacity());
    //        for (int i = 0; i < buffer.capacity(); i++) {
    //            System.out.println(buffer.getByte(i)); // readIndex 不会变换，变换的是refCnt
    //        }
    for (int i = 0; i < buffer.capacity(); i++) {
        System.out.println(buffer.readByte()); // readIndex 不会变换，变换的是ref
    }
}
```

### ChannelHandlerContext

保存Channel相关的所有上下文信息，同时关联一个ChannelHandler对象

### DEMO

#### 群聊系统：

**Server**:

```java
public class GroupChatServer {
    private int port;
    public GroupChatServer(int port){
        this.port = port;
    }
    public void run() throws Exception {
        EventLoopGroup boosGroup = new NioEventLoopGroup();
        EventLoopGroup workGroup = new NioEventLoopGroup();
        try {
            ServerBootstrap serverBootstrap = new ServerBootstrap();
            serverBootstrap.group(boosGroup,workGroup)
                .channel(NioServerSocketChannel.class)
                .option(ChannelOption.SO_BACKLOG,128)
                .childOption(ChannelOption.SO_KEEPALIVE,true)
                .childHandler(new ChannelInitializer<SocketChannel>() {
                    @Override
                    protected void initChannel(SocketChannel ch) throws Exception {
                        ChannelPipeline pipeline = ch.pipeline();
                        // 解码器
                        pipeline.addLast("decoder",new StringDecoder());
                        pipeline.addLast("encoder",new StringEncoder());
                        // 加入自己处理的 handler
                        pipeline.addLast(new GroupChatServerHandler());
                    }
                });
            System.out.println("netty 服务器 启动");
            ChannelFuture channelFuture = serverBootstrap.bind(port).sync();
            // 监听关闭
            channelFuture.channel().closeFuture().sync();
        } finally {
            boosGroup.shutdownGracefully();
            workGroup.shutdownGracefully();
        }
    }

    public static void main(String[] args) throws Exception {
        new GroupChatServer(7000).run();
    }

}
```

**GroupChatServerHandler**:

```java
public class GroupChatServerHandler extends SimpleChannelInboundHandler<String> {
    // 定义一个Channel组， 管理所有的channel
    // GlobalEventExecutor.INSTANCE 全局事件执行器， 单例
    private static ChannelGroup channelGroup = new DefaultChannelGroup(GlobalEventExecutor.INSTANCE);
    SimpleDateFormat sdf = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
    // 表示 channel 处于活动 状态，提示 xx上线
    @Override
    public void channelActive(ChannelHandlerContext ctx) throws Exception {
        System.out.println(ctx.channel().remoteAddress() + " 上线啦");
    }
    // 断开连接， 将xx客户离开信息推送给当前在线客户
    @Override
    public void handlerRemoved(ChannelHandlerContext ctx) throws Exception {
        Channel channel = ctx.channel();
        channelGroup.writeAndFlush("[客户端]" + channel.remoteAddress() + " 离开了\n");
        System.out.println("当前 channelGroup size " + channelGroup.size());
    }

    // 表示 channel 处于离线 状态，提示 xx离线
    @Override
    public void channelInactive(ChannelHandlerContext ctx) throws Exception {
        System.out.println(ctx.channel().remoteAddress() + " 离线啦");
    }

    // handlerAdded当连接建立时，第一个被执行
    // 将当前Channel 加入到 channelGroup
    @Override
    public void handlerAdded(ChannelHandlerContext ctx) throws Exception {
        Channel channel = ctx.channel();

        // 将客户加入聊天的信息推送给其它 在线的客户
        // 将 channelGroup 中所有的channel 遍历并 发布

        channelGroup.add(channel);
        channelGroup.writeAndFlush("[客户端]" + channel.remoteAddress() + "加入聊天" + sdf + "\n");
    }


    @Override
    public void exceptionCaught(ChannelHandlerContext ctx, Throwable cause) throws Exception {
        // 关闭
        ctx.close();
    }
    @Override
    protected void channelRead0(ChannelHandlerContext ctx, String msg) throws Exception {
        // 获取当前channel
        Channel channel = ctx.channel();
        // 遍历 channelGroup 根据不同的情况，回送不同的消息
        channelGroup.forEach(ch -> {
            if(channel != ch){
                ch.writeAndFlush("[客户]" + channel.remoteAddress() + " 发送消息" +
                        msg + "\n");
            }else{
                ch.writeAndFlush("[自己]" + channel.remoteAddress() + " 发送消息" +
                        msg + "\n");
            }
        });
    }
}
```

**GroupChatClient**:

```java
public class GroupChatClient {
    private final String host;
    private final int port;
    public GroupChatClient(String host, int port){
        this.host = host;
        this.port = port;
    }

    public void run() throws Exception {
        EventLoopGroup group = new NioEventLoopGroup();
        try {
            Bootstrap bootstrap = new Bootstrap();
            bootstrap.group(group).channel(NioSocketChannel.class)
                    .handler(new ChannelInitializer<SocketChannel>() {
                        @Override
                        protected void initChannel(SocketChannel ch) throws Exception {
                            ChannelPipeline pipeline = ch.pipeline();
                            pipeline.addLast("decoder",new StringDecoder());
                            pipeline.addLast("encoder",new StringEncoder());
                            pipeline.addLast(new GroupChatClientHandler());
                        }
                    });
            ChannelFuture channelFuture = bootstrap.connect(host, port).sync();
            // 得到channel
            Channel channel = channelFuture.channel();
            System.out.println("--------------" + channel.localAddress() + "----------");
            // 客户端需要输入信息， 创建一个扫描器
            Scanner sc = new Scanner(System.in);
            while(sc.hasNextLine()){
                String msg = sc.nextLine();
                // 通过channel  发送到服务端
                channel.writeAndFlush(msg + "\r\n");
            }
        } finally {
            group.shutdownGracefully();
        }
    }

    public static void main(String[] args) throws Exception {
        new GroupChatClient("127.0.0.1",7000).run();
    }
}
```

**GroupChatClient**:

```java
public class GroupChatClientHandler extends SimpleChannelInboundHandler<String> {
    @Override
    protected void channelRead0(ChannelHandlerContext ctx, String msg) throws Exception {
        System.out.println(msg.trim());
    }
}
```

---

#### 心跳机制Demo:



```java
public class MyServer {
    public static void main(String[] args) throws Exception {

        NioEventLoopGroup bossGroup = new NioEventLoopGroup();
        NioEventLoopGroup workGroup = new NioEventLoopGroup();
        try{
            ServerBootstrap serverBootstrap = new ServerBootstrap();
            serverBootstrap.group(bossGroup,workGroup).channel(NioServerSocketChannel.class)
                    .handler(new LoggingHandler(LogLevel.INFO)) // 增加一个日志处理器
                    .childHandler(new ChannelInitializer<SocketChannel>() {
                        @Override
                        protected void initChannel(SocketChannel ch) throws Exception {
                            ChannelPipeline pipeline = ch.pipeline();
                            // 加入netty 提供的 IdleStateHandler
                            // readerIdleTime : 表示多久没有读，  就会发送一个心跳检测包坚持是否连接
                            // writeIdleTime : 表示多久没有写，  就会发送一个心跳检测包坚持是否连接
                            // allIdleTime : 表示多久没有读和写，  就会发送一个心跳检测包坚持是否连接
                            // 当IdleStateEvent 触发后， 回传递给管道的 下一个handler 的 userEventTiggered 的方法 中处理

                            pipeline.addLast(new IdleStateHandler(3,5,7, TimeUnit.SECONDS));
                            pipeline.addLast(new MyServerHandler());

                        }
                    });
            ChannelFuture channelFuture = serverBootstrap.bind(7000).sync();
            channelFuture.channel().closeFuture().sync();
        }finally{
            bossGroup.shutdownGracefully();
            workGroup.shutdownGracefully();
        }
    }
}
```

**MyServerHandler**:

```java
public class MyServerHandler extends ChannelInboundHandlerAdapter {
    @Override
    public void userEventTriggered(ChannelHandlerContext ctx, Object evt) throws Exception {
        if(evt instanceof IdleStateEvent){
            IdleStateEvent event = (IdleStateEvent) evt;
            String eventType = null;
            switch(event.state()){
                case READER_IDLE:eventType = "读空闲";break;
                case WRITER_IDLE: eventType = "写空闲";break;
                case ALL_IDLE: eventType = "读写空闲";break;
            }

            System.out.println(ctx.channel().remoteAddress() + " -- 超时事件发生 --" + eventType);
            System.out.println("服务器做相应的处理");
        }
    }
}
```

---

#### webSocket

**MyServer**:

```java
public class MyServer {
    public static void main(String[] args) throws Exception{
        NioEventLoopGroup bossGroup = new NioEventLoopGroup();
        NioEventLoopGroup workGroup = new NioEventLoopGroup();
        try{
            ServerBootstrap serverBootstrap = new ServerBootstrap();
            serverBootstrap.group(bossGroup,workGroup).channel(NioServerSocketChannel.class)
                    .handler(new LoggingHandler(LogLevel.INFO)) // 增加一个日志处理器
                    .childHandler(new ChannelInitializer<SocketChannel>() {
                        @Override
                        protected void initChannel(SocketChannel ch) throws Exception {
                            ChannelPipeline pipeline = ch.pipeline();
                            // 基于http协议， 使用http 的编码解码器
                            pipeline.addLast(new HttpServerCodec());
                            pipeline.addLast(new ChunkedWriteHandler());
                            // http 数据在传输过程中是分段的，HttpObjectAggregator， 可以将多个段聚合
                            // 这就是为什么，当浏览器发送大量数据时，就会发送多次http请求
                            pipeline.addLast(new HttpObjectAggregator(8192));

                            // 对于WebSocket。 它的数据是以 帧(frame) 的形式 传递的
                            // 可以看到WebSocketFrame 下面有六个子类
                            // 浏览器请求时 ws://localhost:7000/xxx 表示请求的url 与最后的xxx相对应
                            // WebSocketServerProtocolHandler 核心功能将 http协议升级为 ws 协议，保持长连接
                            pipeline.addLast(new WebSocketServerProtocolHandler("/hello"));
                            // 自定义handler 处理业务逻辑
                            pipeline.addLast(new MyServerHandler());

                        }
                    });
            ChannelFuture channelFuture = serverBootstrap.bind(7000).sync();
            channelFuture.channel().closeFuture().sync();
        }finally{
            bossGroup.shutdownGracefully();
            workGroup.shutdownGracefully();
        }
    }
}
```

**MyServerHandler**:

```java
public class MyServerHandler extends SimpleChannelInboundHandler<TextWebSocketFrame> {

    @Override
    protected void channelRead0(ChannelHandlerContext ctx, TextWebSocketFrame msg) throws Exception {
        System.out.println("服务器收到消息 " + msg.text());
        // 回复消息
        ctx.channel().writeAndFlush(new TextWebSocketFrame("服务器时间" + LocalDateTime.now() +
                " " + msg.text()));
    }
    // 当 客户端连接后，就会触发
    @Override
    public void handlerAdded(ChannelHandlerContext ctx) throws Exception {
        System.out.println("handlerAdded 被调用" + ctx.channel().id().asLongText());
        System.out.println("handlerAdded 被调用" + ctx.channel().id().asShortText());
    }

    @Override
    public void handlerRemoved(ChannelHandlerContext ctx) throws Exception {
        System.out.println("handlerRemoved 被调用" + ctx.channel().id().asLongText());
    }

    @Override
    public void exceptionCaught(ChannelHandlerContext ctx, Throwable cause) throws Exception {
        System.out.println("异常发生" + cause.getMessage());
        ctx.close();
    }
}
```

**htmlClient**:

```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
</head>
<body>
<script>
    var socket;
    // 判断当前浏览器是否支持 websocket
    if(window.WebSocket){
        socket = new WebSocket("ws://localhost:7000/hello");
        // 相当于 channelRead0, ev 收到服务器端回送的消息
        socket.onmessage = function (ev) {
            var rt = document.getElementById("responseText");
            rt.value = rt.value + "\n" + ev.data;
        }
        // 相当于连接开启
        socket.onopen = function (ev) {
            var rt = document.getElementById("responseText");
            rt.value = "连接开启了";
        }
        // 连接关闭
        socket.onclose = function (ev) {
            var rt = document.getElementById("responseText");
            rt.value = rt.value + "\n" + "连接关闭了...";
        }
    }else{
        alert("当前浏览器不支持webSocket")
    }
    // 发送消息给服务器
    function send(message) {
        if(!window.socket){ // 判断socket是否创建好了
            return ;
        }
        if(socket.readyState == WebSocket.OPEN){
            // 通过socket 发送消息
            socket.send(message);
        }else{
            alert("连接未开启");
        }
    }
</script>
    <form onsubmit="return false">
        <textarea name="message" style="height: 300px;width: 300px"></textarea>
        <input type="button" value="发送消息" onclick="send(this.form.message.value)">
        <textarea id = "responseText" style="height: 300px;width: 300px"></textarea>
        <input type="button" value="清空" onclick="document.getElementById('responseText').value = ''">
    </form>
</body>
</html>
```

