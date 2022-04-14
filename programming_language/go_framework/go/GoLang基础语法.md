## GoLang语法学习

### 大数存储

对于很大的数，我们可以用big包;

创建big对象 `new(big.Int)`

```go
distance := new(big.Int)
// 设置为10进制
distance.SetString("2300000000000000000000000000000000000",10)
fmt.Println(distance)

输出：
2300000000000000000000000000000000000

// 另一种创建方式
speed := big.NewInt(2433)
```

`NewInt()`只是把int64转换成`big.Int`类型

+ Go对于字面值和常量的计算是在编译期完成的
  + 故我们可以const dis = （很大的数）,但是不可以输出dis；
  + 如果我们const dis = (大数) / (大数) 使其在10^18以内则可以正常使用。

### 多语言文本

声明字符串的方式：

```go
str := "string"
var str = "str"
var str string = "str"
```

字符串的零值：

```go
var blank string
// 相当于
var blank string = ""
```

#### 字面值和原始字面值

```go
// 字符串的字面值
str := "you \n me"
// 原始字符串的字面值
Nstr := `you \n me`
fmt.Println(str)
fmt.Println(Nstr)

输出：
you 
 me
you \n me
```

Unicode联盟为超过100w个字符分配了相应的数值，这个数叫code point

+ 65代表A,128515代表😊

**为了表示unicode code point，Go提供了rune这个类型，它是int32的一个类型别名。**

**而byte则是uint8的别名，可以表示ASCII码；它为Unicode一个子集（共128个字符）**

```go
// 可以当整数打印，也可以打印出字符
char1 := 1
char2 := 45
char3 := 980000000000

fmt.Printf("%v %v %v\n",char1,char2,char3)
fmt.Printf("%c %c %c\n",char1,char2,char3)
fmt.Printf("%T %T %T\n",char1,char2,char3)
// %d 打印出code point
c1 := '*'
c2 := 'e'
fmt.Printf("%d %d\n",c1,c2)
```

#### 字符串不可变

跟Java一样，可以修改变量的值，但是字符串的值是不可变的

```go
msg := "abcde"
c := msg[3]
fmt.Printf("%c\n", c)

// 提示error: Cannot assign to msg[3]
//msg[3] = 'k'

输出：
d
```

#### len()函数

go中Len()函数得到的是字节长度，如果字符串全是英文或1字节以内的则可以用`len`得到字符串的长度，否则不可取；

```go
str1 := "123456"
fmt.Println(len(str1))
// 一个中文占3个字节
str2 := "哈45678"
fmt.Println(len(str2))

输出：
6
8
```

其他俄语法语等，可能一个字符占**2个字节**；

故我们一般可以将字符串转为rune

```go
// 使用rune 返回字符串长度
fmt.Println(len([]rune(str2)))
// 使用strings.Count函数返回字符串长度 + 1
fmt.Println(strings.Count(str2, "") - 1)

fmt.Println(utf8.RuneCountInString(str2), "runes")
// 返回第一个字符和第一个字符的大小
cc, size := utf8.DecodeRuneInString(str2)
fmt.Printf("%c %d\n",cc, size)

输出:
6
6
6 runes
哈 3
```

也可以用range遍历，range自动解决了上述问题：

```go
for _, c := range str2 {
   fmt.Printf("%c ", c)
}
输出：哈 4 5 6 7 8
```

### 类型转换

在Go中整数和浮点数是不能自动转换的：

```go
age := 10
ageFloat := 10.0
// 会报错：Invalid operation: age * ageFloat (mismatched types int and float64)
res := age * ageFloat

// 转换类型之后则没有问题
ages := float64(age)
res := ages * ageFloat
```

整形转字符串中，如果超过其unicode code point最大，则固定转为一个字符

```go
ee := 12333333333333
e1 := 123333333333333333
fmt.Println(string(ee))
fmt.Println(string(e1))

输出：
�
�
```

