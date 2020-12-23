## leetcode-100.相同的树

给定两个二叉树，编写一个函数来检验它们是否相同。

如果两个树在结构上相同，并且节点具有相同的值，则认为它们是相同的。

示例 1:

**输入:**

>  ​       1         1
>    ​       / \       / \
>    ​      2   3     2   3
>
>   [1,2,3],   [1,2,3]

**输出:** true

示例 2:

**输入:** 

> ​     1         1
>    ​    /           \
>       2             2
> [1,2],     [1,null,2]

**输出:** false

示例 3:

**输入:**

> 1         1
     / \       / \

      2   1     1   2
[1,2,1],   [1,1,2]

**输出:** false

二叉树的递归遍历。

```java
class Solution {
    public boolean isSameTree(TreeNode p, TreeNode q) {
        if(p == null && q == null)return true;
        if(p == null || q == null)return false;
        if(p.val != q.val)return false;
        return isSameTree(p.left,q.left) && isSameTree(p.right,q.right);
    }
}
```

## `leetcode`-94.二叉树的中序遍历

给定一个二叉树，返回它的中序 遍历。

示例:

> 输入: [1,null,2,3]
>    1
>     \
>      2
>     /
>    3
输出: [1,3,2]

进阶: 递归算法很简单，你可以通过迭代算法完成吗？

递归

```java
class Solution {
    List<Integer> list = new ArrayList<>();
    public List<Integer> inorderTraversal(TreeNode root) {
        middle(root,list);
        return list;
    }

    public static void middle(TreeNode root,List list){
        if(root != null){
            if(root.left != null) middle(root.left,list);
            list.add(root.val);
            if(root.right != null) middle(root.right,list);
        }
    }
}
```

迭代:

```java
class Solution {
    public List<Integer> inorderTraversal(TreeNode root) {
        List<Integer> list = new ArrayList<>();
        Stack<TreeNode> stack = new Stack<>();
        
        while(root != null || !stack.isEmpty()){
            //递归左子树
            while(root != null){
                stack.push(root);
                root = root.left;
            }
            //拿出左边的叶子节点
            root = stack.pop();
            list.add(root.val);
            //判断其右边有没有节点
            root = root.right;
        }
        return list;
    }
}
```

## `leetcode`-104.二叉树的最大深度

给定一个二叉树，找出其最大深度。

二叉树的深度为根节点到最远叶子节点的最长路径上的节点数。

说明: 叶子节点是指没有子节点的节点。

示例：
给定二叉树 [3,9,20,null,null,15,7]，

​    3

   / \
  9  20
    /  \
   15   7
返回它的最大深度 3 。

递归，取最大值即可。

```java
class Solution {
    public int maxDepth(TreeNode root) {
        if(root == null)return 0;

        int ans = deep(root,0);
        return ans;
    }
    public static int deep(TreeNode root,int step){
        if(root == null)return step;
        return Math.max(deep(root.left,step+1),deep(root.right,step+1));
    }   
}
```

## 543.二叉树的直径

给定一棵二叉树，你需要计算它的直径长度。一棵二叉树的直径长度是任意两个结点路径长度中的最大值。这条路径可能穿过根结点。

示例 :
给定二叉树

          1
         / \
        2   3
       / \     
      4   5    
返回 3, 它的长度是路径 [4,2,1,3] 或者 [5,2,1,3]。



其实就相当于求最长的那个路径：也就是左儿子的长度+又儿子的长度的最大值；遍历求出每一个子树的左右结点，取最大值，即为答案；

```java
class Solution {
    static int ans;
    public int diameterOfBinaryTree(TreeNode root) {
        if(root == null)return 0;
        ans = 1;
        deep(root);
        return ans-1;
    }

    private static int deep(TreeNode root){
        if(root == null)return 0;
        int L = deep(root.left);
        int R = deep(root.right);
        ans = Math.max(L+R+1,ans); // 当以此root为根节点时，他的路径长度为L+R,与保存的最大直径ans相比较
        return Math.max(L,R)+1; // 要返回时，代表其不为根节点，只能取一个长的子树。
    }
}
```

## 101.对称二叉树

给定一个二叉树，检查它是否是镜像对称的。

例如，二叉树 [1,2,2,3,4,4,3] 是对称的。

    	1
       / \
      2   2
     / \ / \
    3  4 4  3

但是下面这个 [1,2,2,null,3,null,3] 则不是镜像对称的:

> 1
>    / \
>   2   2
>    \   \
>    3    3

判断其是否对称，也就是判断其左子树，和右子树是否相同-->（判断两棵树是否相同）故题目转换为leetcode:100题。

