# 剑指offer第六周

## 79. 滑动窗口的最大值

给定一个数组和滑动窗口的大小，请找出所有滑动窗口里的最大值。

例如，如果输入数组[2, 3, 4, 2, 6, 2, 5, 1]及滑动窗口的大小3,那么一共存在6个滑动窗口，它们的最大值分别为[4, 4, 6, 6, 6, 5]。

**注意：**

- 数据保证k大于0，且k小于等于数组长度。

##### 样例

```
输入：[2, 3, 4, 2, 6, 2, 5, 1] , k=3

输出: [4, 4, 6, 6, 6, 5]
```

> 单调队列模板 --- 注意放的是下标

```java
class Solution {
    public int[] maxInWindows(int[] nums, int k) {
        List<Integer> list = new ArrayList<>();
        ArrayDeque<Integer> q = new ArrayDeque<>();
        for(int i = 0; i < nums.length; i++){   
            // 当 队尾的数 小于或等于 当前数nums[i]时，则 队尾的数字就没有用了，Poll()；
            while(!q.isEmpty() && nums[q.peekLast()] <= nums[i])q.pollLast();
            // 如果 队列的 大小大于等于k， 则代表队首位置的数 毕业了；
            while(!q.isEmpty() && i - q.peek() >= k)q.poll();
            q.offer(i);
            if(i >= k - 1)list.add(nums[q.peek()]);
        }
        int[] res = new int[list.size()];
        for(int i = 0; i < list.size(); i++)res[i] = list.get(i);
        return res;
    }
}
```

## 80. 骰子的点数

将一个骰子投掷n次，获得的总点数为s，s的可能范围为n~6n。

掷出某一点数，可能有多种掷法，例如投掷2次，掷出3点，共有[1,2],[2,1]两种掷法。

请求出投掷n次，掷出n~6n点分别有多少种掷法。

#### 样例1

```
输入：n=1

输出：[1, 1, 1, 1, 1, 1]

解释：投掷1次，可能出现的点数为1-6，共计6种。每种点数都只有1种掷法。所以输出[1, 1, 1, 1, 1, 1]。
```

#### 样例2

```
输入：n=2

输出：[1, 2, 3, 4, 5, 6, 5, 4, 3, 2, 1]

解释：投掷2次，可能出现的点数为2-12，共计11种。每种点数可能掷法数目分别为1,2,3,4,5,6,5,4,3,2,1。

      所以输出[1, 2, 3, 4, 5, 6, 5, 4, 3, 2, 1]。
```

> f【i】【j】 表示 扔i次 得到点数j的次数
>
> 第j次丢的点数是由j - 1丢的点数来的； 

```java
f[i][j] += f[i - 1][j - k];
```

```java
class Solution {
    public int[] numberOfDice(int n) {
        int[][] f = new int[n + 1][6 * n + 1];
        f[0][0] = 1;
        for(int i = 1; i <= n; i ++)
            for(int j = 1; j <= 6 * n; j ++)
                for(int k = 1; k <= Math.min(j,6); k++)
                    f[i][j] += f[i - 1][j - k];
        int[] res = new int[6 * n - n + 1];
        for(int i = n; i <= 6 * n; i ++)res[i - n] = f[n][i];
        
        return res;
    }
}
```

## 81. 扑克牌的顺子

从扑克牌中随机抽5张牌，判断是不是一个顺子，即这5张牌是不是连续的。

2～10为数字本身，A为1，J为11，Q为12，K为13，大小王可以看做任意数字。

为了方便，大小王均以0来表示，并且假设这副牌中大小王均有两张。

#### 样例1

```
输入：[8,9,10,11,12]

输出：true
```

#### 样例2

```
输入：[0,8,9,11,12]

输出：true
```

> 排序，如果里面有相同的数返回false，如果没有，查看第一个不为0的数与最大的数之间的差是否在4以内即可，大王可以随意变换数字；

