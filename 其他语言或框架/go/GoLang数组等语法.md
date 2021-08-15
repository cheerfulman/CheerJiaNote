## 数组

未被赋值的值则为零值；

```go
// 声明数组
var planets [8]string
planets[0] = "Mercury"
planets[1] = "Venus"
planets[2] = "Earth"

earth := planets[2]
fmt.Println(earth)

fmt.Println(len(planets))
fmt.Println(planets[3] == "")

输出：
Earth
8
true
```

+ 复合字面值（composite literal）是一种复合类型初始化的紧凑型语法

+ Go的复合字面值语法允许我们只用一步就完成数组的声明和初始化两部操作：

  `drf := [3]string {"123", "234", "345"}`

+ 可以在复合字面值中使用...作为数组长度

  `drf := [...]string {"123", "234", "345"}`

+ 无论使用哪种方式，数组的长度都是固定的

### 数组的复制

+ 数组在赋值给新变量时或者将它传给函数，都会产生一个数组副本，所以数组作为函数参数效率低下
+ 数组的长度也是数组类型的一部分
  + 如果将长度不符的数组作为参数传递，将会报错
+ 所以函数一般使用slice作为参数传递

```go
var planets [8]string
planets[0] = "Mercury"
planets[1] = "Venus"
planets[2] = "Earth"
// 值传递，发生拷贝
newPlanet := planets
newPlanet[2] = "new"
fmt.Println(newPlanet)
fmt.Println(planets)

输出：
[Mercury Venus new     ]
[Mercury Venus Earth     ]
```

## 切片

+ 假设planets是一个数组，则planets[0:4]就是切片，切除了数组的前四个元素
+ 切数组不会导致数组被修改，只是创建了一个指向数组的窗口或视图

切片省略起始位置则代表从头切

省略结束索引，则切到末尾，末尾索引不能大于数组长度

都省略则全部

slice索引不能为负

**slice切的是字节数而不是rune数**

```go
name := "我真aaaa"
nameSlice := name[:8]
fmt.Println(nameSlice)

输出：
我真aa
```

### 创建切片

```go
name := "我真aaaa"
// go语言会线创建{"123", "111"} 的数组，然后用切片包括它
dwarfs := []string{"123", "111"}
dwarf := name[:]
```

切片当参数时：

```go
// 切片的长度不是切片类型的一部分
func hyperspace(worlds []string)  {
   for i, _ := range worlds {
      worlds[i] = strings.TrimSpace(worlds[i])
   }
}
func main() {
	name := [...]string{"我真aaaa  ","   asdf"}
	dwarf := name[:]
    // 切片值被改变
	hyperspace(dwarf)
	fmt.Println(strings.Join(dwarf, ""))
}
输出：
我真aaaaasdf
```

### 更大的切片

可以使用append在切片中追加数据

```go
slice := []string{"1", "2"}
slice = append(slice, "3")
fmt.Println(slice)
输出：
[1 2 3]
```

### 切片的长度和容量（length && capacity）

+ slice中的个数决定了slice的长度
+ 如果slice底层数组比slice还大，那么就说该slice还有容量可增长

```go
func dump(label string, slice []string)  {
   fmt.Printf("%v: length %v, capacity %v, %v\n",
      label, len(slice), cap(slice), slice)
}
func main() {
   slice := []string{"1", "2"}
   slice = append(slice, "3")

   dump("slice", slice)

   // capacity 为3 代表从1开始截取的原数组
   dump("slice[1:2]", slice[1:2])
   fmt.Println(slice)
}
```

切片还有三切片操作[1:4:4]代表从1截取到4，并且容量为4

使用**make函数**对slice进行预分配，可以避免额外的内存分配和数组复制操作；

如果make中只有兩個參數则第二个参数表示长度和容量；

```go
// 长度1， 容量10 
makeSlice := make([]string, 1, 10)
// 长度1， 容量1
makeSlice1 := make([]string, 1)
```

### 声明可变参数的函数