```java
class Solution {
    public boolean isSymmetric(TreeNode root) {
        if(root == null)return true;
        return isSame(root.left,root.right);
    }
    public static boolean isSame(TreeNode root1,TreeNode root2){
        if(root1 == null && root2 == null)return true;
        if(root1 == null || root2 == null)return false;
        if(root1.val != root2.val)return false;
        //左子树和右子树 相比    右子树和左子树相比
        return isSame(root1.left,root2.right) && isSame(root1.right,root2.left);
    }
}
```



## 107. 二叉树的层次遍历 II

给定一个二叉树，返回其节点值自底向上的层次遍历。 （即按从叶子节点所在层到根节点所在的层，逐层从左向右遍历）

例如：
给定二叉树 [3,9,20,null,null,15,7],

       3
      / \
      9  20
        /  \
       15   7

返回其自底向上的层次遍历为：

[
  [15,7],
  [9,20],
  [3]
]

dfs: 将相同层数的值放入，最后反转即可。

```java
class Solution {
    List<List<Integer>> list = new ArrayList<>();
    public List<List<Integer>> levelOrderBottom(TreeNode root) {
        if(root == null)return list;
        dfs(root,0);
        Collections.reverse(list);
        return list;
    }

    private void dfs(TreeNode root,int level){
        if(root == null) return ;
        if(list.size() <= level){
            list.add(level,new ArrayList<>());
        }
        list.get(level).add(root.val);
        dfs(root.left,level+1);
        dfs(root.right,level+1);
    }
}
```

bfs:每次讲所有的顶点放入，层次遍历；

```java
class Solution {
    List<List<Integer>> list = new ArrayList<>();
    public List<List<Integer>> levelOrderBottom(TreeNode root) {
        if(root == null)return list;
        Queue<TreeNode> queue = new LinkedList<>();
        queue.offer(root);
        while(!queue.isEmpty()){
            List<Integer> list1 = new ArrayList<>();
            int size = queue.size();
            for(int i = 0; i < size; i++){
                TreeNode temp = queue.poll();
                list1.add(temp.val);
                if(temp.left != null) queue.offer(temp.left);
                if(temp.right != null) queue.offer(temp.right);
            }
            list.add(list1);
        }
        Collections.reverse(list);
        return list;
    }
}
```



## 108 将有序数组转换为二叉搜索树

将一个按照升序排列的有序数组，转换为一棵高度平衡二叉搜索树。

本题中，一个高度平衡二叉树是指一个二叉树每个节点 的左右两个子树的高度差的绝对值不超过 1。

示例:

给定有序数组: [-10,-3,0,5,9],

一个可能的答案是：[0,-3,9,-10,null,5]，它可以表示下面这个高度平衡二叉搜索树：

           0
          / \
        -3   9
       /   /
     -10  5

由题意得该数组是有序的，所以直接选取中间的节点当做根节点，就会是B树

```java
class Solution {
    int nums[];
    public TreeNode helper(int left,int right){
        if(left > right) return null;
        int p = (left + right) / 2;
        TreeNode root = new TreeNode(nums[p]);
        root.left = helper(left,p - 1);
        root.right = helper(p + 1, right);
        return root;
    }

    public TreeNode sortedArrayToBST(int[] nums) {
        this.nums = nums;
        return helper(0, nums.length - 1);
    }
}
```



## 110.平衡二叉树

给定一个二叉树，判断它是否是高度平衡的二叉树。

本题中，一棵高度平衡二叉树定义为：

一个二叉树每个节点 的左右两个子树的高度差的绝对值不超过1。

示例 1:

给定二叉树 [3,9,20,null,null,15,7]

    	3
       / \
      9  20
        /  \
       15   7

返回 true 。

示例 2:

给定二叉树 [1,2,2,3,3,null,null,4,4]

       1
      / \
     2   2
    / \
    3   3
    / \
    4   4


ps:判断其两颗子树的高度相差是否大于1，然后再判断其左右子节点的两颗子树的高度差是否大于1；

```java
class Solution {

    public boolean isBalanced(TreeNode root) {
        if(root == null)return true;
        return Math.abs(depth(root.left) - depth(root.right)) <= 1 && isBalanced(root.left) && isBalanced(root.right);
    }

    public static int depth(TreeNode root){
        if(root == null)return 0;
        return Math.max(depth(root.left),depth(root.right)) + 1;
    }
}
```

先递归到底，自底向上判断；

