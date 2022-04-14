# 剑指offer第二周

## 24.机器人的运动范围

地上有一个 mm 行和 nn 列的方格，横纵坐标范围分别是 0∼m−10∼m−1 和 0∼n−10∼n−1。

一个机器人从坐标0,0的格子开始移动，每一次只能向左，右，上，下四个方向移动一格。

但是不能进入行坐标和列坐标的数位之和大于 kk 的格子。

请问该机器人能够达到多少个格子？

#### 样例1

```
输入：k=7, m=4, n=5

输出：20
```

#### 样例2

```
输入：k=18, m=40, n=40

输出：1484

解释：当k为18时，机器人能够进入方格（35,37），因为3+5+3+7 = 18。
      但是，它不能进入方格（35,38），因为3+5+3+8 = 19。
```

**注意**:

1. `0<=m<=50`
2. `0<=n<=50`
3. `0<=k<=100`

> dfs, 如果机器人当前位置可以去，则去判断上下左右能不能去；

```java
class Solution {
    int ans = 0;
    boolean[][] vis;
    public int movingCount(int threshold, int rows, int cols){
        boolean[][] vis = new boolean[rows][cols];
        // System.out.println(rows + " " + cols);
        dfs(threshold,0,0,rows,cols);
        return ans;
    }
    public void dfs(int k, int x,int y,int rows,int cols){
        // System.out.println(x + " " + y);
        if(x >= rows || y >= cols || x < 0 || y < 0 || get(x,y) > k || vis[x][y])return ;
        else{
            vis[x][y] = true;
            ans ++;
            dfs(k,x + 1,y,rows,cols);
            dfs(k,x - 1,y,rows,cols);
            dfs(k,x,y + 1,rows,cols);
            dfs(k,x,y - 1,rows,cols);
        }
    }
    
    public int get(int i ,int j){
        int res = 0;
        while(i > 0){
            res += (i % 10);
            i /= 10;
        }
        while(j > 0){
            res += (j % 10);
            j /= 10;
        }
        return res;
    }
}
```

## 25.减绳子

给你一根长度为 nn 绳子，请把绳子剪成 mm 段（mm、nn 都是整数，2≤n≤582≤n≤58 并且 m≥2m≥2）。

每段的绳子的长度记为k[0]、k[1]、……、k[m]。k[0]k[1] … k[m] 可能的最大乘积是多少？

例如当绳子的长度是8时，我们把它剪成长度分别为2、3、3的三段，此时得到最大的乘积18。

#### 样例

```
输入：8

输出：18
```

> 暴力，枚举；

```java
class Solution {
    public int maxProductAfterCutting(int length)
    {
        int maxLen = length / 2; 
        int res = 1;
        for(int i = 2; i <= maxLen; i ++){
            res = Math.max(res, cut(length,i));
        }
        return res;
    }
    // 每次 选取中间的数乘，必定最大；
    public int cut(int length,int cnt){
        int ans = 1;
        while(cnt > 0){
            int avg = length / cnt;
            ans *= avg;
            cnt --;
            length -= avg;
        }
        return ans;
    }
}
```

> y总说小学生题：可我还是暴力做的；
>
> 结论：每个数能取3，则取三，否则取2；
>
> 证明：N = n1 + n2 + n3 + ..... nk + .....nN;
>
> 当n1 >=  5时，必定可以分成 3 * (n1 - 3)  > n1;
>
> n1 = 4时 ，n1 = 2*2;
>
> 不会有1的情况；

```java
class Solution {
    public int maxProductAfterCutting(int n)
    {
        if(n == 2)return 1;
        int res = 1;
        if(n % 3 == 1){
            res *= 4;
            n -= 4;
        }
        if(n % 3 == 2){
            res *= 2;
            n -= 2;
        }
        while(n >= 3){
            res *= 3;
            n -= 3;
        }
        return res;
    }
}
```

## 26.二进制中1的个数

输入一个32位整数，输出该数二进制表示中1的个数。

**注意**：

- 负数在计算机中用其绝对值的补码来表示。

#### 样例1

```
输入：9
输出：2
解释：9的二进制表示是1001，一共有2个1。
```

#### 样例2

```
输入：-2
输出：31
解释：-2在计算机里会被表示成11111111111111111111111111111110，
      一共有31个1。
```

> 树状数组中的lowbit，返回最后一个1的位置。（利用计算机中负数的补码）
>
> 也可以利用 x & (x-1) 直接



```java
class Solution {
    public int NumberOf1(int n)
    {
        int res = 0;
        while(n != 0){
            n = n & (n - 1);// 直接把最后一个1剪掉，那么&的时候就消失啦，看各位看官喜欢哪种；
            // n -= lowbit(n); 同理
            res ++;
        }
        return res;
    }
    public int lowbit(int n){
        return n & (-n);
    }
}
```

## 27.数值的整数次方

实现函数*double Power(double base, int exponent)*，求*base*的 *exponent*次方。

不得使用库函数，同时不需要考虑大数问题。

**注意：**

- 不会出现底数和指数同为0的情况
- 当底数为0时，指数一定为正

#### 样例1

```
输入：10 ，2

输出：100
```

#### 样例2

```
输入：10 ，-2  

输出：0.01
```

> 经典快速幂

```java
class Solution {
    public double Power(double base, int e) {
        double res = 1;
        long mi = e;
        if(e < 0){
            mi = -e;
            base = 1 / base;
        }
        while(mi > 0){
            if((mi & 1) == 1) res *= base;
            base *= base;
            mi >>= 1;
        }
        return res;
   } 
}
```

## 28.在O(1)时间删除链表结点

给定单向链表的一个节点指针，定义一个函数在O(1)时间删除该结点。

假设链表一定存在，并且该节点一定不是尾节点。

