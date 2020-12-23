# 剑指Offer第三周

## 35.反转链表

定义一个函数，输入一个链表的头结点，反转该链表并输出反转后链表的头结点。

**思考题：**

- 请同时实现迭代版本和递归版本。

#### 样例

```
输入:1->2->3->4->5->NULL

输出:5->4->3->2->1->NULL
```

> pre,cur,next,暴力翻转
>
> pre = null,cur = pre, pre = cur,cur = next; 翻转 模板

```java
class Solution {
    public ListNode reverseList(ListNode head) {
        ListNode pre = null, cur = head;
        while(cur != null){
            ListNode next = cur.next;
            cur.next = pre;
            pre = cur;
            cur = next;
        }
        return pre;
    }
}
```

## 36.合并两个排序的链表

输入两个递增排序的链表，合并这两个链表并使新链表中的结点仍然是按照递增排序的。

#### 样例

```
输入：1->3->5 , 2->4->5

输出：1->2->3->4->5->5
```

> 没什么好说的，归并排序的思想

```java
class Solution {
    public ListNode merge(ListNode l1, ListNode l2) {
        ListNode dump = new ListNode(-1);
        ListNode head = dump;
        while(l1 != null && l2 != null){
            if(l1.val <= l2.val){
                dump.next = l1;
                dump = dump.next;
                l1 = l1.next;
            }else{
                dump.next = l2;
                dump = dump.next;
                l2 = l2.next;
            }
        }
        if(l1 != null) dump.next = l1;
        else dump.next = l2;
        return head.next;
    }
}
```

## 37.树的子结构

输入两棵二叉树A，B，判断B是不是A的子结构。

我们规定空树不是任何树的子结构。

#### 样例

树A：

```
     8
    / \
   8   7
  / \
 9   2
    / \
   4   7
```

树B：

```
   8
  / \
 9   2
```

返回 **true** ,因为B是A的子结构。

> 写一个helper函数，判断两棵树是否相等。递归判断每一个节点

```java
class Solution {
    public boolean hasSubtree(TreeNode p, TreeNode q) {
        if(p == null || q == null)return false;
        return helper(p,q) || hasSubtree(p.left,q) || hasSubtree(p.right,q);
    }
    public boolean helper(TreeNode p, TreeNode q){
        if(p == null && q == null)return true;
        if(p == null)return false;
        if(q == null)return true;
        if(p.val != q.val)return false;
        return helper(p.left,q.left) && helper(p.right,q.right);
    }
}
```

## 38.二叉树的镜像

输入一个二叉树，将它变换为它的镜像。

#### 样例

```
输入树：
      8
     / \
    6  10
   / \ / \
  5  7 9 11

 [8,6,10,5,7,9,11,null,null,null,null,null,null,null,null] 
输出树：
      8
     / \
    10  6
   / \ / \
  11 9 7  5

 [8,10,6,11,9,7,5,null,null,null,null,null,null,null,null]
```

> 对每一个节点的左右子树进行交换

```java
class Solution {
    public void mirror(TreeNode root) {
        if(root == null)return ;
        mirror(root.left);
        mirror(root.right);
        TreeNode temp = root.left;
        root.left = root.right;
        root.right = temp;
    }
}
```

## 39.对称的二叉树

请实现一个函数，用来判断一棵二叉树是不是对称的。

如果一棵二叉树和它的镜像一样，那么它是对称的。

#### 样例

```
如下图所示二叉树[1,2,2,3,4,4,3,null,null,null,null,null,null,null,null]为对称二叉树：
    1
   / \
  2   2
 / \ / \
3  4 4  3

如下图所示二叉树[1,2,2,null,4,4,3,null,null,null,null,null,null]不是对称二叉树：
    1
   / \
  2   2
   \ / \
   4 4  3
```

> 拿树的左边和右边比，右边和左边比

```java
class Solution {
    public boolean isSymmetric(TreeNode root) {
        if(root == null)return true;
        return helper(root.left,root.right);
    }
    public boolean helper(TreeNode p,TreeNode q){
        if(p == null && q == null)return true;
        if(p == null || q == null)return false;
        if(p.val == q.val) return helper(p.left,q.right) && helper(p.right,q.left);
        return false;
    }
}
```

## 40.顺时针打印矩阵

输入一个矩阵，按照从外向里以顺时针的顺序依次打印出每一个数字。

#### 样例

```
输入：
[
  [1, 2, 3, 4],
  [5, 6, 7, 8],
  [9,10,11,12]
]

输出：[1,2,3,4,8,12,11,10,9,5,6,7]
```

> 直接模拟