```java
class Solution {
    public boolean isContinuous(int [] nums) {
        Arrays.sort(nums);
        for(int i = 1; i < nums.length; i ++)if(nums[i] > 0 && nums[i] == nums[i - 1])return false;
        
        for(int num : nums)
            if(num > 0) return nums[nums.length - 1] - num <= 4;
            
        return false;
    }
}
```

## 82.圆圈中最后剩下的数字

0, 1, …, n-1这n个数字(n>0)排成一个圆圈，从数字0开始每次从这个圆圈里删除第m个数字。

求出这个圆圈里剩下的最后一个数字。

#### 样例

```
输入：n=5 , m=3

输出：3
```

> 反推，当最后面只剩他一个人的时候，他必定在第0个位置上；
>
> 当只剩两人的时候，他的位置now在 now = pre(前一个位置) + m(数多少个数) % i (当前总人数)
>
> 也就是说在其位置前加m个人，则每次都不会选到它；
>
> 最后推出总人数为n时，now当前的序号

```java
class Solution {
    // now = pre(前一个位置) + m(数多少个数) % i (当前总人数)
    public int lastRemaining(int n, int m) {
        int pre = 0;
        for(int i = 2; i <= n; i ++){
            pre = (pre + m) % i;
        }
        return pre;
    }
}
```

## 83. 股票的最大利润

假设把某股票的价格按照时间先后顺序存储在数组中，请问买卖 **一次** 该股票可能获得的利润是多少？

例如一只股票在某些时间节点的价格为[9, 11, 8, 5, 7, 12, 16, 14]。

如果我们能在价格为5的时候买入并在价格为16时卖出，则能收获最大的利润11。

#### 样例

```
输入：[9, 11, 8, 5, 7, 12, 16, 14]

输出：11
```

> 贪心：动态记录最小值，将后面的值 - 最小值，来更新最大利润

```java
class Solution {
    public int maxDiff(int[] nums) {
        if(nums.length < 2) return 0;
        int minn = nums[0], n = nums.length, res = 0;
        for(int i = 1; i < n; i++){
            res = Math.max(res,nums[i] - minn);
            minn = Math.min(minn,nums[i]);
        }
        return res;
    }
}
```

## 84.求1+2+…+n

求1+2+…+n,要求不能使用乘除法、for、while、if、else、switch、case等关键字及条件判断语句（A?B:C）。

#### 样例

```
输入：10

输出：55
```

> 等差数列求和

```java
class Solution {
    public int getSum(int n) {
        long temp = (1l + n) * n;
        return (int) (temp / 2);
    }
}
```

> 题目说不能用乘除法，故用递归

```java
class Solution {
    public int getSum(int n) {
        return dfs(n);
    }
    
    public int dfs(int n){
        if(n == 0)return 0;
        int res = n;
        res += dfs(n - 1);
        return res;
    }
}
```

## 85. 不用加减乘除做加法

写一个函数，求两个整数之和，要求在函数体内不得使用＋、－、×、÷ 四则运算符号。

#### 样例

```
输入：num1 = 1 , num2 = 2

输出：3
```

```java
class Solution {
    // num2 k 0;
    // 左移后 至少 k + 1 0;
    // 异或 ^ 1 1 = 0, 1 0 = 1, 0 0 = 0,  相当于不进位加法
    //  & 当 两个都是1时  进位， 再左移 相当于 进位的数，相加则为 原来的数
    public int add(int num1, int num2) {
        while(num2 > 0){
            int sum = num1 ^ num2;
            num2 = (num1 & num2) << 1;
            num1 = sum;
        }
        return num1;
    }
}
```

## 86. 构建乘积数组

给定一个数组`A[0, 1, …, n-1]`，请构建一个数组`B[0, 1, …, n-1]`，其中B中的元素`B[i]=A[0]×A[1]×… ×A[i-1]×A[i+1]×…×A[n-1]`。

不能使用除法。

#### 样例

