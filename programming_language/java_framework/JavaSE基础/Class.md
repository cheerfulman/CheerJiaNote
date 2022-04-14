## Class

> RTTI（RunTime Type Information，运行时类型信息）能够在程序运行时发现和使用类型信息

 Java 是如何在运行时识别对象和类信息的。主要有两种方式：

1. “传统的” RTTI：假定我们在编译时已经知道了所有的类型；
2. “反射”机制：允许我们在运行时发现和使用类的信息。

实际上，`Class` 对象就是用来创建该类所有"常规"对象的。Java 使用 `Class` 对象来实现 RTTI，即便是类型转换这样的操作都是用 `Class` 对象实现的。

**构造器也是类的静态方法，虽然构造器前面并没有 `static` 关键字。所以，使用 `new` 操作符创建类的新对象，这个操作也算作对类的静态成员引用。**

所有的类都是第一次使用时动态加载到 JVM 中的，当程序创建第一个对类的静态成员的引用时，就会加载这个类。

其中有方法：

+ Class.forName() : `forName()` 是 `Class` 类的一个静态方法,根据目标类的类名（`String`）得到该类的 `Class` 对象。
+ getInterface() : 得到其实现的接口
+ getSuperclass() : 得到其基础的类(超类，父类)
+ isInterface() : 判断其是不是接口
+ getSimpleName() : 得到不带包名的类型
+ getName() : 完整类名
+ getCanonicalName() : 也是完整类名（除内部类和数组外，对大部分类产生的结果与 `getName()` 相同）。
+ newInstance() : 利用”虚拟构造器“，在不知道一个类的确切类型时，得到这个类的对象。

我们可以使用`forName()`得到Class,或者是在知道具体类的时候使用getClass() 通过Class，我们可以得到**RTTI**。

#### 类字面常量

| boolean.class | Boolean.TYPE   |
| ------------- | -------------- |
| char.class    | Character.TYPE |
| byte.class    | Byte.TYPE      |
| short.class   | Short.TYPE     |
| int.class     | Integer.TYPE   |
| long.class    | Long.TYPE      |
| float.class   | Float.TYPE     |
| double.class  | Double.TYPE    |
| void.class    | Void.TYPE      |

**当使用 `.class` 来创建对 `Class` 对象的引用时，不会自动地初始化该 `Class` 对象。**

使用类而做的准备工作包含三个步骤：

1. **加载**，这是由类加载器执行的。该步骤将查找字节码（通常在 classpath 所指定的路径中查找，但这并非是必须的），并从这些字节码中创建一个 `Class` 对象。
2. **链接**。在链接阶段将验证类中的字节码，为 `static` 字段分配存储空间，并且如果需要的话，将解析这个类创建的对其他类的所有引用。
3. **初始化**。如果该类具有超类，则先初始化超类，执行 `static` 初始化器和 `static` 初始化块。

```java
// typeinfo/ClassInitialization.java
import java.util.*;

class Initable {
    static final int STATIC_FINAL = 47;
    static final int STATIC_FINAL2 =
        ClassInitialization.rand.nextInt(1000);
    static {
        System.out.println("Initializing Initable");
    }
}

class Initable2 {
    static int staticNonFinal = 147;
    static {
        System.out.println("Initializing Initable2");
    }
}

class Initable3 {
    static int staticNonFinal = 74;
    static {
        System.out.println("Initializing Initable3");
    }
}

public class ClassInitialization {
    public static Random rand = new Random(47);
    public static void
    main(String[] args) throws Exception {
        Class initable = Initable.class;
        System.out.println("After creating Initable ref");
        // Does not trigger initialization:
        System.out.println(Initable.STATIC_FINAL);
        // Does trigger initialization:
        System.out.println(Initable.STATIC_FINAL2);
        // Does trigger initialization:
        System.out.println(Initable2.staticNonFinal);
        Class initable3 = Class.forName("Initable3");
        System.out.println("After creating Initable3 ref");
        System.out.println(Initable3.staticNonFinal);
    }
}
```

```
After creating Initable ref
47
Initializing Initable
258
Initializing Initable2
147
Initializing Initable3
After creating Initable3 ref
74
```

仅使用 `.class` 获得对类对象的引用不会引发初始化。

使用 `Class.forName()` 来产生 `Class` 引用会立即就进行初始化，

如果一个 `static final` 值是“编译期常量”，那么这个值不需要进行初始化就可以被读取。

如果一个 `static` 字段不是 `final` 的，那么在对它访问时，总是要求在它被读取之前，要先进行链接（为这个字段分配存储空间）和初始化（初始化该存储空间），就像在对 `Initable2.staticNonFinal` 的访问中所看到的那样。

## 反射

类Class支持反射的概念，`java.lang.reflect` 库中包含类 `Field`、`Method` 和 `Constructor`（每一个都实现了 `Member` 接口）**这些类型的对象由 JVM 在运行时创建**，以表示未知类中的对应成员。然后，可以使用 `Constructor` 创建新对象，`get()` 和 `set()` 方法读取和修改与 `Field` 对象关联的字段，`invoke()` 方法调用与 `Method` 对象关联的方法。此外，还可以调用便利方法 `getFields()`、`getMethods()`、`getConstructors()` 等，以返回表示字段、方法和构造函数的对象数组。

