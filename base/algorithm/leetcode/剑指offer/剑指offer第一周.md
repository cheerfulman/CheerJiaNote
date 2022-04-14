# 剑指offer 第一周

## acwing-13.找出数组中重复的数字

给定一个长度为 nn 的整数数组 `nums`，数组中所有的数字都在0∼n−1 的范围内。

数组中某些数字是重复的，但不知道有几个数字重复了，也不知道每个数字重复了几次。

请找出数组中任意一个重复的数字。

**注意**：如果某些数字不在0∼n−1 的范围内，或数组中不包含重复数字，则返回 -1；

#### 样例

```
给定 nums = [2, 3, 5, 4, 3, 2, 6, 7]。

返回 2 或 3。
```

> 利用Set存储；
>
> set 存储时间复杂度O(1)，所以此题的时间复杂度是O(n),空间复杂度是O(n);

```java
class Solution {
    public int duplicateInArray(int[] nums) {
        Set<Integer> set = new HashSet<>();
        int flag = -1;
        for(int num : nums){
            if(num < 0 || num >= nums.length)return -1;
            if(!set.add(num))flag = num;
        }
        return flag != -1 ? flag : -1;
    }
}
```

> 利用原数组nums[] 求解
>
> 由于while()最多循环n次，故总时间复杂度为O(3*n)= O(n)，空间复杂度O(1)；

```java
class Solution {
    public int duplicateInArray(int[] nums) {
        int n = nums.length;
        for(int num : nums)
            if(num < 0 || num >= n)
                return -1;
                
        for(int i = 0; i < n; i ++){
            while(nums[i] != nums[nums[i]])swap(nums,i,nums[i]);
            if(i != nums[i])return nums[i];
        }
        return -1;
    }
    
    public void swap(int[] nums, int x,int y){
        int temp = nums[x];
        nums[x] = nums[y];
        nums[y] = temp;
    }
}
```

## acwing-14.不修改数组找出重复的数字

给定一个长度为 n+1 的数组`nums`，数组中所有的数均在 1∼n 的范围内，其中n≥1。

请找出数组中任意一个重复的数，但不能修改输入的数组。

#### 样例

```
给定 nums = [2, 3, 5, 4, 3, 2, 6, 7]。

返回 2 或 3。
```

**思考题**：如果只能使用 O(1) 的额外空间，该怎么做呢？

> 既然不能修改输入的数组，也不可以额外开辟空间，故用二分寻找

```java
class Solution {
    public int duplicateInArray(int[] nums) {
        int n = nums.length;
        for(int i = 0; i < n; i ++){
            if(binarySearch(nums, nums[i],i)) return nums[i];
        }
        return -1;
    }
    public boolean binarySearch(int[] nums, int target,int start){
        int l = start + 1, r = nums.length - 1;
        while(l < r){
            int mid = l + r >>> 1;
            if(nums[mid] >= target) r = mid;
            else l = mid + 1;
        }
        return nums[r] == target;
    }
}
```

> 利用抽屉原理求解： 因为每个数的范围在1~n，总数有n+1个，故必定有个位置的数量大于1；
>
> 分治：判断左半边和右半边，如果在l~mid的数的个数大于 mid  则代表左边有重复的数，反之右边

```java
class Solution {
    public int duplicateInArray(int[] nums) {
        int n = nums.length;
        int l = 1, r = n - 1;
        while(l < r){
            int mid = l + r >>> 1, s = 0;
            for(int x : nums){
                if(x >= l && x <= mid)s ++;
            }
            if(s > mid - l + 1)r = mid;
            else l = mid + 1;
        }
        return r;
    }
}
```

## acwing-15.二维数组中的查找

在一个二维数组中，每一行都按照从左到右递增的顺序排序，每一列都按照从上到下递增的顺序排序。

请完成一个函数，输入这样的一个二维数组和一个整数，判断数组中是否含有该整数。

#### 样例

```
输入数组：

[
  [1,2,8,9]，
  [2,4,9,12]，
  [4,7,10,13]，
  [6,8,11,15]
]

如果输入查找数值为7，则返回true，

如果输入查找数值为5，则返回false。
```

> 不断循环，利用二分查找，时间复杂度O(nlogn),空间复杂度O(1);

```java
class Solution {
    public boolean searchArray(int[][] array, int target) {
        for(int i = 0; i < array.length; i ++){
            int len = array[i].length;
            if(array[i][0] <= target && array[i][len - 1] >= target && binarySearch(array[i],target))return true;
        }
        return false;
    }
    public boolean binarySearch(int[] nums, int target){
        int l = 0, r = nums.length - 1;
        while(l < r){
            int mid = l + r >>> 1;
            if(nums[mid] >= target) r = mid;
            else l = mid + 1;
        }
        return nums[r] == target;
    }
}
```

**O(n+m) 做法**

> 此题有个规律，就是当前数左边的数都比 此数要小，下面的数都比次数要大。所以可以每次判断下target比当前数大还是小.
>
> 大的话 往下走，小的话往左走。

