## 完全背包变形

## 5399.数位成本和为目标值的最大数字

给你一个整数数组 `cost` 和一个整数 `target` 。请你返回满足如下规则可以得到的 **最大** 整数：

- 给当前结果添加一个数位（`i + 1`）的成本为 `cost[i]` （`cost` 数组下标从 0 开始）。
- 总成本必须恰好等于 `target` 。
- 添加的数位中没有数字 0 。

由于答案可能会很大，请你以字符串形式返回。

如果按照上述要求无法得到任何整数，请你返回 "0" 。

**示例 1：**

```
输入：cost = [4,3,2,5,6,7,2,5,5], target = 9
输出："7772"
解释：添加数位 '7' 的成本为 2 ，添加数位 '2' 的成本为 3 。所以 "7772" 的代价为 2*3+ 3*1 = 9 。 "997" 也是满足要求的数字，但 "7772" 是较大的数字。
 数字     成本
  1  ->   4
  2  ->   3
  3  ->   2
  4  ->   5
  5  ->   6
  6  ->   7
  7  ->   2
  8  ->   5
  9  ->   5
```

**示例 2：**

```
输入：cost = [7,6,5,5,5,6,8,7,8], target = 12
输出："85"
解释：添加数位 '8' 的成本是 7 ，添加数位 '5' 的成本是 5 。"85" 的成本为 7 + 5 = 12 。
```

**示例 3：**

```
输入：cost = [2,4,6,2,4,6,4,4,4], target = 5
输出："0"
解释：总成本是 target 的条件下，无法生成任何整数。
```

**示例 4：**

```
输入：cost = [6,10,15,40,40,40,40,40,40], target = 47
输出："32211"
```

 

**提示：**

- `cost.length == 9`
- `1 <= cost[i] <= 5000`
- `1 <= target <= 5000`

```java
class Solution {
    public String largestNumber(int[] cost, int target) {
        String[][] ans = new String[10][target + 1];
        ans[0][0] = "";
        String res = null;
        for(int i = 1; i < 10; i ++){
            for(int j = cost[i - 1]; j <= target; j ++){
                ans[i][j] = ans[i - 1][j]; // 不取时
                if(cost[i - 1] <= j){
                    if(ans[i - 1][j - cost[i - 1]] != null)//第一次取
                        ans[i][j] = max(ans[i][j],i + ans[i - 1][j - cost[i - 1]]);
                    if(ans[i][j - cost[i - 1]] != null)// 多次取
                        ans[i][j] = max(ans[i][j],i + ans[i][j - cost[i - 1]]);
                }
            }
            res = max(res,ans[i][target]);
        }

        return res == null ? "0" : res;
    }
    public String max(String a,String b){
        if(a == null)return b;
        if(a.length() != b.length())return a.length() > b.length() ? a : b;
        
        for(int i = 0; i < a.length(); i ++){
            if(a.charAt(i) > b.charAt(i))return a;
            else return b;
        }
        return a;
    }
}
```

# 简单动规

## LeetCode - 70.爬楼梯

题目描述：有 N 阶楼梯，每次可以上一阶或者两阶，求有多少种上楼梯的方法。

> 当你在第i层台阶时，你可以从i - 1, i - 2层台阶 过来，故f[i] = f[i - 1] + f[i - 2];

```java
class Solution {
    public int climbStairs(int n) {
        int[] f = new int[n + 1];
        f[0] = 1;
        for(int i = 1; i <= n; i++){
            if(i > 1)f[i] = f[i - 1] + f[i - 2];
            else f[i] = f[i - 1];
        }
        return f[n];
    }
}
```

> 由于当前层数只与前一层，和前两层有关，故可以优化空间为O(1)

```java
class Solution {
    public int climbStairs(int n) {
        int pre = 0, cur = 1;
        for(int i = 1; i <= n; i ++){
            int temp = cur;
            cur = pre + cur;
            pre = temp;
        }
        return cur;
    }
}
```

## LeetCode - 198.大家劫舍

题目描述：抢劫一排住户，但是不能抢邻近的住户，求最大抢劫量。

> 定义数组f(n,2)，f[n]表示抢到第几家，f(n,0)表示第n家没有抢，f(n,1)表示第n家 已经被打劫过了

```java
class Solution {
    public int rob(int[] nums) {
        int n = nums.length;
        if(n == 0)return 0;
        if(n == 1)return nums[0];
        int[][] f = new int[n][2];
        for(int i = 0; i < n; i++){
            if(i == 0)f[i][1] += nums[i];
            if(i == 1){
                f[i][1] = f[i - 1][0] + nums[i];
                f[i][0] = f[i - 1][1];
            }
            if(i >= 2){
                f[i][1] = Math.max(Math.max(f[i - 1][0],f[i - 2][0]),f[i - 2][1]) + nums[i];
                f[i][0] = Math.max(Math.max(Math.max(f[i - 1][0],f[i - 2][0]),f[i - 1][1]),f[i - 2][1]);
            }
        }
        return Math.max(f[n - 1][0],f[n - 1][1]);
    }
}
```

