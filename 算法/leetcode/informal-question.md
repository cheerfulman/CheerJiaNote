## leetcode-面试题56 - I. 数组中数字出现的次数

一个整型数组 nums 里除两个数字之外，其他数字都出现了两次。请写程序找出这两个只出现一次的数字。要求时间复杂度是O(n)，空间复杂度是O(1)。 

示例 1：

输入：nums = [4,1,4,6]
输出：[1,6] 或 [6,1]
示例 2：

输入：nums = [1,2,10,4,1,4,3,3]
输出：[2,10] 或 [10,2]



异或求解：

```java
101011   如果 是两个相同的数，异或后一定为0.    		
011101		110111 			
    		110111
  ||		  ||
110110		000000
    
    
假设三个数字3，5，3
二进制分别为 11 101 11,我们进行异或：
    011 - 3
    101 - 5
    ||		
    110 - 6 3和5异或后
    011	- 3
    ||
    101 - 5 最后发现 就是只出现一次的5，因为相同的数字二进制异或为0；
异或 : 相同的位取0，不同的取1；
```



如果只有一个数字只出现过一次，其余都是出现两次的话，那么可以直接异或求解：

```java
ret = 0;
for(int i = 0; i < nums.length; i++)
    ret ^= nums[i];
```

可是此题是有两个出现过一次的数字，那么ret 就一定是这两个数的异或结果。

> 怎么办呢？

答案是：可以分组。

将两个只出现过一次的数分成不同的组。

找出异或结果ret 结果为1的位，代表此位，这两个数一个为0，一个为1。

+ 将此为为0的分为一组进行异或
+ 为1 的进行异或
+ 两个即为答案

```java
public int[] singleNumbers(int[] nums) {
    if (nums == null || nums.length < 2) return new int[0];
    int ret = 0,a = 0, b = 0;  // ret 为 两个数异或的结果， a，b为出现两个一次的数
    for(int i = 0; i < nums.length; i++)ret ^= nums[i];
	//求出最后一个为1的数， 也可以用 ret -= ret & (ret - 1);
    int cnt = ret & (-ret);
	//int cnt = ret - (ret & (ret - 1));
    for(int i = 0; i < nums.length; i++){
        if((nums[i] & cnt) == 0) a ^= nums[i];
        else b ^= nums[i];
    }
    return new int[] {a,b};
}
```

## leetcode-852山脉数组峰顶索引

y总板子：

```java
bool check(int x) {/* ... */} // 检查x是否满足某种性质

// 区间[l, r]被划分成[l, mid]和[mid + 1, r]时使用：
int bsearch_1(int l, int r)
{
    while (l < r)
    {
        int mid = l + r >> 1;
        if (check(mid)) r = mid;    // check()判断mid是否满足性质
        else l = mid + 1;
    }
    return l;
}
// 区间[l, r]被划分成[l, mid - 1]和[mid, r]时使用：
int bsearch_2(int l, int r)
{
    while (l < r)
    {
        int mid = l + r + 1 >> 1;
        if (check(mid)) l = mid;
        else r = mid - 1;
    }
    return l;
}
作者：yxc
链接：https://www.acwing.com/blog/content/277/
来源：AcWing
著作权归作者所有。商业转载请联系作者获得授权，非商业转载请注明出处。
```

我们把符合下列属性的数组 A 称作山脉：

A.length >= 3
存在 0 < i < A.length - 1 使得A[0] < A[1] < ... A[i-1] < A[i] > A[i+1] > ... > A[A.length - 1]
给定一个确定为山脉的数组，返回任何满足 A[0] < A[1] < ... A[i-1] < A[i] > A[i+1] > ... > A[A.length - 1] 的 i 的值。

示例 1：

输入：[0,1,0]
输出：1
示例 2：

输入：[0,2,1,0]
输出：1


提示：

3 <= A.length <= 10000
0 <= A[i] <= 10^6
A 是如上定义的山脉