```java
class Solution {
    public boolean searchArray(int[][] array, int target) {
        if(array.length <= 0)return false;
        int len = array[0].length;
        int x = 0, y  = len - 1;
        while(x < len && y >= 0 && array[x][y] != target){
            if(target < array[x][y]) y--;
            else x ++;
        }
        if(x >= len || y < 0)return false;
        return array[x][y] == target;
    }
}
```

## 16.替换空格

请实现一个函数，把字符串中的每个空格替换成`"%20"`。

你可以假定输入字符串的长度最大是1000。
注意输出字符串的长度可能大于1000。

#### 样例

```
输入："We are happy."

输出："We%20are%20happy."
```

> java的库函数实现 -- 一行代码

```java
class Solution {
    public String replaceSpaces(StringBuffer str) {
        return str.toString().replaceAll(" ","%20");
    }
}
```

## 17.从头到尾打印链表

输入一个链表的头结点，按照 **从尾到头** 的顺序返回节点的值。

返回的结果用数组存储。

#### 样例

```
输入：[2, 3, 5]
返回：[5, 3, 2]
```

```java
class Solution {
    public int[] printListReversingly(ListNode head) {
        List<Integer> res = new ArrayList<>();
        while(head != null){
            res.add(head.val);
            head = head.next;
        }
        int n = res.size();
        int[] ans = new int[n];
        for(int i = res.size() - 1; i >= 0 ; i --){
            ans[n - i - 1] = res.get(i);
        }
        return ans;
    }
}
```

## 18.重建二叉树

输入一棵二叉树前序遍历和中序遍历的结果，请重建该二叉树。

**注意**:

- 二叉树中每个节点的值都互不相同；
- 输入的前序遍历和中序遍历一定合法；

#### 样例

```
给定：
前序遍历是：[3, 9, 20, 15, 7]
中序遍历是：[9, 3, 15, 20, 7]

返回：[3, 9, 20, null, null, 15, 7, null, null, null, null]
返回的二叉树如下所示：
    3
   / \
  9  20
    /  \
   15   7
```

> 利用递归，前序的第一个点，肯定是根，然后在中序遍历中找到该点k，则可以知道左子树的节点个数为 k - il（il 为 中序遍历数组的起点） 将数组分为左右两段。

```java
class Solution {
    int[] p,i;
    Map<Integer,Integer> map;
    public TreeNode buildTree(int[] preorder, int[] inorder) {
        p = preorder;i = inorder;
        map = new HashMap<>();
        for(int i = 0 ; i < inorder.length; i ++){
            map.put(inorder[i],i);
        }
        return dfs(0,p.length - 1, 0, i.length - 1);
    }
    // pl pr 前序遍历 的下标范围，il ir 中序遍历的下标范围
    public TreeNode dfs(int pl,int pr, int il,int ir){
        if(pl > pr)return null;
        // 前序遍历的第一个节点是根节点
        TreeNode root = new TreeNode(p[pl]);
        // 找到该根节点在 中序遍历的位置
        int k = map.get(p[pl]);
        // 左子树 的下标范围 变成， pl + 1, pl + (k - il) --- 中序遍历中得到左子树的数量为(k - il)，
        root.left = dfs(pl + 1, pl + k - il, il, k - 1);
        // 同理
        root.right = dfs(pl + k - il + 1, pr,k + 1,ir);
        return root;
    }
}
```

## 19.二叉树的下一个节点

给定一棵二叉树的其中一个节点，请找出中序遍历序列的下一个节点。

**注意：**

- 如果给定的节点是中序遍历序列的最后一个，则返回空节点;
- 二叉树一定不为空，且给定的节点一定不是空节点；

#### 样例

```
假定二叉树是：[2, 1, 3, null, null, null, null]， 给出的是值等于2的节点。

则应返回值等于3的节点。

解释：该二叉树的结构如下，2的后继节点是3。
  2
 / \
1   3
```

```java
class Solution {
    public TreeNode inorderSuccessor(TreeNode p) {
        // 当有 右子树的时候，返回右子树的最左边，如果没有左子树，即为本身
        if(p.right != null){
            p = p.right;
            while(p.left != null)p = p.left;
            return p;
        }
        
        // 当没有 右子树的时候，返回其节点是 父亲的 左子树的节点的父亲
        while(p.father != null && p == p.father.right)p = p.father;
        return p.father;
    }
}
```



## 20.用两个栈实现队列

请用栈实现一个队列，支持如下四种操作：

- push(x) – 将元素x插到队尾；
- pop() – 将队首的元素弹出，并返回该元素；
- peek() – 返回队首元素；
- empty() – 返回队列是否为空；

**注意：**

- 你只能使用栈的标准操作：`push to top`，`peek/pop from top`, `size` 和 `is empty`；
- 如果你选择的编程语言没有栈的标准库，你可以使用list或者deque等模拟栈的操作；
- 输入数据保证合法，例如，在队列为空时，不会进行`pop`或者`peek`等操作；