```go
func terraform(prefix string, worlds ...string) []string{
   newWorlds := make([]string, len(worlds))

   for i := range worlds {
      newWorlds[i] = prefix + " " + worlds[i]
   }
   return newWorlds
}

// 不能使用slice 而要使用slice...
// ...string不是一个切片类型，而要把切片展开...
fmt.Println(terraform("new ", slice...))
```

小测试：

![image-20210804204201471](https://cdn.jsdelivr.net/gh/cheerfulman/picGo/img/20210804204208.png)

```go
func testSliceCap(slice []int) {
   var i, preCap = 0, cap(slice)
   for {
      slice = append(slice, i)
      i ++
      if cap(slice) != preCap {
         preCap = cap(slice)
         fmt.Printf("前一次扩容容量为：%v, 扩容后为: %v\n",
            preCap, cap(slice))
      }
      if preCap >= 100 {
         break
      }
   }
}
输出：
前一次扩容容量为：2, 扩容后为: 2
前一次扩容容量为：4, 扩容后为: 4
前一次扩容容量为：8, 扩容后为: 8
前一次扩容容量为：16, 扩容后为: 16
前一次扩容容量为：32, 扩容后为: 32
前一次扩容容量为：64, 扩容后为: 64
前一次扩容容量为：128, 扩容后为: 128
```

## map

通过make创建map

```go
func main() {
   tempMap := map[string]int {
      "Earth" : 15,
      "Mars" : 3,
   }

   fmt.Println(tempMap["Earth"])
   tempMap["Earth"] = 3
   fmt.Println(tempMap["Earth"])
}
输出：
15
3
```

如果访问map中没有的键，则返回零值

```
nul := tempMap["aa"]
fmt.Println(nul)
输出：
0
```

Go中的Ok写法

```go
if moon, ok := tempMap["moon"]; ok {
   fmt.Printf("moon is %v", moon)
}else {
   fmt.Printf("no moon key %v", moon)
}
```

**map不会被复制**：数组，Int,float64等类型在赋值时或传递给函数时，会创建副本，但是map不会

他们都会指向同一块内存；

```go
tempMap := map[string]int {
   "Earth" : 15,
   "Mars" : 3,
}

tempNew := tempMap
tempNew["new"] = 1
fmt.Println(tempMap)
fmt.Println(tempNew)

delete(tempNew, "Earth")
fmt.Println(tempMap)
fmt.Println(tempNew)

// 先同时增加了new -> 1 键值对，然后又都删除了Earth -> 15
输出：
map[Earth:15 Mars:3 new:1]
map[Earth:15 Mars:3 new:1]
map[Mars:3 new:1]
map[Mars:3 new:1]
```

### 使用make函数对map进行预分配

```go
m := make(map[float64]int, 8)
fmt.Println(len(m))
输出:
0
```

默认长度为0；

map只有两种方式创建，一种是make，一种是直接字面值：

```go
tempMap := map[string]int {
   "Earth" : 15,
   "Mars" : 3,
}

m := make(map[float64]int, 8)
```

用map模拟set 

`set := make(map[int]bool)`

## 结构struct

声明struct

```go
// 声明一个结构体变量
var curiosity struct {
    x float64
    y float64
}
curiosity.x = 12.3
curiosity.y = 12.3

// 声明一个结构体类型
type location struct {
    x float64
    y float64
}

var locate location
locate.x = 13.2
locate.y = 13.2

newLocate := location{x: 12.6, y: 13.9}
fmt.Println(newLocate)
newNlocate := location{18.0, 19.4}
fmt.Printf("%v\n",newNlocate)
// 字段名也带上
fmt.Printf("%+v\n",newNlocate)
```

### struct转json

使用json.Marshal

如果想使用json.Marshal方法来转结构体，那就要把结构体中的属性名的首字母大写(public)，所以我把结构体的首字母都大写了。

```go
fmt.Printf("%+v\n", locate)
bytes, err := json.Marshal(locate)
if err != nil {
   fmt.Println(err)
   os.Exit(1)
}

fmt.Println("1",string(bytes))
输出：
{X:13.2 Y:13.2}
1 {"X":13.2,"Y":13.2}
```

如果在用Marshal处理json可以定义处理的属性名字

```go
// 声明一个结构体类型
type location struct {
   X float64 `json:"Xx"`
   Y float64 `json:"Yy"`
}

var locate location
locate.X = 13.2
locate.Y = 13.2
fmt.Printf("%+v\n", locate)
bytes, err := json.Marshal(locate)
if err != nil {
   fmt.Println(err)
   os.Exit(1)
}

fmt.Println(string(bytes))
输出：
{X:13.2 Y:13.2}
{"Xx":13.2,"Yy":13.2}
```

使用MarshalIndent使json打印更美观

```go
newBytes, err := json.MarshalIndent(locate, "", "\t")
fmt.Println(string(newBytes))
输出：
{
	"Xx": 13.2,
	"Yy": 13.2
}
```

### go的构造函数

go中的构造函数要自己去构造，一般以new开头的这种

```go
func newLocation (x float64, y float64) location{
   return location{X: x, Y: y}
}
```

### New函数

有一些用于构造的函数名称就是New（如errors包中的New函数）

### 组合

在面向对象的世界中，对象由更小的对象组合而成

Go通过结构体实现组合

Go提供了嵌入的特性，实现方法的转发

```go
type Earth struct {
	slot int
	dis distance
	temp temperature
}

type distance struct {
	x , y float64
}

type temperature struct {
	height, low celsius
}

type celsius float64

func (t temperature) average() celsius {
	return (t.low + t.height) / 1
}

func (e Earth) average() celsius {
	return e.temp.average()
}


func main() {
	bradbury := distance{-3.77, 7.90}
	t := temperature{low: 12.7, height: 78.3}
	earth := Earth{
		slot: 14,
		dis: bradbury,
		temp: t,
	}

	fmt.Println(earth.average())
	fmt.Println(earth.temp.average())
	
	fmt.Printf("%+v\n", earth.temp)
}
输出：
91
91
{height:78.3 low:12.7}
```

#### struct嵌入

```go
// 默认为其初始化了一个变量名为distance、temperature
// 并且可以直接调用temperature的方法
// fmt.Println(earth.average())
// fmt.Println(earth.height)
type Earth struct {
	slot int
	distance
	temperature
}
```

通过这种方式嵌入后，可以直接进行转发，不仅是方法，而且还有变量名

如果字段中的`distance`和`temperature`都有`average()`方法，那么转发就回发生问题。你可以通过

```go
func (e Earth) average() celsius {
	return e.temp.average()
}
```

覆盖掉冲突的`average()`方法，即可直接通过`e.average()`调用

**优先使用组合还不是继承**

### 接口

+ 接口关注类型可以做什么
+ 接口通过列举必须满足的一组方法来声明
+ 在Go语言中，不需要显式声明接口

声明接口

```go
var t interface{
   talk() string
}
```

```go
// 声明接口变量 t
var t interface{
   talk() string
}

type Mari struct {}
type Loser int

func (m Mari) talk() string {
   return "Mari mari"
}

func (l Loser) talk() string {
   return "Loser loser" + strings.Repeat(" ll", int(l))
}
func main() {
   t = Mari{}
   fmt.Println(t.talk())
   t = Loser(4)
   fmt.Println(t.talk())
}
输出：
Mari mari
Loser loser ll ll ll ll
```

声明接口类型：

```go
type talker interface {
   talk() string
}

type Mari struct {}
type Loser int
// 即继承了t 接口
func (m Mari) talk() string {
   return "Mari mari"
}
// 即继承了t 接口
func (l Loser) talk() string {
   return "Loser loser" + strings.Repeat(" ll", int(l))
}
// 可以通过方法的参数传入
func shout(t talker)  {
   louder := strings.ToUpper(t.talk())
   fmt.Println(louder)
}

func main() {
   shout(Mari{})
   shout(Loser(2))
}
```

也可以完成转发：

```go
type startShip struct {
   Loser
}
// 即继承了t 接口
func (l Loser) talk() string {
	return "Loser loser" + strings.Repeat(" ll", int(l))
}

// 可以通过方法的参数传入
func shout(t talker)  {
	louder := strings.ToUpper(t.talk())
	fmt.Println(louder)
}

func main() {
	fmt.Println(startShip{Loser(3)}.talk())
	shout(startShip{Loser(6)})
}
输出：
Loser loser ll ll ll
LOSER LOSER LL LL LL LL LL LL
```

#### 探索接口

```go
func stardate(t time.Time) float64 {
   doy := float64(t.YearDay())
   h := float64(t.Hour()) / 24.0
   return 1000 + doy + h
}

func main() {
   day := time.Date(2018,7,6, 18, 32,
      0,0,time.UTC)
   fmt.Printf("%.1f Curiosity has landed", stardate(day))
}
输出：
1187.8 Curiosity has landed


// 由于t.time 实现了这两个方法，故相当于实现了这个接口
// 可以在stardate方法中将time.Time参数改成 stardater
type stardater interface {
	YearDay() int
	Hour() int
}
func stardate(t stardater) float64 {
	doy := float64(t.YearDay())
	h := float64(t.Hour()) / 24.0
	return 1000 + doy + h
}
```

go可以后置继承其接口，本来time.Time是写死的，但是可以通过后来申明的接口让其继承     -------------   因为不用显示继承接口

#### 满足接口

+ Go标准库导出了很多只有单个方法的接口
+ Go通过简单、通常只有单个方法的接口，来鼓励组合而不是继承，这些接口在各个组件之间形成了简明的界限。

###  指针

+ 指针式的指向另一个变量的地址
+ Go语言的指针同时强调安全性，不会出现迷途指针（也称野指针）

#### &和*符号

+ 变量会将他们的值存在计算机的RAM里，存储位置就是该变量的内存地址
+ &表示地址操作符，通过&可以获得变量的内存地址
  + &无法获得字符串/数值/布尔值的类型地址
  + &42，&"hello",都会编译报错
+ *操作符与&相反，用来解引用，提供内存地址指向的值

```go
func main() {
	answer := 42
	fmt.Println(&answer)
	fmt.Println(*&answer)
	address := &answer
	fmt.Println(*address)
}
输出：
0xc00000a0a0
42
42
```

![image-20210807184425143](https://cdn.jsdelivr.net/gh/cheerfulman/picGo/img/20210807184425.png)

#### 指针

+ 将*放在类型前表示什么指针类型
+ *放在变量前表示解引用操作

```go
var home *string
str := "string"
home = &str
fmt.Println(*home)
输出：
string
```

```go
var administrator *string

scolese := "Christopher J. Scolese"
administrator = &scolese
fmt.Println(*administrator)

bolden := "Charles F. Bolden"
administrator = &bolden
fmt.Println(*administrator)

bolden = "Charles Frank Bolden Jr."
fmt.Println(*administrator)

*administrator = "Maj. Gen. Charles Frank Bolden Jr."
fmt.Println(bolden)

major := administrator
*major = "Major General Charles Frank Bolden Jr."
fmt.Println(bolden)
fmt.Println(administrator == major)

输出：
Christopher J. Scolese
Charles F. Bolden
Charles Frank Bolden Jr.
Maj. Gen. Charles Frank Bolden Jr.
Major General Charles Frank Bolden Jr.
true
```

![image-20210807191058572](https://cdn.jsdelivr.net/gh/cheerfulman/picGo/img/20210807191059.png)

#### 指针接收者

Go在变量通过点标记法进行调用时，自动使用&取得变量的内存地址

+ 所以不用写(&nathan).birthday()

```go
type person struct {
   name, superpower string
   age int
}

//func birthday(p *person) {
// p.age++
//}
func (p *person) birthday() {
   p.age++

}
func main() {
   rebecca := person{
      name:       "Rebecca",
      superpower: "imagination",
      age:        14,
   }
   //birthday(&rebecca)
   rebecca.birthday()

   fmt.Printf("%+v\n", rebecca)

   nathan := person{
      name: "Nathan",
      age: 17,
   }
   nathan.birthday()
   fmt.Printf("%+v\n", nathan)
}
输出：
{name:Rebecca superpower:imagination age:15}
{name:Nathan superpower: age:18}
```

+ 使用指针作为接收者的策略应该始终如一：
+ **如果一种类型的某些方法需要使用到指针作为接收者，就应该为这种类型的所有方法都用指针作为接收者**

#### 隐式指针

+ Go语言里一些内置的集合在暗中使用指针
+ Map在被赋值或被作为参数传递的时候不会被复制
  + map就是一种隐式的指针

#### 切片指向数组

每个切片在内部都会被表示为一个包含3个元素的结构

+ 指向数组的指针
+ 切片的容量
+ 切片的长度。

当切片被直接传递至函数或者方法的时候，切片的内部指针就可以对底层数据进行修改。

**实现了接收者是值类型的方法，相当于自动实现了接收者是指针类型的方法；而实现了接收者是指针类型的方法，不会自动生成对应接收者是值类型的方法。**

> 如果实现了接收者是值类型的方法，会隐含地也实现了接收者是指针类型的方法。

```go
type coder interface {
	code()
	debug()
}

type Gopher struct {
	language string
}

func (p Gopher) code() {
	fmt.Printf("I am coding %s language\n", p.language)
}

func (p *Gopher) debug() {
	fmt.Printf("I am debuging %s language\n", p.language)
}

func main() {
	var c coder = &Gopher{"Go"}
	c.code()
	c.debug()
}
输出：
code()
debug()


var c coder = Gopher{"Go"}
c.code()
c.debug()

报错：
cannot use Gopher literal (type Gopher) as type coder in assignment:
Gopher does not implement coder (debug method has pointer receiver)
```

### nil

GO语言中，nil是一个零值

nil会导致panic

```go
var nullPoint *int
fmt.Println(nullPoint)
// 会panic
fmt.Println(*nullPoint)
```

```go
type Person struct {
	age int
}

func (p *Person) up()  {
	p.age ++
}

func main() {
	var person *Person
	// 在此处不会报错，而是在p.age中报错，因为up这个方法相当于可以传进去一个	nil,但不能用nil.age
	person.up()
}
```

判断传进来的函数是否为nil

```go
func sortStrings(s []string, less func(i, j int) bool) {
   if less == nil {
      less = func(i, j int) bool {
         return s[i] < s[j]
      }
   }

   sort.Slice(s, less)
}
func main() {

	food := []string {"onion", "carrot", "celery"}
	sortStrings(food, nil)
	fmt.Println(food)
}
```

nil的slice和空的slice并不相等

make的初始化的是空，而`var n []string`则是nil

```go
// ma 为 nil
//var ma map[string]int
// ma 为 空
//ma := map[string]int{}
// ma 为 空
ma := make(map[string]int)
fmt.Println(ma == nil)

ma["11"] = 2
fmt.Println(ma)
```

#### nil接口

```go
var v interface{}
fmt.Printf("%T %v %v\n", v, v, v == nil)
var p *int
v = p
fmt.Printf("%T %v %v\n", v, v, v == nil)
// 接口变量内部表示
fmt.Printf("%#v\n", v)
输出：
<nil> <nil> true
*int <nil> false
(*int)(nil)
```

+ 接口类型只有在类型和值都为nil时才等于nil
  + 如果类型不是nil，值为Nil则接口类型就不是nil

### 错误

一般golang中第二个参数返回error，检查下error是否不为nil

```go
func main() {
	files, err := ioutil.ReadDir(".")
	if err != nil {
		fmt.Println(err)
		os.Exit(1)
	}
	
	for _, file := range files {
		fmt.Println(file.Name())
	}
}
```

#### defer关键字

使用defer关键字，确保defer的语句被执行如finally

#### Recover

使用recover相当于catch了panic，不会发生恐慌

```go
defer func() {
   if e := recover(); e != nil {
      fmt.Println(e)
   }
}()
panic("I Love You!")
```