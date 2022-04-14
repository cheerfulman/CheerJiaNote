## 46.二叉搜索树的后序遍历序列

输入一个整数数组，判断该数组是不是某二叉搜索树的后序遍历的结果。

如果是则返回true，否则返回false。

假设输入的数组的任意两个数字都互不相同。

#### 样例

```
输入：[4, 8, 6, 12, 16, 14, 10]

输出：true
```

> 后续遍历，故最后一个节点一定是根节点，而中间一定从某一个位置分开，其左边全部为左子树，右边全部为右子树，只要判断左子树全部比根小，右子树比根大递归下去，看是否满足，如果找不到这个点，则返回false；

```java
class Solution {
    int[] seq;
    public boolean verifySequenceOfBST(int [] sequence) {
        seq = sequence;
        return dfs(0,seq.length - 1);
    }
    public boolean dfs(int l, int r){
        if(l >= r)return true;
        int root = seq[r],k = 0;
        while(k < r && seq[k] < root)k++;// 都比根小的为左子树
        for(int i = k; i < r; i ++) // 右子树是不是都比根大，是的话继续，否则false；
            if(seq[i] < root)return false;
        return dfs(l,k - 1) && dfs(k,r - 1);
    }
}
```

> 二叉搜索树：故中序遍历为升序
>
> 可以利用中序遍历，和后序遍历，当两个遍历中左子树和右子树个数不同时，返回false；

```java
class Solution {
    int[] seq1,seq2;// seq1--中序遍历，seq2 -- 后序遍历
    Map<Integer,Integer> map = new HashMap<>();
    public boolean verifySequenceOfBST(int [] sequence) {
        seq2 = sequence.clone();
        Arrays.sort(sequence);
        seq1 = sequence;
        for(int i = 0; i < seq1.length; i ++)
            map.put(seq1[i],i);
        return dfs(0,seq1.length - 1,0,seq1.length - 1);
    }
    public boolean dfs(int l1, int r1,int l2, int r2){
        if(r1 - l1 != r2 - l2)return false;
        if(l2 >= r2 || l1 >= r1)return true;
        int root = seq2[r2],k = map.get(root),i = 0;
        while(i < r2 && seq2[i] < root)i++;// 都比根小的为左子树
        return dfs(l1,k - 1,l2,i-1) && dfs(k+1,r1,i,r2 - 1);
    }
}
```

## 47. 二叉树中和为某一值的路径

输入一棵二叉树和一个整数，打印出二叉树中结点值的和为输入整数的所有路径。

**从树的根结点开始往下一直到叶结点所经过的结点形成一条路径。**

#### 样例

```
给出二叉树如下所示，并给出num=22。
      5
     / \
    4   6
   /   / \
  12  13  6
 /  \    / \
9    1  5   1

输出：[[5,4,12,1],[5,6,6,5]]
```

> 没看见加粗字体，人都搞傻了。
>
> 就是先序遍历，求解即可

```java
class Solution {
    List<List<Integer>> res = new ArrayList<>();
    int total;
    public List<List<Integer>> findPath(TreeNode root, int sum) {
        if(root == null)return res;
        total = sum;
        Integer[] temp = new Integer[10050];
        dfs(root,temp,root.val,1);
        return res;
    }
    public void dfs(TreeNode root,Integer[] temp,int t,int step){
        if(t > total)return ;
        temp[step] = root.val;
        if(root.left == null && root.right == null && t == total){
            List<Integer> tt = new ArrayList<>();
            for(int i = 1; i <= step; i ++)tt.add(temp[i]);
            res.add(tt);
        }
        if(root.left != null)dfs(root.left,temp,t+root.left.val,step + 1);
        if(root.right != null)dfs(root.right,temp,t+root.right.val,step + 1);
    }
}
```

> 一个是数组模式，一个是List模式

```java
class Solution {
    List<List<Integer>> ans = new ArrayList<>();
    List<Integer> path = new ArrayList<>();

    public List<List<Integer>> findPath(TreeNode root, int sum) {
        dfs(root, sum);
        return ans;
    }

    public void dfs(TreeNode root, int sum){
        if(root == null) return;
        path.add(root.val);
        sum -= root.val;
        if(root.left == null && root.right == null && sum == 0){
            //引用调用，需要复制path内容，不然存在ans中的为引用
            List <Integer> tmp = new ArrayList <>();
            tmp.addAll(path);
            ans.add(tmp);
        }
        dfs(root.left, sum);
        dfs(root.right, sum);
        path.remove(path.size() - 1);
    } 
}
```

## 48.复杂链表的复刻

请实现一个函数可以复制一个复杂链表。

在复杂链表中，每个结点除了有一个指针指向下一个结点外，还有一个额外的指针指向链表中的任意结点或者null。

**注意**：

