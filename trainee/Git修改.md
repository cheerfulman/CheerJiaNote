## Git修改

### Git管理修改

**Git新增了暂存区的概念；**

假设我们修改了一个文件：

```text
lomontzhu@lomontzhu-pc0 MINGW64 ~/Desktop/note (master)
$ cat trainee/Git修改.md
## Git修改

Git新增了暂存区的概念：

假设我们修改了一个文件
```

然后添加：

```txt
lomontzhu@lomontzhu-pc0 MINGW64 ~/Desktop/note (master)
$ git status
On branch master
Changes to be committed:
  (use "git restore --staged <file>..." to unstage)
        new file:   "trainee/Git\344\277\256\346\224\271.md"
```

此时文件以及在`stage`区域了如果我们再修改：

```txt
lomontzhu@lomontzhu-pc0 MINGW64 ~/Desktop/note (master)
$ cat trainee/Git修改.md
## Git修改

**Git新增了暂存区的概念；**

假设我们修改了一个文件：

​```text
lomontzhu@lomontzhu-pc0 MINGW64 ~/Desktop/note (master)
$ cat trainee/Git修改.md
## Git修改

Git新增了暂存区的概念：
```

然后提交:

```git
lomontzhu@lomontzhu-pc0 MINGW64 ~/Desktop/note (master)
$ git commit -m "git tracks changes"
[master fc25c50] git tracks changes
 1 file changed, 16 insertions(+)
 create mode 100644 "trainee/Git\344\277\256\346\224\271.md"

lomontzhu@lomontzhu-pc0 MINGW64 ~/Desktop/note (master)
$ git status
On branch master
Changes not staged for commit:
  (use "git add <file>..." to update what will be committed)
  (use "git restore <file>..." to discard changes in working directory)
        modified:   "trainee/Git\344\277\256\346\224\271.md"
```

我们发现`modified:   "trainee/Git\344\277\256\346\224\271.md"`第二次修改没提交上去；

我们可以用`git diff head -- trainee/Git修改.md`查看工作区和版本库里的区别 ---> 发现确实没修改；

### 小结

+ 第一次修改 -> `git add` -> 第二次修改 -> `git commit` 版本库里存放的就是第一次修改的内容
+ 因为第一次`git add`后文件在`stage`区，而你第二次修改的是`workspace`区域，当你`git commit` 后将`stage`区提交到版本库中，故是第一次修改的内容
+ 我们在每次修改时，都可以git add到`stage`中，这样就不会漏啦；

### 撤销修改

