# goroutine

在Go中，独立的任务交goroutine

利用Go关键字，就可以开启goroutine

```go
func main() {
   go sleepyGopher()
   time.Sleep(time.Second * 2)
   fmt.Println("直接输出")
}

func sleepyGopher()  {
   time.Sleep(time.Second)
   fmt.Println("我睡了一秒")
}
输出：
我睡了一秒
直接输出
```

## Goroutine的参数

向goroutine传参就像跟函数传递参数一样，参数是按值传递的

```go
func main() {
   for i := 0; i < 5; i ++ {
      go sleepyGopher(i)
   }
   time.Sleep(time.Second * 2)
   fmt.Println("直接输出")
}

func sleepyGopher(id int)  {
   time.Sleep(time.Second)
   fmt.Println("我睡了一秒", id)
}
输出：
我睡了一秒 4
我睡了一秒 1
我睡了一秒 2
我睡了一秒 3
我睡了一秒 0
直接输出
```

> 我在main函数中sleep了2秒，而实际上我们不知道goroutine什么时候结束，这时候就需要通道告诉我们，就可以不用等2秒

## 通道channel

使用make创建通道

+ 发送操作会等待直到另一个goroutine尝试对该通道进行接收操作为止
  + 执行发送操作的goroutine在等待期间将无法执行其它操作
  + 未在等待通道操作的goroutine可以自由的允运行
+ 执行接收的goroutine将等待直到另一个goroutine向通道发送操作为止

```go
func main() {
   c := make(chan int)
   for i := 0; i < 5; i ++ {
      go sleepyGopher(i, c)
   }
    // 不需要等待了，收到值就直接输出
   for i := 0; i < 5; i ++ {
      gopherID := <- c
      fmt.Println("直接输出id: ", gopherID)
   }
}

func sleepyGopher(id int, c chan int)  {
   time.Sleep(time.Second)
   fmt.Println("我睡了一秒", id)
   c <- id
}
输出：
我睡了一秒 0
我睡了一秒 3
我睡了一秒 1
我睡了一秒 4
我睡了一秒 2
直接输出id:  0
直接输出id:  3
直接输出id:  1
直接输出id:  4
直接输出id:  2
```

![image-20210810233803090](https://cdn.jsdelivr.net/gh/cheerfulman/picGo/img/202108110007237.png)

## 使用select处理多个通道

+ 等待不同的值
+ time.After函数，返回一个通道，该通道指定时间后会接收到一个值（发送该值的goroutine是Go运行时的一部分）

```go
func main() {
   c := make(chan int)
   for i := 0; i < 5; i ++ {
      go sleepyGopher(i, c)
   }

   timeout := time.After(2 * time.Second)
   for i := 0; i < 5; i++ {
      select {  //select语句
      case gopherID := <- c:  //等待地鼠醒来
         fmt.Println("gopher ", gopherID, " has finished sleeping")
      case <-timeout:  // 等待直到时间耗尽
         fmt.Println("my patience ran out")
         return  // 放弃等待然后返回
      }
   }

}

func sleepyGopher(id int, c chan int) {
   time.Sleep(time.Duration(rand.Intn(3000)) * time.Millisecond)
   c <- id
}
```

**提示**: 这个模式适用于任何想要控制事件完成时间的场景。通过将动作放入goroutine并在动作完成时向通道执行发送操作，我们可以为Go中的任何动作都设置超时。

> 即使程序已经停止等待goroutine，但只要`main`函数还没返回，仍在运行的goroutine就会继续占用内存。所以在情况允许的情况下，我们还是应该尽量结束无用的goroutine。

## nil通道

> 因为创建通道需要显式地使用`make`函数，所以你可能会好奇，如果我们不使用`make`函数初始化通道变量的值，那么会发生什么？答案是，跟映射、切片和指针一样，通道的值也可以是nil，而这个值实际上也是它们默认的零值。

>  对值为nil的通道执行发送或接收操作并不会引发惊恐，但是会导致操作永久阻塞，就好像遇到了一个从来没有接收或者发送过任何值的通道一样。但如果你尝试对值为nil的通道执行close`函数，那么该函数将引发惊恐。

> 初看上去，值为nil的通道似乎没什么用处，但事实恰恰相反。例如，对于一个包含select语句的循环，如果我们不希望程序在每次循环的时候都等待select语句涉及的所有通道，那么可以先将某些通道设置为nil，等到待发送的值准备就绪之后，再为通道变量赋予一个非 nil 值并执行实际的发送操作。

## 阻塞和死锁

当goroutine在等待通道的发送或者接收操作的时候，我们就说它被阻塞了。听上去，这似乎跟我们写一个不做任何事情只会空转的无限循环一样，并且它们从表面上看也非常相似。但实际上，如果你在笔记本电脑的程序中运行类似的无限循环，那么过不了多久，你就会发现笔记本电脑由于忙着执行这个循环而变得越来越热，并且风扇也开始转得越来越快了。与此相反，除goroutine本身占用的少量内存之外，被阻塞的goroutine并不消耗任何资源。goroutine会静静地停在那里，等待导致它阻塞的事情发生，然后解除阻塞。

当一个或多个goroutine因为某些永远无法发生的事情而被阻塞时，我们称这种情况为死锁，而出现死锁的程序通常都会崩溃或者被挂起。引发死锁的代码甚至可以非常简单，就像这样：

```go
func main() {
    c := make(chan int)
    <-c 
}
```

```go
package main

import (
   "fmt"
   "strings"
)

func sourceGopher(downstream chan string) {
   for _, v := range []string{"hello world", "a bad apple", "goodbye all"} {
      downstream <- v
   }
   downstream <- ""
}

func filterGopher(upstream, downstream chan string) {
   for {
      item := <-upstream
      if item == "" {
         downstream <- ""
         return
      }
      if !strings.Contains(item, "bad") {
         downstream <- item
      }
   }
}
func printGopher(upstream chan string) {
   for {
      v := <-upstream
      if v == "" {
         return
      }
      fmt.Println(v)
   }
}


func main() {
   c1 := make(chan string)
   c2 := make(chan string)
   go sourceGopher(c1)
   go filterGopher(c1, c2)
   printGopher(c2)
}
输出：
hello world
goodbye all
```

Go允许我们在没有值可供发送的情况下通过`close`函数关闭通道`close(c)`
因为“从通道里面读取值，直到它被关闭为止”这种模式实在是太常用了，所以Go为此提供了一种快捷方式。通过在`range`语句里面使用通道，程序可以在通道被关闭之前，一直从通道里面读取值。

```go
func filterGopher(upstream, downstream chan string) {
   for {
      item, ok := <-upstream
      // 用Ok判断upstream是否关闭
      if !ok {
         close(downstream)
         return
      }
      if !strings.Contains(item, "bad") {
         downstream <- item
      }
   }
   // 使用 range自动判断upstream是否关闭
   for item := range upstream {
      if !strings.Contains(item, "bad") {
         downstream <- item
      }
   }
   close(downstream)

}
```

## 并发状态

+ 共享值
+ 竞争条件

互斥锁(mutex)

+ mutex = mutual exclusive
+ Lock(), Unlock()

+ sync包