```java
//寻找山峰下标函数
public int findInMountPeek(int l, int r,MountainArray mountainArr){
    while(l < r){
        int mid = l + r >> 1;
        if(mountainArr.get(mid) < mountainArr.get(mid + 1))l = mid + 1;
        else r = mid;
    }
    return l;
}
```

## leetCode-山脉数组中查找目标值

（这是一个 交互式问题 ）

给你一个 山脉数组 mountainArr，请你返回能够使得 mountainArr.get(index) 等于 target 最小 的下标 index 值。

如果不存在这样的下标 index，就请返回 -1。

 

何为山脉数组？如果数组 A 是一个山脉数组的话，那它满足如下条件：

首先，A.length >= 3

其次，在 0 < i < A.length - 1 条件下，存在 i 使得：

A[0] < A[1] < ... A[i-1] < A[i]
A[i] > A[i+1] > ... > A[A.length - 1]


你将 不能直接访问该山脉数组，必须通过 MountainArray 接口来获取数据：

MountainArray.get(k) - 会返回数组中索引为k 的元素（下标从 0 开始）
MountainArray.length() - 会返回该数组的长度


注意：

对 MountainArray.get 发起超过 100 次调用的提交将被视为错误答案。此外，任何试图规避判题系统的解决方案都将会导致比赛资格被取消。

为了帮助大家更好地理解交互式问题，我们准备了一个样例 “答案”：https://leetcode-cn.com/playground/RKhe3ave，请注意这 不是一个正确答案。

 

示例 1：

输入：array = [1,2,3,4,5,3,1], target = 3
输出：2
解释：3 在数组中出现了两次，下标分别为 2 和 5，我们返回最小的下标 2。
示例 2：

输入：array = [0,1,2,4,2,1], target = 3
输出：-1
解释：3 在数组中没有出现，返回 -1。


提示：

3 <= mountain_arr.length() <= 10000
0 <= target <= 10^9
0 <= mountain_arr.get(index) <= 10^9

```java
public int findInMountainArray(int target, MountainArray mountainArr) {
    int l = 0, r = mountainArr.length() - 1;
    int mountPeek = findInMountPeek(l, r,mountainArr);
    // System.out.println(mountPeek);
    int res = findInSortMount(l, mountPeek,mountainArr,target);
    if(res != -1)return res;
    return findInReverseMount(mountPeek + 1, r, mountainArr,target);
}
// 查找山峰下标值
public int findInMountPeek(int l, int r,MountainArray mountainArr){
    while(l < r){
        int mid = l + r >> 1;
        if(mountainArr.get(mid) < mountainArr.get(mid + 1))l = mid + 1;
        else r = mid;
    }
    return l;
}
// 从山峰左边找
public int findInSortMount(int l, int r,MountainArray mountainArr,int target){
    while(l < r){
        int mid = l + r + 1 >> 1;
        if(mountainArr.get(mid) > target)r = mid - 1;
        else l = mid;
    }
    if(mountainArr.get(l) == target)return l;
    else return -1;
}
// 山峰右边找
public int findInReverseMount(int l,int r,MountainArray mountainArr,int target){
    while(l < r){
        int mid = l + r >> 1;
        if(mountainArr.get(mid) > target)l = mid + 1;
        else r = mid;
    }
    if(mountainArr.get(l) == target)return l;
    else return -1;
}
```

> 接下来的题都是两数之和的进阶（学习两数之和的思想）

## leetcode 560-和为k的子数组

给定一个整数数组和一个整数 k，你需要找到该数组中和为 k 的连续的子数组的个数。

示例 1 :

输入:nums = [1,1,1], k = 2
输出: 2 , [1,1] 与 [1,1] 为两种不同的情况。
说明 :

数组的长度为 [1, 20,000]。
数组中元素的范围是 [-1000, 1000] ，且整数 k 的范围是 [-1e7, 1e7]。
通过次数44,915提交次数102,107

```java
class Solution {
    public int subarraySum(int[] nums, int k) {
        Map<Integer, Integer> map = new HashMap<>();
        int ans = 0,sum = 0;
        map.put(0,1);
        for(int num : nums){
            sum += num;
            if(map.containsKey(sum - k))ans += map.get(sum - k);
            map.put(sum,map.getOrDefault(sum,0) + 1);
        }
        return ans;
    }
}
```

