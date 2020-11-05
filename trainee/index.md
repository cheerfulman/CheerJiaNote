## Git命令初学

### Git和SVN

+ Git是分布式版本控制，每台电脑都有一个完整的版本库
+ Svn是集中式版本控制，版本库集中在中央，每台电脑需要联网，从集中版本库中获得最新版本

![](../img/image-20201105174604425.png)

### Git基本命令

```text
git add 放入stage区
git commit -m "注释内容，方便寻找版本"  提交到Repository
git status  查看状态
git diff [file1] 查看与Repository有何不同
```

> git status 查看状态

#### git init

![image-20201105201733015](../img/image-20201105201733015.png)

#### git add

![image-20201105201759185](../img/image-20201105201759185.png)

#### git commit -m "注释"

![image-20201105201903425](../img/image-20201105201903425.png)

