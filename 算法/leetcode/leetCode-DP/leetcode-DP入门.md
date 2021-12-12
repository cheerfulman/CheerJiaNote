## LeetCode动态规划入门篇

地址：https://leetcode-cn.com/study-plan/dynamic-programming/?progress=a03m4kv

### The third day

#### [打家劫舍](https://leetcode-cn.com/problems/house-robber/)

题目描述：给你一串数组，不能够拿相邻的值，求能取的最大和；

```txt
状态转移方程：
要么偷第i - 1家，要么偷i - 2家
f[i] = max(f[i - 1], f[i - 2] + nums[i])
```

```go
func max(a, b int) int {
    if a > b {
        return a
    }
    return b
}

func rob(nums []int) int {
    if len(nums) <= 1 {
        return nums[0]
    }
    f := make([]int, 105)
    f[0], f[1] = nums[0], max(nums[0], nums[1])
    
    for i := 2; i < len(nums); i ++ {
        f[i] = max(f[i - 1], f[i - 2] + nums[i])
    }
    return f[len(nums) - 1]
}
```

#### [打家劫舍II](https://leetcode-cn.com/problems/house-robber/)

题目描述： 一个环形数组，不能取相邻数组；

```txt
复杂做法：
定义二维数组表状态，f[0][i], f[1][i]， 0-代表不偷第一家，1代表偷第一家
转移方程
不在数组末尾时：
f[0][i] = max(f[0][i - 1], f[0][i - 2] + nums[i])
f[1][i] = max(f[1][i - 1], f[1][i - 2] + nums[i])
在数组末尾时：
f[0][i] = max(f[0][i - 1], f[0][i - 2] + nums[i])
f[1][i] = f[1][i - 1]  --- 此时f[1][i] 不能取最后一个数，因为与第一家相邻


初始化：f[1][0], f[0][1], f[1][1] = nums[0], nums[1], nums[0]
```

```go
func max(a, b int) int {
    if a > b {
        return a
    }
    return b
}

func rob(nums []int) int {
    if len(nums) <= 1 {
        return nums[0]
    }
    var f [2][105]int
    
    f[1][0], f[0][1], f[1][1] = nums[0], nums[1], nums[0]
    
    for i := 2; i < len(nums); i ++ {
        if i == len(nums) - 1 {
            f[0][i] = max(f[0][i - 1], f[0][i - 2] + nums[i])
            f[1][i] = f[1][i - 1]
        } else {
            f[0][i] = max(f[0][i - 1], f[0][i - 2] + nums[i])
            f[1][i] = max(f[1][i - 1], f[1][i - 2] + nums[i])
        } 
    }
    return max(f[0][len(nums) - 1], f[1][len(nums) - 1])
}
```

```txt
题解做法：
分两步：
第一步从f[0]偷到f[len - 1] ~ 偷第0家
第二步从f[1]偷到f[len]	~ 不偷第0家
状态转移就很简单了，同打家劫舍I
f[i] = max(f[i - 1], f[i - 2] + nums[i])
```

```go
func max(a, b int) int {
    if a > b {
        return a
    }
    return b
}

func _rob(nums []int) int {
    first, second := nums[0], max(nums[0], nums[1])
    for _, val := range nums[2:] {
        first, second = second, max(first + val, second)
    }
    return second
}

func rob(nums []int) int {
    n := len(nums)
    if n == 1 {
        return nums[0]
    }
    if n == 2 {
        return max(nums[0], nums[1])
    }
    return max(_rob(nums[0:n-1]), _rob(nums[1:]))
}
```

#### [740 删除并获得点数](https://leetcode-cn.com/problems/delete-and-earn/)

题意：给你一串数组，不能拿值相近的数字，求能拿的最大值；

如[3,4,2] 拿4，则不能拿3和5，所以答案为6，拿4和2

**打家劫舍变种**

注意数值大小小于1000；

**提示：**

- `1 <= nums.length <= 2 * 104`
- `1 <= nums[i] <= 104`

用sum初始化，得到所有数值的总和，再套用打家劫舍不能拿相邻的值即可

```go
func deleteAndEarn(nums []int) int {
    maxVal := 0
    for _, num := range nums {
        maxVal = max(maxVal, num)
    }
    sum := make([]int, maxVal + 1)
    
    for _, val := range nums {
        sum[val] += val
    }
    
    first, second := sum[0], max(sum[0], sum[1])
    for i := 2; i <= maxVal; i ++ {
        first, second = second, max(second, first + sum[i])
    }
    return second
}

func max(a, b int) int {
    if a > b {
        return a
    }
    return b
}
```

