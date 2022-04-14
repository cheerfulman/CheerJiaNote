## Integer缓存机制

Integer缓存大小为-128 ~ 127；

在jdk1.5后有自动装箱拆箱机制；

```java
Integer a = 3;
//等价于
Integer a = Integer.valueOf(3); //默认调用
int b = a;
//默认调用
int b = a.IntValue();
```

不知道大家有没有注意到，在ArrayList的方法中有两个重载的方法：

`remove(int index)`  和 `remove(Object o)` 假设现在 list 中有三个值（2，1，3），你调用remove(2),此时是删除的2还是下标为2的3呢？

答案是 ：**删除3**。

在这种情况下不会进行自动装箱，调用的是`remove(int index)`，换句话说也没必要，毕竟重载中已经有与其参数相匹配的Int类型了，为何还要装箱多此一举呢？



### == 和equals()

众所周知，`==`主要是对地址的判断，而`equas()`是对值判断是否相等。

结合自动装箱和`Integer`缓存范围，来看看下面的例子

```java
public class Main {
    public static void main(String[] args) {
 
        Integer a = 1;
        Integer b = 2;
        Integer c = 3;
        Integer d = 3;
        Integer e = 321;
        Integer f = 321;
        Long g = 3L;
        Long h = 2L;
 
        System.out.println(c==d);
        System.out.println(e==f);
        System.out.println(c==(a+b));
        System.out.println(c.equals(a+b));
        System.out.println(g==(a+b));
        System.out.println(g.equals(a+b));
        System.out.println(g.equals(a+h));
    }
}

Output:
true
false
true
true
true
false
true
```



这些运行结果你是否都答对了呢？

1. 在`==`运算符中，是判断包装类型的引用是否相等，即是否指向同一地址（**如果包含算术运算，就会触发自动拆箱**）。
2. `equals()`方法不会进行类型转换，仅比较对象的值
3. 第三句中`c==(a+b)`  由于包含算术运算进行了自动拆箱，故比较的是值是否相等（**可以看作int，而基本类型存储在栈内存中，值相等的，地址都一样**）。
4. 而`c.equals(a+b)` 则会先拆箱，再装箱，最后比较；



**在包装类中只有`Float`和`Double`没有缓存机制，直接new对象，其他都有。**

附上源码：`Double`

```java
public static Double valueOf(String s) throws NumberFormatException {
        return new Double(parseDouble(s));
    }
public static Double valueOf(double d) {
        return new Double(d);
    }
```

附上源码：`Float`

```java
public static Float valueOf(String s) throws NumberFormatException {
        return new Float(parseFloat(s));
    }
public static Float valueOf(float f) {
        return new Float(f);
    }
```



### 警惕 `NullPointerException`

类中的基本类型的成员在声明的时候即使我们没有对变量进行赋值，编译器也会自动的为其赋予初始值，比如 `int` 值就是 0，`boolean` 类型的就是 `false`，`char`类型就是`空`，所以我们在使用基本类型的时候，是不会出现`NullPointerException` 的。

但在使用包装类的时候，我们就要注意这个问题了，不能因为有自动拆装箱这个语法糖，就忘记了包装类和基本类型的区别。

如果你在使用包装类时没有通过显式、或是通过自动装箱机制为其赋值，在你取出值、或是通过自动拆箱使用该值的时候，就会发生 `NullPointerException`，这个是大家要注意的。



参考学习：https://mp.weixin.qq.com/s?__biz=MzI1ODQ3NDA2Mg==&mid=2247484223&idx=1&sn=2f81638d472ee4411e9b94ac15f278d6&chksm=ea06ea1edd716308411acd6127b611dab7bf3c608f6ea3c4e0bbd64e9cf87c68b2016943b072&scene=21#wechat_redirect