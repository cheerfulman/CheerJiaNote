# 剑指offer第六周

## 68. 0到n-1中缺失的数字

一个长度为n-1的递增排序数组中的所有数字都是唯一的，并且每个数字都在范围0到n-1之内。

在范围0到n-1的n个数字中有且只有一个数字不在该数组中，请找出这个数字。

#### 样例

```
输入：[0,1,2,4]

输出：3
```

> 这题题目绕了点，我不如这样看，长度为n，范围为0~n,共n+1个
>
> 所以所有值加起来为(0+n) * n+1 / 2

```java
class Solution {
    public int getMissingNumber(int[] nums) {
        int sum = (nums.length + 1) * nums.length / 2;
        for(int num : nums)sum -= num;
        return sum;
    }
}
```

> 二分求解O(logn)；
>
> [0,1,2,4]，我们可以知道nums[0] = 0,nums[1] = 1,nums[2] = 2,nums[3] = 4；
>
> 如题是排序的，所以，如果nums[mid] == mid时，这个空缺的位置就在右边，否则左边，故可用二分

```java
class Solution {
    public int getMissingNumber(int[] nums) {
        int n = nums.length;
        if(n == 0)return 0;
        int l = 0, r = n - 1;
        while(l < r){
            int mid = l + r >>> 1;
            if(nums[mid] != mid)r = mid;
            else l = mid + 1;
        }
        if(nums[r] == r)r++;
        return r;
    }
}
```

## 69. 数组中数值和下标相等的元素

假设一个单调递增的数组里的每个元素都是整数并且是唯一的。

请编程实现一个函数找出数组中任意一个数值等于其下标的元素。

例如，在数组[-3, -1, 1, 3, 5]中，数字3和它的下标相等。

#### 样例

```
输入：[-3, -1, 1, 3, 5]

输出：3
```

**注意**:如果不存在，则返回-1。

> 二分 ------- 看到排序的可以想想二分

```java
class Solution {
    public int getNumberSameAsIndex(int[] nums) {
        int l = 0, r = nums.length - 1;
        while(l < r){
            int mid = l + r >>> 1;
            if(nums[mid] < mid) l = mid + 1;
            else r = mid;
        }
        if(nums[r] == r)return r;
        return -1;
    }
}
```

## 70.二叉搜索树的第k个结点

给定一棵二叉搜索树，请找出其中的第k小的结点。

你可以假设树和k都存在，并且1≤k≤树的总结点数。

#### 样例

```
输入：root = [2, 1, 3, null, null, null, null] ，k = 3

    2
   / \
  1   3

输出：3
```

> 二叉搜索树，中序遍历就是排序的

```java
class Solution {
    int k,step = 0;
    TreeNode res;
    public TreeNode kthNode(TreeNode root, int kk) {
        k = kk;
        helper(root);
        return res;
    }
    public void helper(TreeNode root){
        if(root == null || step > k) return ;
        helper(root.left);
        step++;
        if(step == k)res = root;
        helper(root.right);
    }
}
```

## 71.二叉树的深度

输入一棵二叉树的根结点，求该树的深度。

从根结点到叶结点依次经过的结点（含根、叶结点）形成树的一条路径，最长路径的长度为树的深度。

#### 样例

```
输入：二叉树[8, 12, 2, null, null, 6, 4, null, null, null, null]如下图所示：
    8
   / \
  12  2
     / \
    6   4

输出：3
```

> 遍历即可

```java'
class Solution {
    public int treeDepth(TreeNode root) {
        return deep(root);
    }
    public int deep(TreeNode root){
        if(root == null)return 0;
        return Math.max(deep(root.left),deep(root.right)) + 1;
    }
}
```

## 72. 平衡二叉树

输入一棵二叉树的根结点，判断该树是不是平衡二叉树。

如果某二叉树中任意结点的左右子树的深度相差不超过1，那么它就是一棵平衡二叉树。

**注意：**

- 规定空树也是一棵平衡二叉树。

#### 样例

```
输入：二叉树[5,7,11,null,null,12,9,null,null,null,null]如下所示，
    5
   / \
  7  11
    /  \
   12   9

输出：true
```

> 写一个判断深度的方法deep()
>
> 然后判断其左子树和右子树的差是否小于1并且递归处理左子树，右子树。
>
> 只有所有的节点都满足才返回false；

```java
class Solution {
    public boolean isBalanced(TreeNode root) {
        if(root == null)return true;
        return Math.abs(deep(root.left) - deep(root.right)) <= 1 && isBalanced(root.left) && isBalanced(root.right);
    }
    public int deep(TreeNode root){
        if(root == null)return 0;
        return Math.max(deep(root.left),deep(root.right)) + 1;
    }
}
```

> 递归到底，如果左子树和右子树差大于1 则返回-1，否则返回高度

```java
class Solution {
    public boolean isBalanced(TreeNode root) {
        return deep(root) == -1 ? false : true;
    }
    public int deep(TreeNode root){
        if(root == null)return 0;
        int left = deep(root.left);
        if(left == -1)return -1;
        int right = deep(root.right);
        if(right == -1)return -1;
        return Math.abs(left - right) > 1 ? -1 : Math.max(left,right) + 1;
    }
}
```



## 73. 数组中只出现一次的两个数字