#### 样例

```
MyQueue queue = new MyQueue();

queue.push(1);
queue.push(2);
queue.peek();  // returns 1
queue.pop();   // returns 1
queue.empty(); // returns false
```

> 栈的反方向就是队列，当要pop的时候，将栈A的值，pop给栈B，此时栈B的top就是 队首了。peek() 同理

```java
class MyQueue {
    Stack<Integer> st1;
    Stack<Integer> st2;
    /** Initialize your data structure here. */
    public MyQueue() {
        st1 = new Stack<>();
        st2 = new Stack<>();
    }
    
    /** Push element x to the back of queue. */
    public void push(int x) {
        if(!st2.isEmpty()){
            while(!st2.isEmpty()){
                st1.push(st2.pop());
            }
        }
        st1.push(x);
    }
    
    /** Removes the element from in front of queue and returns that element. */
    public int pop() {
        while(!st1.isEmpty()){
            st2.push(st1.pop());
        }
        return st2.pop();
    }
    
    /** Get the front element. */
    public int peek() {
        if(!st2.isEmpty())return st2.peek();
        else{
            while(!st1.isEmpty()){
                st2.push(st1.pop());
            }
            return st2.peek();
        }
    }
    
    /** Returns whether the queue is empty. */
    public boolean empty() {
        if(st1.isEmpty() && st2.isEmpty())return true;
        return false;
    }
}
```

## 21.斐波那契数列

输入一个整数 nn ，求斐波那契数列的第 nn 项。

假定从0开始，第0项为0。(nn<=39)

#### 样例

```
输入整数 n=5 

返回 5
```

> cur0存储前前个数，cur0存储前一个数

```java
class Solution {
    public int Fibonacci(int n) {
        if(n == 1)return 1;
        if(n == 0)return 0;
        int cur0 = 1, cur1= 1,ans = 0;
        for(int i = 2; i < n; i ++){
            ans = cur0 + cur1;
            cur0 = cur1;
            cur1 = ans;
        }
        return ans;
    }
}
```

## 22.旋转数组的最小数字

把一个数组最开始的若干个元素搬到数组的末尾，我们称之为数组的旋转。

输入一个升序的数组的一个旋转，输出旋转数组的最小元素。

例如数组{3,4,5,1,2}为{1,2,3,4,5}的一个旋转，该数组的最小值为1。

数组可能包含重复项。

**注意**：数组内所含元素非负，若数组大小为0，请返回-1。

#### 样例

```
输入：nums=[2,2,2,0,1]

输出：0
```

> 题意：将一个升序数组，后半部分，放到前面来，求找出最小的元素；
>
> 题解: 先保证后部分的数都小于前部分，然后二分找。

```java
class Solution {
    public int findMin(int[] nums) {
        int n = nums.length - 1;
        if(n == -1)return -1;
        while(nums[n] == nums[0])n--;
        if(nums[n] >= nums[0])return nums[0];
        int l = 0, r = n;
        
        while(l < r){
            int mid = l + r >>> 1;
            if(nums[mid] >= nums[0]) l = mid + 1;
            else r = mid;
        }
        return nums[l];
    }
}
```

## 23.矩阵中的路径

请设计一个函数，用来判断在一个矩阵中是否存在一条包含某字符串所有字符的路径。

路径可以从矩阵中的任意一个格子开始，每一步可以在矩阵中向左，向右，向上，向下移动一个格子。

如果一条路径经过了矩阵中的某一个格子，则之后不能再次进入这个格子。

**注意：**

- 输入的路径不为空；
- 所有出现的字符均为大写英文字母；

#### 样例

```
matrix=
[
  ["A","B","C","E"],
  ["S","F","C","S"],
  ["A","D","E","E"]
]

str="BCCE" , return "true" 

str="ASAE" , return "false"
```

> dfs 寻找路径，记得还原标记。
>
> 还原与不还原区别: 其他地方到此点时是否有区别，有区别就还原，没区别，还原个屁

```java
class Solution {
    int[] dx = {0,0,1,-1}, dy = {1,-1,0,0};
    public boolean dfs(int x,int y,int len, String str,char[][] matrix){
        if(str.charAt(len) != matrix[x][y])return false;
        if(len + 1 == str.length()) return true;
        char t = matrix[x][y];
        matrix[x][y] = '#';
        for(int i = 0; i < 4; i ++){
            int new_x = x + dx[i];
            int new_y = y + dy[i];
            if(new_x >= 0 && new_x < matrix.length && new_y >= 0 && new_y < matrix[0].length){
                if(dfs(new_x,new_y,len + 1,str,matrix))return true;
            }
        }
        matrix[x][y] = t;
        return false;
    }
    public boolean hasPath(char[][] matrix, String str) {
        for(int i = 0; i < matrix.length; i++)
            for(int j = 0 ; j < matrix[i].length; j++)
                if(dfs(i,j,0,str,matrix))return true;
                
        return false;
    }
}
```