> 可以把第二层状态去掉f[n],表示抢劫到第n家的最大价值
>
> 转移方程为： f[i] = max(f[i - 2] + nums[i], f[i - 1]);

```java
class Solution {
    public int rob(int[] nums) {
        int n = nums.length;
        if(n == 0)return 0;
        int[] f = new int[n];
        for(int i = 0; i < n; i ++){
            if(i == 0)f[i] = nums[0];
            if(i == 1)f[i] = Math.max(nums[0],nums[1]);
            if(i >= 2)f[i] = Math.max(f[i - 2] + nums[i],f[i - 1]);
        }
        return f[n - 1];
    }
}
```

> 由上一题可知，此状态只与前一家，或者前两家有关，所以可以优化空间复杂度为O(1)

```java
class Solution {
    public int rob(int[] nums) {
        int n = nums.length;
        int pre1 = 0, pre2 = 0;
        for(int i = 0; i < n; i ++){
            int cur = Math.max(pre2 + nums[i],pre1);
            pre2 = pre1;
            pre1 = cur;
        }
        return pre1;
    }
}
```

## LeetCode - 213. 打家劫舍 II

题目描述：如上，但是房子排列成环形而已；

> 由于成环，故我们偷了第1家，就不能偷最后一家，不偷第一家，就可以偷最后一家；
>
> 所以我们有两种情况：偷第一家，和不偷第一家，其他情况转换为上一题求解

```java
class Solution {
    public int rob(int[] nums) {
        if (nums == null || nums.length == 0) {
            return 0;
        }
        int n = nums.length;
        if(n == 1)return nums[0];
        return Math.max(robb(nums,1,n),robb(nums,0,n - 1));
    }
    private int robb(int[] nums, int st,int en){
        int pre1 = 0, pre2 = 0;
        for(int i = st; i < en; i ++){
            int cur = Math.max(pre2 + nums[i],pre1);
            pre2 = pre1;
            pre1 = cur;
        }
        return pre1;
    }
}
```

## LeetCode - 64. 最小路径和

题目描述：给定一个包含非负整数的 *m* x *n* 网格，请找出一条从左上角到右下角的路径，使得路径上的数字总和为最小。

> 由于只能从上和左边过来，故方程为f(i,j) = min(f(i - 1,j),f(i, j  - 1)) + grid(i,j);

```java
class Solution {
    public final int MAXN = 0x3f3f3f3f;
    public int minPathSum(int[][] grid) {
        if(grid.length == 0 || grid[0].length == 0)return 0;
        int n = grid.length, m = grid[0].length;
        // f[i][j] 表示，在i,j走到i,j位置最小的花费
        int[][] f = new int[n + 1][m + 1];
        // 将 第0行，和0列置为 inf，不可达
        Arrays.fill(f[0],MAXN);
        for(int i = 0; i <= n; i ++)f[i][0] = MAXN;

        // 规定一个起点， 也就是说 必须从左上角开始出发；
        f[0][1] = 0;

        for(int i = 1; i <= n; i ++)
            for(int j = 1; j <= m; j ++)
                f[i][j] = Math.min(f[i - 1][j],f[i][j - 1]) + grid[i - 1][j - 1];
        return f[n][m];
    }
}
```

> 只跟上面，和左边有关，所以可以简化成一维，dp[j]存储的是上轮的第j个位置，dp[j-1]为左边的位置

```java
class Solution {
    public int minPathSum(int[][] grid) {
        if(grid.length == 0 || grid[0].length == 0)return 0;
        int n = grid.length, m = grid[0].length;
        // f[i][j] 表示，在i,j走到i,j位置最小的花费
        int[] f = new int[m];
        for(int i = 0; i < n; i ++){
            for(int j = 0; j < m; j ++){
                if(j == 0)f[j] = f[j];
                else if(i == 0)f[j] = f[j - 1];
                else f[j] = Math.min(f[j],f[j - 1]);
                f[j] += grid[i][j];
            }
        }
        return f[m - 1];
    }
}
```

## LeetCode - 64. 最小路径和

题目描述：m * n的网格从左上 到 右下角的路线方案数。

> 可以用普通迷宫方法求解，但是会超时

```java
class Solution {
    int res = 0;
    boolean[][] vis;
    public int uniquePaths(int m, int n) {
        vis = new boolean[m][n];
        dfs(0,0,m - 1,n - 1);
        return res;
    }
    public void dfs(int i,int j,int m,int n){
        if(i == m && j == n){res ++; return ;}
        if(i >= 0 && i <= m && j >= 0 && j <= n && !vis[i][j]){
            vis[i][j] = true;
            dfs(i + 1,j,m,n);
            dfs(i,j + 1,m,n);
            vis[i][j] = false;
        }
        return ;
    }
}
```