#### 样例

```
输入：链表 1->4->6->8
      删掉节点：第2个节点即6（头节点为第0个节点）

输出：新链表 1->4->8
```

> 由于此题为单链表，无法得到前驱节点，故要删除此节点，将下一个节点的值赋给自己，并且删除下一个节点的值

```java
class Solution {
    public void deleteNode(ListNode node) {
        node.val = node.next.val;
        node.next = node.next.next;
    }
}
```

## 29.删除链表中重复的节点

在一个排序的链表中，存在重复的结点，请删除该链表中重复的结点，重复的结点不保留。

#### 样例1

```
输入：1->2->3->3->4->4->5

输出：1->2->5
```

#### 样例2

```
输入：1->1->1->2->3

输出：2->3
```

> 本菜鸡，pre,cur,next利用三节点删除代码。（冗余）

```java
class Solution {
    public ListNode deleteDuplication(ListNode head) {
        ListNode dump = new ListNode(-1);
        dump.next = head;
        ListNode temp = head,pre = dump;
        while(temp != null ){
            ListNode cur = temp.next;
            if(cur != null && temp.val == cur.val){
                while(cur != null && temp.val == cur.val){
                    cur = cur.next;
                }
                pre.next = cur;
                // pre = temp;
                temp = cur;
            }else{
                pre = temp;
                temp = cur;
            }
        }
        return dump.next;
    }
}
```

> y总整洁代码，以哑节点代替pre，判断next走了多少步，如果只走一步，则代表不用删，如果多走了则必定有相同元素，删；

```java
class Solution {
    public ListNode deleteDuplication(ListNode head) {
        ListNode dump = new ListNode(-1);
        dump.next = head;
        ListNode temp = dump;
        while(temp.next != null ){
            ListNode cur = temp.next;
            while(cur != null && temp.next.val == cur.val){
                cur = cur.next;
            }
            if(temp.next.next == cur)temp = temp.next;
            else temp.next = cur;
        }
        return dump.next;
    }
}
```

## 32.调整数组顺序使奇数位于偶数前面

输入一个整数数组，实现一个函数来调整该数组中数字的顺序。

使得所有的奇数位于数组的前半部分，所有的偶数位于数组的后半部分。

#### 样例

```
输入：[1,2,3,4,5]

输出: [1,3,5,2,4]
```

> 利用快排的原理

```java
class Solution {
    public void reOrderArray(int [] array) {
        int i = 0, j = array.length - 1;
        while(i < j){
            while(i < j && (array[i] & 1) == 1)i++;
            while(j > i && (array[j] & 1) == 0)j--;
            if(i < j){
                int temp = array[i];
                array[i] = array[j];
                array[j] = temp;
            }
        }
    }
}
```

## 33.链表中倒数第k个节点

输入一个链表，输出该链表中倒数第k个结点。

**注意：**

- `k >= 0`;
- 如果k大于链表长度，则返回 NULL;

#### 样例

```
输入：链表：1->2->3->4->5 ，k=2

输出：4
```

> 经典快慢指针

```java
class Solution {
    public ListNode findKthToTail(ListNode head, int k) {
        ListNode slow = head,fast = head;
        while(k-- > 0){
            if(fast == null)return null;
            fast = fast.next;
        }
        while(fast != null){
            slow = slow.next;
            fast = fast.next;
        }
        return slow;
    }
}
```

## 34.链表中环的入口节点

给定一个链表，若其中包含环，则输出环的入口节点。

若其中不包含环，则输出`null`。

#### 样例

![QQ截图20181202023846.png](https://www.acwing.com/media/article/image/2018/12/02/19_69ba6d14f5-QQ%E6%88%AA%E5%9B%BE20181202023846.png)

```
给定如上所示的链表：
[1, 2, 3, 4, 5, 6]
2
注意，这里的2表示编号是2的节点，节点编号从0开始。所以编号是2的节点就是val等于3的节点。

则输出环的入口节点3.
```

> 由ListNode结构体得出，此链为单链表，单链表最多只会有一个环，故可判断，当一个点被重复走过时，此点为环的入口（入口会被最先经过两次）
>
> 故：哈希做法 时间复杂度O(n),空间O(n)

```java
class Solution {
    Set<ListNode> set = new HashSet<>();
    public ListNode entryNodeOfLoop(ListNode head) {
        ListNode temp = head;
        while(temp != null){
            if(set.isEmpty() || !set.contains(temp))set.add(temp);
            else return temp;
            temp = temp.next;
        }
        return null;
    }
}
```

> 最优解:双链表 时间复杂度O(n),空间O(1)
>
> 假设它们相遇的时候为y，起点到环入口为x，那么快指针走了2*(x+y) 慢指针走(x+y)
>
> 得出快指针在环内走x+2y  ,减去离环起点的距离y，故x+y 一定是环的整数倍
>
> 所以从相遇点c，再走x步，一定能到起点b；

![https://www.acwing.com/media/article/image/2019/01/06/1_54311a0411-QQ%E5%9B%BE%E7%89%8720180531162503.png](https://www.acwing.com/media/article/image/2019/01/06/1_54311a0411-QQ图片20180531162503.png)

```java
class Solution {
    public ListNode entryNodeOfLoop(ListNode head) {
        ListNode slow = head,fast = head;
        while(fast != null && slow != null){
            slow = slow.next;
            fast = fast.next;
            // 防止 无环情况下 NullPointerException
            if(fast != null)fast = fast.next;
            else return null;
            // 当成环，让fast 再走 x步，回到起点（x+y）是圈的整数倍
            if(slow == fast){
                slow = head;
                while(slow != fast){
                    slow = slow.next;
                    fast = fast.next;
                }
                return slow;
            }
        }
        return null;
    }
}
```