- 函数结束后原链表要与输入时保持一致。

> HashMap

```java
class Solution {
    public ListNode copyRandomList(ListNode head) {
        Map<ListNode,ListNode> map = new HashMap<>();
        ListNode cur = head,res;
        while(cur != null){
            map.put(cur,new ListNode(cur.val));
            cur = cur.next;
        }
        for (ListNode key: map.keySet()) {
            ListNode value = map.get(key);
            value.next = map.get(key.next);
            value.random = map.get(key.random);
        }
        return map.get(head);
    }
}
```

> 复制出新链表

```java
class Solution {
    public ListNode copyRandomList(ListNode head) {
        for(ListNode p = head;p != null;){
            ListNode np = new ListNode(p.val),next = p.next;
            p.next = np;
            np.next = next;
            p = next;
        }
        for (ListNode p = head; p != null; p = p.next.next)
        {
            if (p.random != null)
                p.next.random = p.random.next;
        }
        
        ListNode dummy = new ListNode(-1);
        ListNode cur = dummy;
        for (ListNode p = head; p != null; p = p.next)
        {
            cur.next = p.next;
            cur = cur.next;
            p.next = p.next.next;
        }
        
        return dummy.next;
    }
}
```

## 49.二叉搜索树与双向链表

输入一棵二叉搜索树，将该二叉搜索树转换成一个排序的双向链表。

要求不能创建任何新的结点，只能调整树中结点指针的指向。

**注意**：

- 需要返回双向链表最左侧的节点。

例如，输入下图中左边的二叉搜索树，则输出右边的排序双向链表。