```java
class Solution {
    public int[] printMatrix(int[][] matrix) {
        int r = matrix.length;
        if(r <= 0)return new int[0];
        int c = matrix[0].length,i = 0, j = 0,cnt = 0;
        boolean[][] vis = new boolean[r][c];
        int total = r * c;
        int[] res = new int[total];
        
        while(cnt < total){
            // 右
            while(j < c && !vis[i][j]){
                res[cnt++] = matrix[i][j];
                vis[i][j] = true;
                j++;
            }
            j--;i++;
            // 下
            while(i < r && !vis[i][j]){
                res[cnt++] = matrix[i][j];
                vis[i][j] = true;
                i++;
            }
            i--;j--;
            // 左
            while(j >= 0 && !vis[i][j]){
                res[cnt++] = matrix[i][j];
                vis[i][j] = true;
                j--;
            }
            j++;i--;
            // 上
            while(i >= 0 && !vis[i][j]){
                res[cnt++] = matrix[i][j];
                vis[i][j] = true;
                i--;
            }
            i++;j++;
        }
        return res;
        
    }
}
```

> d 为方向，下一个点不能走则换方向

```java
class Solution {
    public int[] printMatrix(int[][] matrix) {
        int r = matrix.length;
        if(r <= 0)return new int[0];
        int c = matrix[0].length,cnt = 0,d = 0;
        boolean[][] vis = new boolean[r][c];
        int[] res = new int[r * c],dx = {0,1,0,-1}, dy = {1,0,-1,0};
        int x = 0,y = 0;
        
        for(int i = 0; i < r * c; i ++){
            res[cnt++] = matrix[x][y];
            vis[x][y] = true;
            int a = x + dx[d], b = y + dy[d];
            if(a < 0 || a >= r || b < 0 || b >= c || vis[a][b]){
                d = (d + 1) % 4;
                a = x + dx[d]; b = y + dy[d];
            }
            x = a; y = b;
        }
        
        return res;
        
    }
}
```

## 41.包含min函数的栈

设计一个支持push，pop，top等操作并且可以在O(1)时间内检索出最小元素的堆栈。

- push(x)–将元素x插入栈中
- pop()–移除栈顶元素
- top()–得到栈顶元素
- getMin()–得到栈中最小元素

#### 样例

```
MinStack minStack = new MinStack();
minStack.push(-1);
minStack.push(3);
minStack.push(-4);
minStack.getMin();   --> Returns -4.
minStack.pop();
minStack.top();      --> Returns 3.
minStack.getMin();   --> Returns -1.
```

> 设置一个min,存储最小的值，当min要发生改变时，将这个min存入stack，方便min被pop时，取出前一个min；

```java
class MinStack {
    Stack<Integer> stack;
    Integer min;
    /** initialize your data structure here. */
    public MinStack() {
        stack = new Stack<>();
        min = Integer.MAX_VALUE;
    }
    
    public void push(int x) {
        if(x <= min){
            stack.push(min);
            min = x;
            stack.push(x);
        }else stack.push(x);
        
    }
    
    public void pop() {
        Integer cur = stack.pop();
        if(cur == min){
            min = stack.pop();
        }
    }
    
    public int top() {
        return stack.peek();
    }
    
    public int getMin() {
        return min;
    }
}
```

## 42. 栈的压入、弹出序列

输入两个整数序列，第一个序列表示栈的压入顺序，请判断第二个序列是否可能为该栈的弹出顺序。

假设压入栈的所有数字均不相等。

例如序列1,2,3,4,5是某栈的压入顺序，序列4,5,3,2,1是该压栈序列对应的一个弹出序列，但4,3,5,1,2就不可能是该压栈序列的弹出序列。

**注意**：若两个序列长度不等则视为并不是一个栈的压入、弹出序列。若两个序列都为空，则视为是一个栈的压入、弹出序列。

#### 样例

```
输入：[1,2,3,4,5]
      [4,5,3,2,1]

输出：true
```

> 当前只有两个操作：
>
> 1. 当栈顶元素与其相等时，pop
> 2. 佛则入栈
>
> 如果栈顶不相等也Pop，的话，序列就不对了；

```java
class Solution {
    public boolean isPopOrder(int[] push,int[] pop) {
        if(push.length != pop.length)return false;
        Stack<Integer> stack = new Stack<>();
        int i = 0;
        for(Integer num : push){
            stack.push(num);
            while(!stack.isEmpty() && stack.peek() == pop[i]){
                stack.pop();
                i ++;
            }
        }
        return stack.isEmpty();
    }
}
```

## 43.不分行从上往下打印二叉树

从上往下打印出二叉树的每个结点，同一层的结点按照从左到右的顺序打印。

#### 样例

```
输入如下图所示二叉树[8, 12, 2, null, null, 6, null, 4, null, null, null]
    8
   / \
  12  2
     /
    6
   /
  4

输出：[8, 12, 2, 6, 4]
```

> 简单层次遍历