一个整型数组里除了两个数字之外，其他的数字都出现了两次。

请写程序找出这两个只出现一次的数字。

你可以假设这两个数字一定存在。

#### 样例

```
输入：[1,2,3,3,4,4]

输出：[1,2]
```

> 数组中只有两个数字出现一次，其余数字都出现两次。
>
> 根据 a ^ a = 0得，如果将数组所有数字做异或即得到 此唯独出现一次的两个数字的异或；
>
> 然后根据位 进行分组，即可；

```java
class Solution {
    public int[] findNumsAppearOnce(int[] nums) {
        int res = 0;
        for(int num : nums)res ^= num;
        res &= (-res);
        
        int[] ans = new int[2];
        for(int i : nums){
            if((i & res) == res) ans[0] ^= i;
            else ans[1] ^= i;
        }
        return ans;
    }
}
```

## 74. 数组中唯一只出现一次的数字

在一个数组中除了一个数字只出现一次之外，其他数字都出现了三次。

请找出那个只出现一次的数字。

你可以假设满足条件的数字一定存在。

**思考题：**

- 如果要求只使用 O(n)O(n) 的时间和额外 O(1)O(1) 的空间，该怎么做呢？

#### 样例

```
输入：[1,1,1,2,2,2,3,4,4,4]

输出：3
```

> 方法一：将nums数组中所有数分为不同的位数去看，一个数出现一次，其余全部出现三次，其它数字在它的位上出现的次数一定是3此；
>
> 比如： 3 --- 11,那么 3在 第0位和第1位一定出现3次
>
> 而         5---   101 ，那么 最后一位出现 6次，中间出现3次，首位出现3次； 如果有个数只出现一次，则打破此平衡，不再是3的倍数

```java
class Solution {
    public int findNumberAppearingOnce(int[] nums) {
        // 记录 每一位 上 出现1 的个数 
        int[] count = new int[32];
        for(int num : nums){
            for(int i = 0; i < 32; i ++){
                count[i] += num & 1;
                num >>>= 1;
            }
        }
        int res = 0, m = 3;
        // 将出现次数不是3的整倍数的位上 取为1；
        for(int i = 0; i < 32; i ++){
            res <<= 1;
            // 如果 出现 4 或者 1 次 等，则 res的此为 为1；
            res |= count[31 - i] % m;
        }
        return res;
    }
}
```

## 75. 和为S的两个数字

输入一个数组和一个数字s，在数组中查找两个数，使得它们的和正好是s。

如果有多对数字的和等于s，输出任意一对即可。

你可以认为每组输入中都至少含有一组满足条件的输出。

#### 样例

```
输入：[1,2,3,4] , sum=7

输出：[3,4]
```

> 利用ma存每个数值需要的数

```java
class Solution {
    public int[] findNumbersWithSum(int[] nums, int target) {
        Map<Integer,Integer> map = new HashMap<>();
        for(int num : nums){
            int need = target - num;
            Integer temp = map.get(need);
            if(temp == null)map.put(num,need);
            else return new int[] {need,temp};
        }
        return null;
    }
}
```

## 76. 和为S的连续正数序列

输入一个正数s，打印出所有和为s的连续正数序列（至少含有两个数）。

例如输入15，由于1+2+3+4+5=4+5+6=7+8=15，所以结果打印出3个连续序列1～5、4～6和7～8。

#### 样例

```
输入：15

输出：[[1,2,3,4,5],[4,5,6],[7,8]]
```

> 双指针

```java
class Solution {
    public List<List<Integer> > findContinuousSequence(int sum) {
       int i = 1, total = 0;
       List<List<Integer>> res = new ArrayList<>();
       List<Integer> temp = new ArrayList<>();
       for(int k = 1; k < sum; k ++){
           total += k;
           temp.add(k);
           while(total > sum){
               temp.remove((Integer)i);
               total -= i;
               i++;
           }
           if(total == sum)res.add(new ArrayList<>(temp));
       }
       return res;
    }
}
```

## 77. 翻转单词顺序

输入一个英文句子，翻转句子中单词的顺序，但单词内字符的顺序不变。

为简单起见，标点符号和普通字母一样处理。

例如输入字符串`"I am a student."`，则输出`"student. a am I"`。

#### 样例

```
输入："I am a student."

输出："student. a am I"
```

```java
class Solution {
    public String reverseWords(String s) {
        String[] str = s.trim().split(" ");
        StringBuilder sb = new StringBuilder();
        for(int i = str.length - 1; i > 0; i--){
            if(str[i].trim().length() == 0) continue;
            sb.append(str[i].trim()).append(" ");
        }
        sb.append(str[0].trim());
        return sb.toString();
    }
}
```

## 78. 左旋转字符串

字符串的左旋转操作是把字符串前面的若干个字符转移到字符串的尾部。

请定义一个函数实现字符串左旋转操作的功能。

比如输入字符串`"abcdefg"`和数字2，该函数将返回左旋转2位得到的结果`"cdefgab"`。

**注意：**

- 数据保证n小于等于输入字符串的长度。

#### 样例

```
输入："abcdefg" , n=2

输出："cdefgab"
```

> 直接string

```java
class Solution {
    public String leftRotateString(String str,int n) {
        String temp = str.substring(0,n),res = str.substring(n);
        res += temp;
        return res;
    }
}
```