故我们一般使用strconv包的Itoa函数；

+ Itoa就是Integer to ASCII的意思

还有就是使用Sprintf使其转化为String，Sprintf会返回一个string

```go
str := fmt.Sprintf("asdf%vss",10)
fmt.Println(str)

输出：
asdf10ss
```

strconv中的Atoi函数：ASCII转Integer

Atoi会返回一个result, err

1. Atoi, 是先按字符串长度算溢出的, 比如 1000000000000000000a, 他的err是溢出, 不是语法错误
2. err是溢出的时候, result的值是int64的max值
3. err是语法错误的时候, result的值是0

Go是静态类型语言，一旦某个变量被声明则无法改变；

### 函数

Go语言中，大写字母开头的函数，变量其它标识会被导出，其它包可用。

如果在函数声明时，多个形参类型相同，则改类型可只写一次。如：

```go
func swap(a, b int) (int, int) {
   return b, a
}
```

#### 可变参数函数

比如`fmt.Println(186,"seconds")`参数可以是不同类型。

Println的声明是这样的：

```go
func Println(a ...interface{}) (n int, err error) {
   return Fprintln(os.Stdout, a...)
}
```

+ ...表示函数的参数是可变的
+ 参数类型为interface{}代表是一个空接口

### 方法

方法其实也是个函数，它属于某个类型；

用关键字type可用申明新类型：

+ type celsius float64

+ var temperature celsius = 20

虽然celsius是一种全新的类型，但是其与float64有相同的功能

```go
type celsius float64
const degrees = 20
var temperature celsius = 10
temperature += degrees
fmt.Println(temperature)
```

> 声明新类型的好处：提高代码的可读性和维护性

虽然celsius有float64的功能，但其不能混着用，还是代表不同的类型

```go
var warmUp float64 = 10
// 会报错
temperature += warmUp
```

**在Java中，方法属于类，在go中它提供了方法，但是没有提供类和对象**

```go
type celsius float64
// 则代表将方法与celsius进行关联， 并可用当celsius当做一个参数
func (c celsius) celsius() celsius{
	return c - 273.15
}
```

+ celsius方法虽然没有参数，但是有一个接收者(c celsius)
+ 每个方法有多个参数，但只能有一个接收者
+ 在方法体中，接收者的行为和其它函数一样

## 一等函数

在go中，函数是头等的，它可用在整数、字符串或其它类型能用的地方。

+ 将函数赋值给变量
+ 将函数作为参数传递给函数
+ 将函数作为函数的返回类型

将函数赋值给变量：

```go
type kelvin float64
func fac() kelvin {
   return kelvin(rand.Intn(151) + 156)
}
func real() kelvin {
   return 0
}

func main() {
   sensor := fac
   fmt.Println(sensor())

   sensor = real
   fmt.Println(sensor())
}
```

将函数作为参数传递：

```go
type kelvin float64
func fac() kelvin {
   return kelvin(rand.Intn(151) + 156)
}

// 此时该sensor 可以为fac 也可以为real
func fa(samples int, sensor func() kelvin) {
   for i := 0; i < samples; i ++ {
      fmt.Println(sensor())
      time.Sleep(time.Second)
   }
}

func real() kelvin {
   return 0
}

func main() {  
   fa(3, fac)
   fa(3, real)
}
```

### 声明函数类型

声明函数类型，有助于精简和明确调用者的代码：

如 **type sensor func() kelvin**

故： `func a(s fuc() kelvin)`

可改为 `func a(s sensor)`

### 闭包和匿名函数

匿名函数：

```go
var f = func() {
    fmt.Println("我是没有名字的函数")
}
通过f()调用

另一种匿名函数
func() {
    fmt.Println("我是一个只会被使用一次的匿名函数")
}()
```

函数字面值需要保留外部作用域的变量引用，所以函数字面值都是闭包的