> 此题是一个dp，可以将大问题，分成很多小问题，所以说你想到达 i,j点，你可以变成先到达i - 1,j 和i , j -1点，从这两个点到i,j点 的方案就是 其两点相加

```java
class Solution {
    public int uniquePaths(int m, int n) {
        int[][] f = new int[m + 1][n + 1];
        f[1][0] = 1;
        for(int i = 1; i <= m; i ++){
            for(int j = 1; j <= n; j ++){
                f[i][j] = f[i - 1][j] + f[i][j - 1];
            }
        }
        return f[m][n];
    }
}
```

> 根据上面的知识，分解成常数空间复杂度

```java
class Solution {
    public int uniquePaths(int m, int n) {
        int[] f = new int[n];
        Arrays.fill(f,1);
        for(int i = 1; i < m; i ++){
            for(int j = 1; j < n; j ++){
                f[j] = f[j] + f[j - 1];
            }
        }
        return f[n - 1];
    }
}
```

> 利用数学排列组合的方法

```java
class Solution {
    public int uniquePaths(int m, int n) {
        int S = m + n - 2;  // 总共的移动次数
        int D = m - 1;      // 向下的移动次数
        long ret = 1;
        for(int i = 1; i <= D; i ++){
            ret = ret * (S - D + i) / i;
        }
        return (int) ret;
    }
}
```

## LeetCode - 303. 区域和检索 - 数组不可变

题目描述：给定一个整数数组  *nums*，求出数组从索引 *i* 到 *j* (*i* ≤ *j*) 范围内元素的总和，包含 *i, j* 两点。

> 就是前缀和

```java
class NumArray {
    int[] sum;
    public NumArray(int[] nums) {
        sum = new int[nums.length + 1];
        for(int i = 1; i <= nums.length; i++){
            sum[i] = sum[i - 1] + nums[i - 1];
        }
    }
    
    public int sumRange(int i, int j) {
        return sum[j + 1] - sum[i];
    }
}
```

## LeetCode - 413.等差数列划分

题目描述：给你一个数组，求改数组又多少个子数组是等差数列，子数组 长度必须大于 2

> 根据我手写 规律发现，等差数组的长度大于3时，子数组为等差数组的数量就 加 len - 3；
>
> 实际情况：f[i] 表示以i结尾的个数
>
> 如：【1，2，3】，【2，3，4】，【1，2，3，4】
>
> 以3 结尾f[3] = 1， 那么f[4]以4结尾 则为在f[3]结尾后均可加上4，f[4] = f[3] + 1;
>
> 而等差数列则是 以3 ~ n 结尾的的都可以，最后循环相加，则是所有的子数组为等差数组的数量

```java
class Solution {
    public int numberOfArithmeticSlices(int[] A) {
        int[] f = new int[A.length];
        for(int i = 2; i < A.length; i ++){
            if(A[i] - A[i - 1] == A[i - 1] - A[i - 2])f[i] = f[i - 1] + 1;
            else f[i] = 0;
        }

        int res = 0;
        for(int num : f)res += num;
        return res;
    }
}
```

## LeetCode - 343.整数拆分

题目描述：将一个整数n，至少拆分成2个数的和，求其拆分后所有数的最大乘积。

> 3 为最优 ---  实验猜测，不会推导

```java
class Solution {
    public int integerBreak(int n) {
        if(n <= 3)return n - 1;
        int s = n / 3;
        if(n % 3 == 0)return pow(3,s);
        if(n % 3 == 1)return pow(3,s - 1) * 4;
        return pow(3,s) * 2;
    }
    public int pow(int a,int n){
        int ans = 1;
        while(n > 0){
            if((n & 1) == 1) ans *= a;
            a *= a;
            n >>= 1;
        }
        return ans;
    }
}
```

> dp

```java
class Solution {
    public int integerBreak(int n) {
        int[] f = new int[n  + 1];
        f[1] = 1;
        for(int i = 2; i <= n; i ++)
            for(int j = 1; j < i; j ++)
                f[i] = Math.max(f[i],Math.max(f[j] * (i - j),j * (i - j)));
        return f[n];
    }
}
```

## LeetCode - 279.完全平方数

题目描述：给定数n，找到若干个平方数之和，使其完全平方数最少。

> 此题和前面的题目一样，后面的数字可以和前面相关。
>
> 如8 可以由 4 + 4， 而dp[4] = 1，可以划分为这种子问题，故可以用dp。
>
> 而递推公式怎么找呢？
>
> 比如13，我们可以找的是 4 + 9， 那么我可以一直寻找完全平方数，也就是j * j；
>
> 故dp[i] = dp[i - j*j] + 1;