### The fourth day

#### [55 跳跃游戏](https://leetcode-cn.com/problems/jump-game/)

题目描述：直接看样例

Example 1:

```
Input: nums = [2,3,1,1,4]
Output: true
Explanation: Jump 1 step from index 0 to 1, then 3 steps to the last index.
```

Example 2:

```txt
Input: nums = [3,2,1,0,4]
Output: false
Explanation: You will always arrive at index 3 no matter what. Its maximum jump length is 0, which makes it impossible to reach the last index.
```

贪心即可记录最大可以到的下标；

```go
func canJump(nums []int) bool {
    n := len(nums)
    maxPosition := nums[0]
    for i := 0; i < n; i ++ {
        if maxPosition < i {
            return false
        }
        maxPosition = max(maxPosition, i + nums[i])
    }
    return true
}

func max(a, b int) int {
    if a > b {
        return a
    }
    return b
}
```

#### [45 跳跃游戏 II](https://leetcode-cn.com/problems/jump-game-ii/)

题意：假设你永远可以到达数组最后的位置，求最少跳动的次数；

> 我们使用
>
> maxPosition： 记录当前能跳到的最远距离。
>
> end: 表示上次跳到的距离
>
> 而当我每次走到end时，将end = maxPosition(表示从 0 ~ end为止下一步能到的最远距离就是maxPosition)

```go
func jump(nums []int) int {
    length := len(nums)
    steps, maxPosition, end := 0, 0, 0
    for i := 0; i < length - 1; i ++ {
        maxPosition = max(maxPosition, i + nums[i])
        if i == end {
            steps ++
            end = maxPosition
        }
    }
    return steps
}

func max(a, b int) int {
    if a > b {
        return a
    }
    return b
}
```

### Fifth day

#### [53 最大子数组和](https://leetcode-cn.com/problems/maximum-subarray/submissions/)

题意：一串数组，求最大子数组的和；

> F[n] 表示以nums[n]结尾的最大连续子数组
>
> 方程：f[i] = max(nums[i], f[i - 1] + nums[i])
>
> 由于只用f[i - 1] 来递推可用滚动数组的思想优化空间 -- 只用一个变量pre 即可

```go
func maxSubArray(nums []int) int {
    max := nums[0]
    for i := 1; i < len(nums); i++ {
        if nums[i] + nums[i - 1] > nums[i] {
            nums[i] += nums[i - 1]
        }
        if max < nums[i] {
            max = nums[i]
        }
    }
    return max
}
```

#### [918 环形子数组最大和](https://leetcode-cn.com/problems/maximum-sum-circular-subarray/)

题意：同53，但是数组是环形的；

分情况讨论：

1. 最大子数组 不成环 --- 变成53题 
2. 最大子数组 成环 ，那么最小子数组和就不会成环 --- (total - minSum) 则为答案

如何证明**最大子数组和**剩下的部分加起来就一定是**最小子数组和**呢？

证明1. 最大子数组和 与 最小子数组和  并集一定为当前数组

> 假设**最大子数组和**区间为**[l1,r1]**， 最小子数组和区间为**[l2,r2]**， 0的部分属于**最大子数组和**
>
> 表示为[l1 ~ r1,  val1,val2,val3, l2 ~ r2, val4, val5]
>
> 可知 如果  val1 + val2 + val3  >= 0 则 r1 会等于 val3 下标
>
> 如果  val1 + val2 + val3  < 0 则 l2 = val下标
>
> val4 与 val 5 同理
>
> 故最大数组和与最小数组和的元素总个数 = 原数组元素总个数

证明2. 最大子数组和 与 最小子数组和  没有交集

> 一段连续下标的数值v1,v2,v3， v1 + v2 + v3 要么大于0属于最大子数组和中，要么小于0属于最小子数组和

```go
func maxSubarraySumCircular(nums []int) int {
    total, maxSum, minSum, maxPre, minPre := nums[0], nums[0], nums[0], nums[0], nums[0]
    for i := 1; i < len(nums); i ++ {
        total += nums[i]
        maxPre = max(nums[i], nums[i] + maxPre)
        if maxPre > maxSum {
            maxSum = maxPre
        }
        minPre = min(nums[i], nums[i] + minPre)
        if minPre < minSum {
            minSum = minPre
        }
    }
    if maxSum < 0 {
        return maxSum
    }
    return max(maxSum, total - minSum)
}

func max(a, b int) int {
    if a > b {
        return a
    }
    return b
}
func min(a, b int) int {
    if a > b {
        return b
    }
    return a
}
```