![](https://www.acwing.com/media/article/image/2018/12/02/19_23bee494f5-QQ截图20181202052830.png)

> 我们知道二叉搜索树的中序遍历是有序的，故我们可以利用中序遍历，构造出排序的双向链表
>
> 利用pre记录前一个节点，然后串起来；

```java
class Solution {
    TreeNode pre = null;
    public TreeNode convert(TreeNode root) {
        if(root == null)return root;
        dfs(root);
        while(root != null && root.left != null) root = root.left;
        return root;
    }
    public void dfs(TreeNode root){
        if(root == null)return ;
        dfs(root.left);
        root.left = pre;
        if(pre != null)pre.right = root;
        pre = root;
        dfs(root.right);
    }
}
```

## 50.序列化二叉树

请实现两个函数，分别用来序列化和反序列化二叉树。

您需要确保二叉树可以序列化为字符串，并且可以将此字符串反序列化为原始树结构。

#### 样例

```
你可以序列化如下的二叉树
    8
   / \
  12  2
     / \
    6   4

为："[8, 12, 2, null, null, 6, 4, null, null, null, null]"
```

**注意**:

- 以上的格式是AcWing序列化二叉树的方式，你不必一定按照此格式，所以可以设计出一些新的构造方式。

> 序列化方式随意，这里采用前序遍历。

```java
class Solution {
    String res = "";
    int u = 0;
    // Encodes a tree to a single string.
    String serialize(TreeNode root) {
        dfs_s(root);
        return res;
    }
    
    void dfs_s(TreeNode root){
        if(root == null){res += "null ";return ;}
        res += root.val + " ";
        dfs_s(root.left);
        dfs_s(root.right);
    }

    // Decodes your encoded data to tree.
    TreeNode deserialize(String data) {
        return dfs_d(data.toCharArray());
    }
    
    TreeNode dfs_d(char[] data){
        if(u == data.length)return null;
        int k = u;
        while(k < data.length && data[k] != ' ')k++;
        if(data[u] == 'n'){u = k + 1; return null;}
        int val = 0,sign = 1;
        if(data[u] == '-'){sign = -1;u++;}
        for(int i = u; i < k; i++)val = val * 10 + data[i] - '0';
        val *= sign;
        TreeNode root = new TreeNode(val);
        u = k + 1;
        root.left = dfs_d(data);
        root.right = dfs_d(data);
        return root;
    }
}

```

## 51.数字排列

输入一组数字（可能包含重复数字），输出其所有的排列方式。

#### 样例

```
输入：[1,2,3]

输出：
      [
        [1,2,3],
        [1,3,2],
        [2,1,3],
        [2,3,1],
        [3,1,2],
        [3,2,1]
      ]
```

> 先排序，将重复的元素放一起，然后判断

```java
class Solution {
    boolean[] vis;
    List<List<Integer>> res;
    int[] nums;
    public List<List<Integer>> permutation(int[] nu) {
        nums = nu;
        vis = new boolean[nums.length];
        res = new ArrayList<>();
        Arrays.sort(nums);
        dfs(nums.length,new ArrayList());
        return res;
    }
    public void dfs(int n,ArrayList temp){
        if(temp.size() > n)return ;
        if(temp.size() == n){
            ArrayList<Integer> t = new ArrayList<>();
            t.addAll(temp);
            res.add(t);
            return ;
        }
        for(int i = 0; i < n; i ++){
            if (vis[i] || i > 0 && nums[i-1] == nums[i] && !vis[i-1]) continue;
            temp.add(nums[i]);
            vis[i] = true;
            dfs(n,temp);
            vis[i] = false;
            temp.remove(temp.size() - 1);
            
        }
    }
}
```

## 52.数组中出现次数超过一半的数字

数组中有一个数字出现的次数超过数组长度的一半，请找出这个数字。

假设数组非空，并且一定存在满足条件的数字。

**思考题**：

- 假设要求只能使用 O(n)的时间和额外 O(1) 的空间，该怎么做呢？

#### 样例

```
输入：[1,2,1,1,3]

输出：1
```

> 由于某个数值一定超过一半
>
> 故其出现的次数一定大于等于其他元素出现的次数
>
> 利用cnt记录某数值出现的次数。

```java
class Solution {
    public int moreThanHalfNum_Solution(int[] nums) {
        int cnt = 0,res = nums[0];
        for(int num : nums){
            if(num == res)cnt ++;
            else{
                if(cnt == 0){cnt ++;res = num;}
                else cnt --;
            }
        }
        return res;
    }
}
```

## 53. 最小的k个数

输入n个整数，找出其中最小的k个数。

**注意：**

- 数据保证k一定小于等于输入数组的长度;
- 输出数组内元素请按从小到大顺序排序;

#### 样例

```
输入：[1,2,3,4,5,6,7,8] , k=4

输出：[1,2,3,4]
```

> 利用大根堆，堆中存k个数，如果堆中个数大于k个，则poll最大的元素（因为最大的元素，可能是k+1小的数了）

```java
class Solution {
    public List<Integer> getLeastNumbers_Solution(int [] input, int k) {
        Queue<Integer> q = new PriorityQueue<>((x,y) -> y - x);
        for(int x : input){
            if(q.size() < k || q.peek() > x) q.offer(x);
            if(q.size() > k) q.poll();
        }
        List<Integer> res = new ArrayList<>();
        while(!q.isEmpty())res.add(q.poll());
        Collections.reverse(res);
        return res;
    }
    
    
}
```

> 利用快排，当i==j位于第k个位置时，代表此数就是第k小的数；
>
> 否则判断j在k的左边还是右边

```java
class Solution {
    public List<Integer> getLeastNumbers_Solution(int [] input, int k) {
        List<Integer> res = new ArrayList<>();
        for(int i = 1;i <= k;i++)
            res.add(quick_sort(input,0,input.length-1,i));
        return res;
    }
    int quick_sort(int[] q, int l, int r, int k){
        if(l >= r)return q[l];
        int i = l - 1, j = r + 1, x = q[l + r >> 1];
        while(i < j){
            do i ++; while(q[i] < x);
            do j--; while(q[j] > x);
            if(i < j){
                int temp = q[i];
                q[i] = q[j];
                q[j] = temp;
            }
        }
        if(k <= j - l + 1)return quick_sort(q,l,j,k);
        else return quick_sort(q,j + 1,r,k-(j-l+1));
    }
}
```

## 54.数据流中的中位数

如何得到一个数据流中的中位数？

如果从数据流中读出奇数个数值，那么中位数就是所有数值排序之后位于中间的数值。

如果从数据流中读出偶数个数值，那么中位数就是所有数值排序之后中间两个数的平均值。

#### 样例

```
输入：1, 2, 3, 4

输出：1,1.5,2,2.5

解释：每当数据流读入一个数据，就进行一次判断并输出当前的中位数。
```

> 利用大根堆和小根堆，大根堆存小的数，小根堆存大的数

```java
class Solution {
    Queue<Integer> min_heap = new PriorityQueue<>();
    Queue<Integer> max_heap = new PriorityQueue<>((x,y) -> y - x);
    public void insert(Integer num) {
        max_heap.offer(num);
        // 当大根堆数量比小根堆多2时，直接拿一个上去
        if(max_heap.size() > min_heap.size() + 1)min_heap.offer(max_heap.poll());
        // 当大根堆的值比大根堆大时，交换
        if(!max_heap.isEmpty() && !min_heap.isEmpty() && max_heap.peek() > min_heap.peek()){
            Integer maxn = max_heap.poll(), minn = min_heap.poll();
            min_heap.offer(maxn);max_heap.offer(minn);
        }
    }
    public Double getMedian() {
        if((max_heap.size() + min_heap.size() & 1)  == 1)return max_heap.peek() * 1.0;
        else return (max_heap.peek() + min_heap.peek()) / 2.0;
    }
}
```

> // 这样就不会出现 大根堆比小根堆的值大的情况了
>         min_heap.offer(num);
>         max_heap.offer(min_heap.poll());
>
> 省去一个if；

```java
class Solution {
    Queue<Integer> min_heap = new PriorityQueue<>();
    Queue<Integer> max_heap = new PriorityQueue<>((x,y) -> y - x);
    public void insert(Integer num) {
        // 这样就不会出现 大根堆比小根堆的值大的情况了
        min_heap.offer(num);
        max_heap.offer(min_heap.poll());
        
        // 当大根堆数量比小根堆多2时，直接拿一个上去
        if(max_heap.size() > min_heap.size() + 1)min_heap.offer(max_heap.poll());
    }
    public Double getMedian() {
        if((max_heap.size() + min_heap.size() & 1)  == 1)return max_heap.peek() * 1.0;
        else return (max_heap.peek() + min_heap.peek()) / 2.0;
    }
}
```

## 55.连续子数组的最大和

输入一个 **非空** 整型数组，数组里的数可能为正，也可能为负。

数组中一个或连续的多个整数组成一个子数组。

求所有子数组的和的最大值。

要求时间复杂度为O(n)。

#### 样例

```
输入：[1, -2, 3, 10, -4, 7, 2, -5]

输出：18
```

```java
class Solution {
    public int maxSubArray(int[] nums) {
        int res = Integer.MIN_VALUE,cur = 0;
        for(int n : nums){
            if(cur < 0)cur = 0;
            cur += n;
            res = Math.max(res,cur);
        }
        return res;
    }
}
```

## 56.从1到n整数中1出现的次数

输入一个整数n，求从1到n这n个整数的十进制表示中1出现的次数。

例如输入12，从1到12这些整数中包含“1”的数字有1，10，11和12，其中“1”一共出现了5次。

#### 样例

```
输入： 12
输出： 5
```

> 举例 :11230枚举 万位为一的个数，千位为一，百位........
>
> 假设当前在百位上 ——2—— 2的左边有 11，右边有 30，那么百位为一时就有 0~11 * 0~99 （左边两位变换，和2的右边两位变换的个数）所以就是left（左边的数） * t(t为当前是什么位上，百位还是千位等) 
>
> 右边可以变换的数为 0~99 = t；
>
> 如果 当前位置为1，则右边只能变换到0~right了
>
> 例如11130，111—— 1的右边只有0~30,所以是right + 1个
>
> 而11230 则是 111(0~99)都行；

```java
class Solution {
    public int numberOf1Between1AndN_Solution(int n) {
        int res = 0;
        List<Integer> bit = new ArrayList<>();
        while(n > 0){bit.add(n % 10); n /= 10;}
        for(int i = bit.size() - 1; i >= 0;  i--){
            int left = 0, right = 0, t = 1;
            for(int j = bit.size() - 1; j > i; j --)left = left * 10 + bit.get(j);
            for(int j = i - 1; j >= 0; j --){right = right * 10 + bit.get(j);  t *= 10;}

            res += left * t;
            if(bit.get(i) == 1)res += right + 1; // 0 - right
            if(bit.get(i) > 1) res += t;    // 如果 i在百位上则 0 - 99 == t(100);
        }
        return res;
    }
}
```

> 总的来说就是看其左边的数字，和右边的数字

```java
class Solution {
    // 888
    // 个位 : 0 ~ 88; 89
    // 十位 : 9 * 10; 90
    // 百位 : 0 ~ 99; 100
    
    
    // 13106
    // 个位 : 0 ~ 1310; 1311
    // 十位 : (0 ~ 130) * 10 + 131 * 0; 1310;
    // 百位 : 13 * 100 = 1300 + 7 = 1307
    // 千位 : 2000;
    // 万位 : 0 ~ 3106; 3107
    
    
    // abcdef 
    
    // ab * 1000;
    // c == 1, def
    // c > 1; 1000;
    
    public int numberOf1Between1AndN_Solution(int n) {
        int res = 0;
        for(int i = 1; i <= n; i *= 10){
            int a = n / i, b = n % i;
            // (a + 8) / 10 如果这个数 大于 1 则有 （a + 1）* i 个，如果a == 1则是 a * i + 左边的数，a == 0则不加
            // 举例 : 13206 百位时 a = 132, 但是我们要其左边的数字，又想知道百位数字是不是大于1的 故(a + 8) / 10
            // 就是左边的数字 (0 ~ 13) == (a + 1) == 14 然后乘 i（左边的位数）== 14 * 100 = 1400
            // 如果是13106, 则 是(0 ~ 12) == (a) == 13 == 13 * 100 + (b + 1) == 1300 + 7 == 1307;
            res += (a + 8) / 10 * i + ((a % 10) == 1 ? b + 1 : 0);
        }
        return res;
    }
}
```