```java
class Solution {
    public int numSquares(int n) {
        int[] f = new int[n + 1];
        for(int i = 1; i <= n; i++) f[i] = i;
        for(int i = 1 ; i <= n; i ++){
            for(int j = 1; j * j <= i; j ++){
                f[i] = Math.min(f[i],f[i - j * j] + 1);
            }  
        }
        return f[n];
    }
}
```

## LeetCode - 91.解码方法

题目描述：将一串只包含数字的字符串解码成 字母的方案有多少，1 代表 'A', 26 代表 ’Z'

```java
class Solution {
    public int numDecodings(String s) {
        int n = s.length();
        if(n == 0)return n;
        int[] f = new int[n + 1];
        f[0] = 1;
        char pre = ' ';
        for(int i = 1; i <= n; i++){
            char cur = s.charAt(i - 1);
            if(pre != ' ' && pre != '0'){
                int temp = (pre - '0') * 10 + (cur - '0');
                if(temp >= 1 && temp <= 26)f[i] += f[i - 2];
            }
            if(cur - '0' != 0)f[i] += f[i - 1];
            pre = cur;
        }
        return f[n];
    }
}
```

## LeetCode - 300.最长上升子序列

题目描述：给你一个数组，求最长的上升子序列的长度

> dp[i] 为 第i个数结尾的最长的长度。
>
> 第一遍遍历数组，嵌套子循环j ~ i 当dp[j] < dp[i] 时，则dp[i] = max(dp[i],dp[j] + 1)；
>
> 时间复杂度O(N^2)

```java
class Solution {
    public int lengthOfLIS(int[] nums) {
        if(nums == null || nums.length == 0)return 0;
        int[] f = new int[nums.length];
        Arrays.fill(f,1);
        for(int i = 0; i < nums.length; i ++){
            for(int j = 0; j < i; j ++){
                if(nums[j] < nums[i])f[i] = Math.max(f[i],f[j] + 1);
            }
        }
        int res = 0;
        for(int num : f)res = Math.max(res,num);
        return res;
    }
}
```

> O(NlogN), 尽量选择小的数放在数组末尾。

```java
class Solution {
    public int lengthOfLIS(int[] nums) {
        if(nums == null || nums.length == 0)return 0;
        int[] tail = new int[nums.length];
        int res = 0;
        for(int i = 0; i < nums.length; i++){
            int l = 0, r = res;
            while(l < r){
                int mid = l + r >> 1;
                if(tail[mid] < nums[i]) l = mid + 1;
                else r = mid;
            }
            tail[l] = nums[i];
            if(r == res)res ++;
        }
        return res;
    }
}
```

## LeetCode - 416.分割等和子集

题目描述：给你一串数组，让你分割成两份使其和相等；

> 01背包变形，相当于让你取出其中的物品，使其总和为总值的一半

```java
class Solution {
    public boolean canPartition(int[] nums) {
        if(nums == null || nums.length == 0)return true;
        int total = 0;
        for(int num : nums)total += num;
        if((total & 1) == 1)return false;
        total /= 2;

        boolean[] f = new boolean[total + 1];
        // 能拿到的值为total
        f[0] = true;
        for(int i = 0; i < nums.length; i ++){
            for(int j = total; j >= nums[i]; j --){
                f[j] = f[j] || f[j - nums[i]];
            }
        }
        return f[total];
    }
}
```

## LeetCode - 474.一和零

```text
输入: Array = {"10", "0001", "111001", "1", "0"}, m = 5, n = 3
输出: 4

解释: 总共 4 个字符串可以通过 5 个 0 和 3 个 1 拼出，即 "10","0001","1","0" 。

来源：力扣（LeetCode）
链接：https://leetcode-cn.com/problems/ones-and-zeroes
著作权归领扣网络所有。商业转载请联系官方授权，非商业转载请注明出处。
```

> 相当于01背包，和你能拿的体积v,和重量w，求拿最多的物品题一样。
>
> 此题为你能拿的0的数量为m，1的数量为n，每个物品消耗的0,1不一样，也就是v,w，求能物品的最多数量；

```java
class Solution {
    public int findMaxForm(String[] strs, int m, int n) {
        int[][] f = new int[m + 1][n + 1];
        for(String str : strs){
            int[] ans = get(str);
            int zears = ans[0], ones = ans[1];
            for(int i = m; i >= zears; i --){
                for(int j = n; j >= ones; j --){
                    f[i][j] = Math.max(f[i - zears][j - ones] + 1,f[i][j]);
                }
            }
        }
        return f[m][n];
    }
    public int[] get(String str){
        int[] res = new int[2];
        for(char c : str.toCharArray()){
            res[c - '0'] ++;
        }
        return res;
    }
}
```