```
输入：[1, 2, 3, 4, 5]

输出：[120, 60, 40, 30, 24]
```

**思考题**：

- 能不能只使用常数空间？（除了输出的数组之外）

> 先枚举前一半，然后后一半

```java
class Solution {
    public int[] multiply(int[] A) {
        int[] res = new int[A.length];
        for(int i = 0, p = 1; i < A.length; i ++){
            res[i] = p;
            p *= A[i];
        }
        
        for(int i = A.length - 1,p = 1; ~i != 0; i --){
            res[i] *= p;
            p *= A[i];
        }
        return res;
    }
}
```

## 87. 把字符串转换成整数

请你写一个函数StrToInt，实现把字符串转换成整数这个功能。

当然，不能使用atoi或者其他类似的库函数。

#### 样例

```
输入："123"

输出：123
```

**注意**:

你的函数应满足下列条件：

1. 忽略所有行首空格，找到第一个非空格字符，可以是 ‘+/−’ 表示是正数或者负数，紧随其后找到最长的一串连续数字，将其解析成一个整数；
2. 整数后可能有任意非数字字符，请将其忽略；
3. 如果整数长度为0，则返回0；
4. 如果整数大于INT_MAX(2^31 − 1)，请返回INT_MAX；如果整数小于INT_MIN(−2^31) ，请返回INT_MIN；

> 模拟题

```java
class Solution {
    public int strToInt(String str) {
        int maxx = Integer.MAX_VALUE, minn = Integer.MIN_VALUE;
        if(str.length() == 0) return 0;
        // res 答案， flag 记录符号，digit记录 res的长度
        int res = 0, flag = 1,digit = 0;
        // 去掉首位空字符串
        str = str.trim();
        // 判断是否是 非字符串开始，如果是直接break
        boolean start = false;
        for(int i = 0; i < str.length(); i ++){
            if(str.charAt(i) == '-'){flag = -1;continue;}
            if(str.charAt(i) == '+')continue;
            if(str.charAt(i) > '9' || str.charAt(i) < '0'){
                if(start)continue;
                else break;
            }
            // 接下来要加的数字
            int bit = (str.charAt(i) - '0');
            if(flag == 1){
                if((res < maxx / 10 || digit == 9 && bit <= 7 && res / 1000000000 <= 2))res = res * 10 + bit;
                else return maxx;
            }else{
                if((res < maxx / 10 || digit == 9 && bit <= 8 && res / 1000000000 <= 2))res = res * 10 + bit;
                else return minn;
            }
            digit ++;
            start = true;
        }
        return res * flag;
    }
}
```

## 88. 树中两个结点的最低公共祖先

给出一个二叉树，输入两个树节点，求它们的最低公共祖先。

一个树节点的祖先节点包括它本身。

**注意：**

- 输入的二叉树不为空；
- 输入的两个节点一定不为空，且是二叉树中的节点；

#### 样例

```
二叉树[8, 12, 2, null, null, 6, 4, null, null, null, null]如下图所示：
    8
   / \
  12  2
     / \
    6   4

1. 如果输入的树节点为2和12，则输出的最低公共祖先为树节点8。

2. 如果输入的树节点为2和6，则输出的最低公共祖先为树节点2。
```

> 从根开始寻找左右子树，如果发现某棵树就是要找的p,或者q，则直接返回p,q如果左边找到 p,q 右边没找到，那么左边的就是最近祖先，如果都找到则返回其公共祖先，root，如果都没找到，返回null；

```java
class Solution {
    public TreeNode lowestCommonAncestor(TreeNode root, TreeNode p, TreeNode q) {
        if(root == null)return null;
        if(root == p || root == q)return root;
        TreeNode left = lowestCommonAncestor(root.left,p,q);
        TreeNode right = lowestCommonAncestor(root.right,p,q);
        
        if(left != null && right != null) return root;
        if(left == null && right != null)return right;
        if(right == null && left != null)return left;
        return null;
    }
}
```

