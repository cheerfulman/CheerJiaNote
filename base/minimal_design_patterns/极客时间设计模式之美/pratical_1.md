# 业务开发常用的基于贫血模型的MVC架构违背OOP吗？

我们都知道，很多业务系统都是基于 MVC 三层架构来开发的。实际上，更确切点讲，这是一种基于贫血模型的 MVC 三层架构开发模式。

它是一种彻底的面向过程编程风格，也被有些人称为**反模式**。

## 什么是基于贫血模型的传统开发模式？

像mvc，分为 Repository 层、Service 层、Controller 层。其中，Repository 层负责数据访问，Service 层负责业务逻辑，Controller 层负责暴露接口。

相对很多Java项目来说，**UserEntity 和 UserRepository** 组成了数据访问层，**UserBo 和 UserService** 组成了业务逻辑层，**UserVo 和 UserController** 在这里属于接口层。

其中，Entity、BO、VO其中就只包含数据，而Repository、Service、Controller就只包含方法，这种就叫贫血模式。将数据与操作相分离，破坏了面向对象的封装特性，是一种典型的面向过程的编程风格。

## 什么是基于充血模型的 DDD 开发模式？

**充血模型**（Rich Domain Model）：数据和对应的业务逻辑被封装到同一个类中。

### 什么是领域驱动设计？

在基于充血模型的 DDD 开发模式中，Service 层包含 Service 类和 Domain 类两部分。Domain 就相当于贫血模型中的 BO。不过，Domain 与 BO 的区别在于它是基于充血模型开发的，既包含数据，也包含业务逻辑。而 Service 类变得非常单薄。总结一下的话就是，基于贫血模型的传统的开发模式，重 Service 轻 BO；基于充血模型的 DDD 开发模式，轻 Service 重 Domain。

### 为什么基于贫血模型的传统开发模式如此受欢迎？

1. 基本逻辑比较简单，都是CRUD。

2. 充血模型设计复杂，

3. 思维固化，以前都是贫血模型，不随意改动，且有学习成本

### 什么项目应该考虑使用基于充血模型的 DDD 开发模式？

DDD开发模式需要事先理清楚所有的业务，定义领域模型所包含的属性和方法。领域模型相当于可复用的业务中间层。

所以适合复杂的项目，如果比较简单的项目，就没必要花这么多时间在DDD上，直接贫血模式梭哈即可。

### 充血贫血区别

基于充血模型的 DDD 开发模式跟基于贫血模型的传统开发模式相比，主要区别在 Service 层。在基于充血模型的开发模式下，我们将部分原来在 Service 类中的业务逻辑移动到了一个充血的 Domain 领域模型中，让 Service 类的实现依赖这个 Domain 类。

在基于充血模型的 DDD 开发模式下，Service 类并不会完全移除，而是负责一些不适合放在 Domain 类中的功能。比如，负责与 Repository 层打交道、跨领域模型的业务聚合功能、幂等事务等非功能性的工作。

基于充血模型的 DDD 开发模式跟基于贫血模型的传统开发模式相比，Controller 层和 Repository 层的代码基本上相同。这是因为，Repository 层的 Entity 生命周期有限，Controller 层的 VO 只是单纯作为一种 DTO。两部分的业务逻辑都不会太复杂。业务逻辑主要集中在 Service 层。所以，Repository 层和 Controller 层继续沿用贫血模型的设计思路是没有问题的。