## LeetCode - 322.零钱兑换

题目描述：给你一个金额amount 和数组 coins 求使用 数组里最少数量的硬币 组成amount，组成不了返回-1；

```java
class Solution {
    public int coinChange(int[] coins, int amount) {
        if(amount == 0)return 0;
        int[] f = new int[amount + 1];
        for(int i = 1; i <= amount; i ++){
            for(int num : coins){
                if(i < num)continue;
                if(i == num)f[i] = 1;
                else if(f[i] == 0 && f[i - num] != 0)f[i] = f[i - num] + 1;
                else if(f[i - num] != 0)f[i] = Math.min(f[i],f[i - num] + 1);
            }
        }
        return f[amount] == 0 ? -1 : f[amount];
    }
}
```

## LeetCode - 518.零钱兑换 II

题目描述：给你一个金额amount 和数组 coins 求使用 组成amount 的方案数；

```java
class Solution {
    public int change(int amount, int[] coins) {
        int[] f = new int[amount + 1];
        f[0] = 1;
        for(int num : coins){
            for(int i = 1; i <= amount; i ++){
                if(i < num)continue;
                f[i] += f[i - num];
            }
        }
        return f[amount];
    }
}
```

## LeetCode - 139.单词拆分

题目描述：给定一个**非空**字符串 *s* 和一个包含**非空**单词列表的字典 *wordDict*，判定 *s* 是否可以被空格拆分为一个或多个在字典中出现的单词。

```text
输入: s = "applepenapple", wordDict = ["apple", "pen"]
输出: true
解释: 返回 true 因为 "applepenapple" 可以被拆分成 "apple pen apple"。
     注意你可以重复使用字典中的单词。

来源：力扣（LeetCode）
链接：https://leetcode-cn.com/problems/word-break
著作权归领扣网络所有。商业转载请联系官方授权，非商业转载请注明出处。
```

> 思路: 如果从0 ~ j 的字符串在 字典中有，那么j + 1 就可以查看 0 ~ j+1 和 j ~ j + 1在字典中有没有，故我们可以想到每次在字典中可以匹配到时，就记录一个位置j，下次循环遍历各个位置到j + 1 在不在字典中，我们先用List 存 j。

```java
class Solution {
    public boolean wordBreak(String s, List<String> wordDict) {
        if(s == null || s.length() == 0)return false;
        int len = s.length();
        Set<String> set = new HashSet<>();
        // 题目说字典中的单词 只会出现一次，我们利用set 优化查找复杂度为 O(1)
        for(String str : wordDict)set.add(str);
        // 记录 可以匹配到的位置
        List<Integer> list = new ArrayList<>();
        list.add(0);
        for(int i = 0; i <= len ; i++){
            for(int j = 0; j < list.size(); j ++){
                Integer st = list.get(j);
                // 每次 判断是否可以成功
                String str = s.substring(st,i);
                if(set.contains(str))list.add(i);
            }
        }
        boolean res = false;
        for(Integer temp : list)
            if(temp == len)res = true;
        return res;
    }
}
```

> 上面的方法会 内存溢出，因为存的 j 太多了，利用f[j] = true 记录前面可以从哪个位置开始拆分

```java
class Solution {
    public boolean wordBreak(String s, List<String> wordDict) {
        if(s == null || s.length() == 0)return false;
        int len = s.length();
        // 题目说字典中的单词 只会出现一次，我们利用set 优化查找复杂度为 O(1)
        Set<String> set = new HashSet<>();
        for(String str : wordDict)set.add(str);
        
        boolean[] f = new boolean[len + 1];
        f[0] = true;
        for(int i = 0; i <= len ; i++)
            for(int j = 0; j < i; j ++)
                if(f[j] && set.contains(s.substring(j,i)))
                    f[i] = true;

        return f[len];
    }
}


// 做一个剪枝
class Solution {
    public boolean wordBreak(String s, List<String> wordDict) {
        if(s == null || s.length() == 0)return false;
        int len = s.length(),max_word = 0;
        // 题目说字典中的单词 只会出现一次，我们利用set 优化查找复杂度为 O(1)
        Set<String> set = new HashSet<>();
        for(String str : wordDict){
            set.add(str);
            // 记录下最长的单词
            max_word = Math.max(max_word,str.length());
        }
        
        boolean[] f = new boolean[len + 1];
        f[0] = true;
        for(int i = 1; i <= len ; i++)
            for(int j = i - 1; j >= 0 && i - j <= max_word; j --)
                if(f[j] && set.contains(s.substring(j,i))){f[i] = true;break;}
        return f[len];
    }
}
```

## LeetCode - 377.组合总和IV