```java
class Solution {
    public List<Integer> printFromTopToBottom(TreeNode root) {
        if(root == null) return new ArrayList<Integer>();
        List<Integer> list = new ArrayList<Integer>();
        ArrayDeque<TreeNode> q = new ArrayDeque<TreeNode>();
        q.offer(root);
        list.add(root.val);
        
        while(!q.isEmpty()){
            int count = q.size();
            for(int i = 0; i < count; i ++){
                TreeNode cur = q.poll();
                if(cur.left != null){
                    list.add(cur.left.val);
                    q.offer(cur.left);
                }
                if(cur.right != null){
                    list.add(cur.right.val);
                    q.offer(cur.right);
                }
            }
        }
        
        return list;
    }
}
```

## 44.分行从上往下打印二叉树

从上到下按层打印二叉树，同一层的结点按从左到右的顺序打印，每一层打印到一行。

#### 样例

```
输入如下图所示二叉树[8, 12, 2, null, null, 6, null, 4, null, null, null]
    8
   / \
  12  2
     /
    6
   /
  4

输出：[[8], [12, 2], [6], [4]]
```

> 跟上一题一模一样，宽搜即可，每个cout代表每一层的个数

```java
class Solution {
    public List<List<Integer>> printFromTopToBottom(TreeNode root) {
        if(root == null) return new ArrayList<>();
        List<List<Integer>> list = new ArrayList<>();
        ArrayDeque<TreeNode> q = new ArrayDeque<TreeNode>();
        q.offer(root);
        ArrayList<Integer> pp = new ArrayList<Integer>();pp.add(root.val);
        list.add(pp);
        
        while(!q.isEmpty()){
            int count = q.size();
            ArrayList<Integer> temp = new ArrayList<Integer>();
            for(int i = 0; i < count; i ++){
                TreeNode cur = q.poll();
                if(cur.left != null){
                    temp.add(cur.left.val);
                    q.offer(cur.left);
                }
                if(cur.right != null){
                    temp.add(cur.right.val);
                    q.offer(cur.right);
                }
            }
            if(temp.size() > 0)list.add(temp);
        }
        
        return list;
    }
}
```

## 45.之字形打印二叉树

请实现一个函数按照之字形顺序从上向下打印二叉树。

即第一行按照从左到右的顺序打印，第二层按照从右到左的顺序打印，第三行再按照从左到右的顺序打印，其他行以此类推。

#### 样例

```
输入如下图所示二叉树[8, 12, 2, null, null, 6, 4, null, null, null, null]
    8
   / \
  12  2
     / \
    6   4
输出：[[8], [2, 12], [6, 4]]
```

> 同理，在上一题的基础上，加一个flag标志位，代表当前从左往右，还是从右往左；

```java
class Solution {
    public List<List<Integer>> printFromTopToBottom(TreeNode root) {
        if(root == null) return new ArrayList<>();
        List<List<Integer>> list = new ArrayList<>();
        ArrayDeque<TreeNode> q = new ArrayDeque<TreeNode>();
        q.offer(root);
        ArrayList<Integer> pp = new ArrayList<Integer>();pp.add(root.val);
        list.add(pp);
        boolean f = false;
        
        
        while(!q.isEmpty()){
            int count = q.size();
            ArrayList<Integer> temp = new ArrayList<Integer>();
            for(int i = 0; i < count; i ++){
                if(f){
                    TreeNode cur = q.pollFirst();
                    if(cur.left != null){
                        temp.add(cur.left.val);
                        q.offerLast(cur.left);
                    }
                    if(cur.right != null){
                        temp.add(cur.right.val);
                        q.offerLast(cur.right);
                    }
                }else{
                    TreeNode cur = q.pollLast();
                    if(cur.right != null){
                        temp.add(cur.right.val);
                        q.offerFirst(cur.right);
                    }
                    if(cur.left != null){
                        temp.add(cur.left.val);
                        q.offerFirst(cur.left);
                    }
                }
            }
            f = !f;
            if(temp.size() > 0)list.add(temp);
        }
        return list;
    }
}
```

```java
class Solution {
    public List<List<Integer>> printFromTopToBottom(TreeNode root) {
        List res = new ArrayList();
        List<TreeNode> q = new ArrayList<>();
        if(root != null)q.add(root);
        boolean reverse = false;
        
        while(q.size() > 0){
            List cur_v = new ArrayList();
            List cur_q = new ArrayList();
            for(TreeNode t : q){
                cur_v.add(t.val);
                if(t.left != null)cur_q.add(t.left);
                if(t.right != null)cur_q.add(t.right);
            }
            q = cur_q;
            if(reverse)Collections.reverse(cur_v);
            reverse = !reverse;
            res.add(cur_v);
        }
        
        return res;
    }
}
```