```java
class Solution {

    public boolean isBalanced(TreeNode root) {
        if(root == null)return true;
        return self(root) ==  -1 ? false : true;
    }

    //自底向上判断，先递归到底，然后再判断是否符合平衡二叉树，若不符合直接return，符合则对 深度+1 继续判断。
    public static int self(TreeNode root){
        if(root == null)return 0;
        int left =self(root.left);
        if(left == -1)return -1; //剪枝，如果其子树差已经大于1了 直接返回-1
        int right =  self(root.right);
        if(right == -1)return -1;//剪枝，如果其子树差已经大于1了 直接返回-1
        return Math.abs(left-right) <= 1 ? Math.max(left,right) + 1 : -1;
    }
}
```

## leetcode-92.验证二叉搜索树

给定一个二叉树，判断其是否是一个有效的二叉搜索树。

假设一个二叉搜索树具有如下特征：

节点的左子树只包含小于当前节点的数。
节点的右子树只包含大于当前节点的数。
所有左子树和右子树自身必须也是二叉搜索树。
示例 1:

输入:
    2
   / \
  1   3
输出: true
示例 2:

输入:
    5
   / \
  1   4
     / \
    3   6
输出: false
解释: 输入为: [5,1,4,null,null,3,6]。
     根节点的值为 5 ，但是其右子节点值为 4 。

```java
    5
   / \
  1   7
     / \
    6   8
对于6这个结点，必须比7小，比5大。故root.val的范围要在(lower,upper)中。

class Solution {
    public boolean isValidBST(TreeNode root) {
        return helper(root,null,null);
    }
    public boolean helper(TreeNode root,Integer lower, Integer upper){
        if(root == null)return true;
        int val = root.val;
        if(lower != null && val <= lower)return false;
        if(upper != null && val >= upper)return false;

        if(!helper(root.left,lower,val)) return false;
        if(!helper(root.right,val,upper))return false;
        return true;
    }
}
```

中序遍历求解: 对于BST树，中序遍历后，值都为升序。

```java
class Solution {
    Integer pre = null;
    public boolean isValidBST(TreeNode root) {
        return helper(root);
    }
    public boolean helper(TreeNode root){
        if(root == null)return true;
        if(!helper(root.left)) return false;
        if(pre != null && root.val <= pre)return false;
        pre = root.val;
        if(!helper(root.right)) return false;
        return true;
    }
}
```

中序遍历栈求解: 对于BST树，中序遍历后，值都为升序。

```java
class Solution {
    public boolean isValidBST(TreeNode root) {
        Stack<TreeNode> stack = new Stack<>();
        Integer pre = null;
        while(root != null || !stack.isEmpty()){
            while(root != null){
                stack.push(root);
                root = root.left;
            }
            root = stack.pop();
            if(pre != null && pre >= root.val)return false;
            pre = root.val;
            root = root.right;
        }
        return true;
    }
}
```

## leetcode-572.另一个子树

给定两个非空二叉树 s 和 t，检验 s 中是否包含和 t 具有相同结构和节点值的子树。s 的一个子树包括 s 的一个节点和这个节点的所有子孙。s 也可以看做它自身的一棵子树。

示例 1:
给定的树 s:

     3
    / \
   4   5
  / \
 1   2
给定的树 t：

   4 
  / \
 1   2
返回 true，因为 t 与 s 的一个子树拥有相同的结构和节点值。

示例 2:
给定的树 s：

     3
    / \
   4   5
  / \
 1   2
    /
   0
给定的树 t：

   4
  / \
 1   2
返回 false。

> 递归处理

```java
class Solution {
    public boolean isSubtree(TreeNode s, TreeNode t) {
        if(s == null) return false;
        if(helper(s,t))return true;
        else return isSubtree(s.left,t) || isSubtree(s.right,t);   
    }
    public boolean helper(TreeNode s,TreeNode t){
        if(s == null && t == null)return true;
        if(s == null || t == null)return false;
        if(s.val != t.val)return false;
        else return helper(s.left,t.left) && helper(s.right,t.right);
    }
}
```

## leetcode-236.二叉树的最近公共祖先

![image-20200512000159917](C:\Users\Administrator\AppData\Roaming\Typora\typora-user-images\image-20200512000159917.png)

```java
// 递归插叙左子树和右子树，当左子树为空，则返回右子树，（肯定在右子树）反之同理。如果都不为空，则当前的root必定是它们的最近祖先。

public TreeNode lowestCommonAncestor(TreeNode root, TreeNode p, TreeNode q) {
    if(root == null || root == p || root == q)return root;
    TreeNode left = lowestCommonAncestor(root.left,p,q);
    TreeNode right = lowestCommonAncestor(root.right,p,q);
    if(left == null)return right;
    if(right == null)return left;
    return root;
}
```