```text
nums = [1, 2, 3]
target = 4

所有可能的组合为：
(1, 1, 1, 1)
(1, 1, 2)
(1, 2, 1)
(1, 3)
(2, 1, 1)
(2, 2)
(3, 1)

请注意，顺序不同的序列被视作不同的组合。

因此输出为 7。

来源：力扣（LeetCode）
链接：https://leetcode-cn.com/problems/combination-sum-iv
著作权归领扣网络所有。商业转载请联系官方授权，非商业转载请注明出处。
```

> 跟leetcode 518 一样，也就是上上题，都是完全背包，他们都属于背包中的组合问题，要注意的就是顺序；
>
> 518 题中是你拿的硬币数量不一样或者数量一样面值一样才是不同的方案数。
>
> 而题是只要sequences 不一样则不一样，那么我应该将容量扩增在外循环，硬币选购在内循环
>
> 而518则硬币选购在外循环，容量扩增在内循环。
>
> 简单理解（我菜）：在外循环则代表nums[1]拿过后就不会拿了，而在内循环nums[1]可以反复拿，也就是说存在nums[1]-nums[2]-nums[1]的顺序，而外循环则是nums[1]-nums[1]-nums[2]，是以nums[1]起头的，故跟顺序无关则外循环。

```java
class Solution {
    public int combinationSum4(int[] nums, int target) {
        int[] f = new int[target + 1];
        f[0] = 1;
        for(int i = 1 ; i <= target; i ++){
            for(int num : nums){
                if(i < num)continue;
                f[i] += f[i - num];
            }
        }
        return f[target];
    }
}
```

## LeetCode - 309.最佳股票时机含冷冻期

```text
输入: [1,2,3,0,2]
输出: 3 
解释: 对应的交易状态为: [买入, 卖出, 冷冻期, 买入, 卖出]
```

```java
class Solution {
    public int maxProfit(int[] prices) {
        if(prices == null || prices.length == 0)return 0;
        int n = prices.length;
        // 设置三个状态，0 ---  持有股票， 1 --- 冷冻期， 2 --- 不持有
        int[][] f = new int[n + 1][3];
        f[0][0] -= prices[0];
        for(int i = 1; i <= n; i ++){
            // 持有股票，可以从 2 不持有 买，也可以继续不持有 
            f[i][0] = Math.max(f[i - 1][2] - prices[i - 1],f[i - 1][0]);
            // 冷冻期 一定是 刚刚卖出 从0-- 持有 卖出
            f[i][1] = f[i - 1][0] + prices[i - 1];
            // 不持有股票，可以从冷冻期不持有，或者是 继续保持 不持有
            f[i][2] = Math.max(f[i - 1][1],f[i - 1][2]);
        }
        return Math.max(f[n][1],f[n][2]);
    }
}
```

> 当我们发现他们的状态只与f[i - 1]相关时，我们可以优化一维空间

```java
class Solution {
    public int maxProfit(int[] prices) {
        if(prices == null || prices.length == 0)return 0;
        int n = prices.length;
        // 设置三个状态，0 ---  持有股票， 1 --- 冷冻期， 2 --- 不持有
        int[] f = new int[3];
        f[0] = -prices[0];
        for(int i = 1; i <= n; i ++){
            int new_f0 = Math.max(f[0],f[2] - prices[i - 1]);
            int new_f1 = f[0] + prices[i - 1];
            int new_f2 = Math.max(f[1],f[2]);
            f[0] = new_f0;f[1] = new_f1;f[2] = new_f2;
        }
        return Math.max(f[1],f[2]);
    }
}
```

## LeetCode - 714.最佳股票时机含手续费

```text
fee 为手续费，每次买入卖出 是一笔手续费
输入: prices = [1, 3, 2, 8, 4, 9], fee = 2
输出: 8
解释: 能够达到的最大利润:  
在此处买入 prices[0] = 1
在此处卖出 prices[3] = 8
在此处买入 prices[4] = 4
在此处卖出 prices[5] = 9
总利润: ((8 - 1) - 2) + ((9 - 4) - 2) = 8.

来源：力扣（LeetCode）
链接：https://leetcode-cn.com/problems/best-time-to-buy-and-sell-stock-with-transaction-fee
著作权归领扣网络所有。商业转载请联系官方授权，非商业转载请注明出处。
```

> 我们可以简约成只有卖出时会存在手续费交易。

```java
class Solution {
    public int maxProfit(int[] prices, int fee) {
        if(prices == null || prices.length == 0)return 0;
        int n = prices.length;
        int[][] f = new int[n + 1][2]; // 0 -- 持有 1 --- 不持有
        f[0][0] = -prices[0];
        for(int i = 1 ;i <= n; i ++){
            f[i][0] = Math.max(f[i - 1][1] - prices[i - 1],f[i - 1][0]);
            f[i][1] = Math.max(f[i - 1][0] + prices[i - 1] - fee,f[i - 1][1]);
        }
        return f[n][1]; 
    }
}
```