## 1248.统计[优美子数组]

给你一个整数数组 nums 和一个整数 k。

如果某个 连续 子数组中恰好有 k 个奇数数字，我们就认为这个子数组是「优美子数组」。

请返回这个数组中「优美子数组」的数目。

 

示例 1：

输入：nums = [1,1,2,1,1], k = 3
输出：2
解释：包含 3 个奇数的子数组是 [1,1,2,1] 和 [1,2,1,1] 。
示例 2：

输入：nums = [2,4,6], k = 1
输出：0
解释：数列中不包含任何奇数，所以不存在优美子数组。
示例 3：

输入：nums = [2,2,2,1,2,2,1,2,2,2], k = 2
输出：16


提示：

1 <= nums.length <= 50000
1 <= nums[i] <= 10^5
1 <= k <= nums.length

```java
class Solution {
    public int numberOfSubarrays(int[] nums, int k) {
        int n = nums.length;
        if(n == 0)return 0;
        int odd = 0, ans = 0;
        Map<Integer,Integer> map = new HashMap<>();
        map.put(0,1);
        for(int i = 0; i < n; i ++){
            if((nums[i] & 1) == 1)odd ++;
            if(map.containsKey(odd - k))ans += map.get(odd - k);
            map.put(odd,map.getOrDefault(odd,0) + 1);
        }
        return ans;
    }   
}
```

## leetcode.454.四数相加II

给定四个包含整数的数组列表 A , B , C , D ,计算有多少个元组 (i, j, k, l) ，使得 A[i] + B[j] + C[k] + D[l] = 0。

为了使问题简单化，所有的 A, B, C, D 具有相同的长度 N，且 0 ≤ N ≤ 500 。所有整数的范围在 -228 到 228 - 1 之间，最终结果不会超过 231 - 1 。

例如:

输入:
A = [ 1, 2]
B = [-2,-1]
C = [-1, 2]
D = [ 0, 2]

输出:
2

解释:
两个元组如下:
1. (0, 0, 0, 1) -> A[0] + B[0] + C[0] + D[1] = 1 + (-2) + (-1) + 2 = 0
2. (1, 1, 0, 0) -> A[1] + B[1] + C[0] + D[0] = 2 + (-1) + (-1) + 0 = 0

```java
class Solution {
    public int fourSumCount(int[] A, int[] B, int[] C, int[] D) {
        int n = A.length,ans = 0;
        Map<Integer,Integer> map = new HashMap<>();
        for(int i = 0 ; i < n; i ++){
            for(int j = 0; j < n; j ++){
                int cur = -(A[i] + B[j]);
                map.put(cur,map.getOrDefault(cur,0) + 1);
            }
        }

        for(int num1 : C){
            for(int num2 : D){
                int cur = num1 + num2;
                if(map.containsKey(cur))ans += map.get(cur);
            }
        }
        return ans;
    }
}
```

## leetcode-5.最长回文子串

给定一个字符串 s，找到 s 中最长的回文子串。你可以假设 s 的最大长度为 1000。

示例 1：

输入: "babad"
输出: "bab"
注意: "aba" 也是一个有效答案。
示例 2：

输入: "cbbd"
输出: "bb"

> 枚举所有中点

```java
public String longestPalindrome(String s) {
    int len_str = s.length();
    if(s == null || len_str < 1)return "";
    int st = 0, en = 0;
    for(int i = 0; i < len_str; i++){
        int len1 = expend(s,i,i);
        int len2 = expend(s,i,i+1);
        int len = Math.max(len1,len2);
        if(en - st + 1 < len){
            st = i - (len - 1) / 2;
            en = i + len / 2;
        }
    }

    return s.substring(st,en + 1);
}
private int expend(String str, int left, int right){
    // char[] s = str.toCharArray();
    int len = str.length();
    while(left >= 0 && right < len && str.charAt(left) == str.charAt(right)){
        left--;
        right++;
    }
    return right - left - 1;
}
```