```go
type sensor func() kelvin

func kk() kelvin {
   return 5
}


func example(s sensor, offset kelvin) sensor {
   return func() kelvin {
      return s() + offset
   }
}

func main() {
    // 我们可以看到在此 example已经返回了
	f := example(kk, 7)
	// 但是在之后，我们调用f()依然可以访问外部的这两个参数(s sensor, offset kelvin)   ---- 闭包因此而得名
	fmt.Println(f())
}
```

也就是说返回这个函数已经把外部捕获的参数封装封闭在内部了。

**闭包就是匿名函数封闭并包围作用域中的变量而得名的**

```go
package main

import (
   "fmt"
   "math/rand"
   "time"
)

type kelvin float64
func fac() kelvin {
   return kelvin(rand.Intn(151) + 156)
}

// 此时该sensor 可以为fac 也可以为real
func fa(samples int, sensor func() kelvin) {
   for i := 0; i < samples; i ++ {
      fmt.Println(sensor())
      time.Sleep(time.Second)
   }
}

func real() kelvin {
   return 0
}

// 匿名函数
var f = func() {
   fmt.Println("我是没有名字的函数")
}


type sensor func() kelvin

func kk() kelvin {
   return 0
}


func example(s sensor, offset kelvin) sensor {
   return func() kelvin {
      return s() + offset
   }
}

func main() {
   sensor := fac
   fmt.Println(sensor())

   sensor = real
   fmt.Println(sensor())

   fa(3, fac)

   f()
   func() {
      fmt.Println("我是一个只会被使用一次的匿名函数")
   }()
   // 我们可以看到在此 example已经返回了
   f := example(kk, 7)
   // 但是在之后，我们调用f()依然可以访问外部的这两个参数(s sensor, offset kelvin)
   fmt.Println(f())


   var offset kelvin = 5
   sensor = example(kk, offset)
   for count := 10; count >= 0; count -- {
      offset ++
      // 一直返回5，因为 offset是值传递
      fmt.Println(sensor())
   }

   sensor = example(fac, offset)
   for count := 10; count >= 0; count -- {
      // 多次返回不同的随机数
      fmt.Println(sensor())
   }
}
```

### 习题

巩固学习函数类型，函数做参数的使用

题目：

![image-20210803011118796](https://cdn.jsdelivr.net/gh/cheerfulman/picGo/img/20210803011126.png)

答案：

```go
package main

import "fmt"

const (
	line 			= "======================="
	rowFormat 		= "| %8s | %8s |\n"
	numberFormat 	= "%.1f"
)
type celsius float64

type fahrenheit float64
func (c celsius) fahrenheit() fahrenheit {
	return fahrenheit((c * 9.0 / 5.0) + 32)
}

func (f fahrenheit) celsius() celsius {
	return celsius((f - 32.0) * 5.0 / 9.0)
}

func drawTable(unit1, unit2 string, stWarm, enWarm float64, getRows func(warm float64) string) {
	fmt.Println(line)
	fmt.Printf(rowFormat,unit1, unit2)
	fmt.Println(line)
	for i := stWarm; i <= enWarm; i += 5 {
		fmt.Println(getRows(i))
	}
}
func ctof(warm float64) string {
	cel := celsius(warm)
	fah := cel.fahrenheit()
	cstring := fmt.Sprintf(numberFormat, cel)
	hstring := fmt.Sprintf(numberFormat, fah)
	return fmt.Sprintf(rowFormat, cstring, hstring)
}

func ftoc(warm float64) string {
	fah := fahrenheit(warm)
	cel := fah.celsius()
	cstring := fmt.Sprintf(numberFormat, cel)
	hstring := fmt.Sprintf(numberFormat, fah)
	return fmt.Sprintf(rowFormat, hstring,cstring)
}

func main() {
	drawTable("℃", "℉", -40.0, 100, ctof)
	fmt.Println()
	drawTable("℉", "℃", -40.0, 100, ftoc)
}

```