> 优化一维空间复杂度，变为只有四个固定变量

```java
class Solution {
    public int maxProfit(int[] prices, int fee) {
        if(prices == null || prices.length == 0)return 0;
        int n = prices.length;
        int[] f = new int[2]; // 0 -- 持有 1 --- 不持有
        f[0] = -prices[0];
        for(int i = 1 ;i <= n; i ++){
            int new_f0 = Math.max(f[1] - prices[i - 1],f[0]);
            int new_f1 = Math.max(f[0] + prices[i - 1] - fee,f[1]);
            f[0] = new_f0;f[1] = new_f1;
        }
        return f[1]; 
    }
}
```

## LeetCode - 123.最佳股票时机 III

```text
你只有两次交易机会
输入: [3,3,5,0,0,3,1,4]
输出: 6
解释: 在第 4 天（股票价格 = 0）的时候买入，在第 6 天（股票价格 = 3）的时候卖出，这笔交易所能获得利润 = 3-0 = 3 。
     随后，在第 7 天（股票价格 = 1）的时候买入，在第 8 天 （股票价格 = 4）的时候卖出，这笔交易所能获得利润 = 4-1 = 3 。

来源：力扣（LeetCode）
链接：https://leetcode-cn.com/problems/best-time-to-buy-and-sell-stock-iii
著作权归领扣网络所有。商业转载请联系官方授权，非商业转载请注明出处。
```

```java
class Solution {
    public int maxProfit(int[] prices) {
        int firset_bug = Integer.MIN_VALUE,firset_sell = 0;
        int second_bug = Integer.MIN_VALUE,second_sell = 0;
        for(int pri : prices){
            if(firset_bug < -pri)firset_bug = -pri;
            if(firset_sell < firset_bug + pri)firset_sell = firset_bug + pri;
            if(second_bug < firset_sell - pri)second_bug = firset_sell - pri;
            if(second_sell < second_bug + pri)second_sell = second_bug + pri;
        }
        return second_sell;
    }
}
```

> dp

```java
class Solution {
    public int maxProfit(int[] prices) {
        if(prices == null || prices.length == 0)return 0;
        int n = prices.length;
        int[][][] f = new int[n + 1][3][2];
        // 初始化 第0次交易
        for(int i = 0; i <= n; i ++){
            f[i][0][0] = 0;
            f[i][0][1] = Integer.MIN_VALUE;
        }
        // 第0 天
        for(int i = 0; i <= 2; i ++){
            f[0][i][0] = 0;
            f[0][i][1] = -prices[0];
        }

        for(int i = 1 ; i <= n; i ++){
            for(int k = 1; k <= 2; k ++){
                // 每次买入算一笔交易
                f[i][k][0] = Math.max(f[i - 1][k][0],f[i - 1][k][1] + prices[i - 1]);
                f[i][k][1] = Math.max(f[i - 1][k][1],f[i - 1][k - 1][0] - prices[i - 1]);
            }
        }
        return f[n][2][0];

    }
}
```

## LeetCode - 123.最佳股票时机 IV

题目描述：你最多可以完成 **k** 笔交易。

```text
输入: [2,4,1], k = 2
输出: 2
解释: 在第 1 天 (股票价格 = 2) 的时候买入，在第 2 天 (股票价格 = 4) 的时候卖出，这笔交易所能获得利润 = 4-2 = 2 。

来源：力扣（LeetCode）
链接：https://leetcode-cn.com/problems/best-time-to-buy-and-sell-stock-iv
著作权归领扣网络所有。商业转载请联系官方授权，非商业转载请注明出处。
```

```java
class Solution {
    public int maxProfit(int k, int[] prices) {
        if(prices == null || prices.length == 0)return 0;
        int n = prices.length; 
        if(k >= n / 2){
            int res = 0;
            for(int i = 1; i < prices.length; i ++){
                if(prices[i] > prices[i - 1])res += prices[i] - prices[i - 1];
            }
            return res;
        }
        int[][][] f = new int[n + 1][k + 1][2];
        for(int i = 0; i <= n; i ++){
            f[i][0][0] = 0;
            f[i][0][1] = Integer.MIN_VALUE;
        }
        for(int i = 0; i <= k; i ++){
            f[0][i][0] = 0;
            f[0][i][1] = -prices[0];
        }

        for(int i = 1; i <= n; i ++ ){
            for(int j = 1; j <= k; j ++){
                f[i][j][0] = Math.max(f[i - 1][j][0],f[i - 1][j][1] + prices[i - 1]);
                f[i][j][1] = Math.max(f[i - 1][j][1],f[i - 1][j - 1][0] - prices[i - 1]);
            }
        }
        return f[n][k][0];
    }
}
```

## LeetCode - 583.两个字符串的删除操作

题目描述：给定两个单词 *word1* 和 *word2*，找到使得 *word1* 和 *word2* 相同所需的最小步数，每步可以删除任意一个字符串中的一个字符。

```text
输入: "sea", "eat"
输出: 2
解释: 第一步将"sea"变为"ea"，第二步将"eat"变为"ea"
```

> 其只能删除，故只要找到最长公共子序列即可（lcs），如果两个单词没有相同的则删除m + n 次，若有则m + n - 2 * lcs；

```java
class Solution {
    public int minDistance(String word1, String word2) {
        int n = word1.length(), m = word2.length();
        int[][] f = new int[n + 1][m + 1];
        for(int i = 1; i <= n; i ++){
            char w1 = word1.charAt(i - 1);
            for(int j = 1; j <= m; j ++){
                char w2 = word2.charAt(j - 1);
                if(w1 == w2){
                    f[i][j] = f[i - 1][j - 1] + 1;
                }else{
                    f[i][j] = Math.max(f[i - 1][j],f[i][j - 1]);
                }
            }
        }
        return m + n - 2 * f[n][m];
    }
}
```

## LeetCode -72.编辑距离

题目描述：给你两个单词 word1 和 word2，请你计算出将 word1 转换成 word2 所使用的最少操作数 。

你可以对一个单词进行如下三种操作：

1. 插入一个字符
2. 删除一个字符
3. 替换一个字符

```text
输入：word1 = "horse", word2 = "ros"
输出：3
解释：
horse -> rorse (将 'h' 替换为 'r')
rorse -> rose (删除 'r')
rose -> ros (删除 'e')

来源：力扣（LeetCode）
链接：https://leetcode-cn.com/problems/edit-distance
著作权归领扣网络所有。商业转载请联系官方授权，非商业转载请注明出处。
```

```java
class Solution {
    public int minDistance(String word1, String word2) {
        if(word1 == null || word2 == null)return 0;
        int n = word1.length(), m = word2.length();
        int[][] f = new int[n + 1][m + 1];

        for(int i = 1; i <= n; i++)f[i][0] = i;
        for(int i = 1; i <= m; i++)f[0][i] = i;

        for(int i = 1; i <= n; i ++ ){
            for(int j = 1; j <= m; j ++){
                // i 位置和 j 相等 则只要看 前i - 1 和 j - 1变化的次数
                if(word1.charAt(i - 1) == word2.charAt(j - 1))f[i][j] = f[i - 1][j - 1];
                else{
                    // 插入 替换 删除 对word1单词操作 等价 word2 操作 故我们只看做操作 word1 
                    // 如果替换一个字符，则使i == j（字符位置）相等 故 只要看 i - 1,j - 1；
                    // 如果 在 我word1单词 上删除一个字符 则不用看第i 个 字符了 因为被删除了则只要看 i - 1,j
                    // 如果 在 word1 插入一个字符，则i + 1 == j 那么只要看 前 i 个 和 j - 1 了；
                    f[i][j] = Math.min(Math.min(f[i - 1][j - 1],f[i - 1][j]),f[i][j - 1]) + 1;
                }
            }
        }
        return f[n][m];
    }
}
```

## LeetCode -650.只有两个键可以用

题目描述：最初在一个记事本上只有一个字符 'A'。你每次可以对这个记事本进行两种操作：

Copy All (复制全部) : 你可以复制这个记事本中的所有字符(部分的复制是不允许的)。
Paste (粘贴) : 你可以粘贴你上一次复制的字符。
给定一个数字 n 。你需要使用最少的操作次数，在记事本中打印出恰好 n 个 'A'。输出能够打印出 n 个 'A' 的最少操作次数。

```text
输入: 3
输出: 3
解释:
最初, 我们只有一个字符 'A'。
第 1 步, 我们使用 Copy All 操作。
第 2 步, 我们使用 Paste 操作来获得 'AA'。
第 3 步, 我们使用 Paste 操作来获得 'AAA'。

来源：力扣（LeetCode）
链接：https://leetcode-cn.com/problems/2-keys-keyboard
著作权归领扣网络所有。商业转载请联系官方授权，非商业转载请注明出处。
```

```java
// 如果是素数则返回素数，如果不是则可以分解质因数得到答案
// 比如9 = 3 * 3 则当我们有3个A时，我们可以复制3 粘贴 3 - 1次得到9
class Solution {
    public int minSteps(int n) {
        int res = 0, d = 2;
        while(n > 1){
            while(n % d == 0){
                res += d;
                n /= d;
            }
            d ++;
        }
        return res;
    }
}
```

```java
class Solution {
    public int minSteps(int n) {
        if(n == 1)return 0;
        for(int i = 2; i * i <= n; i ++){
            if(n % i == 0)return i + minSteps(n / i);
        }
        return n;
    }
}
```

