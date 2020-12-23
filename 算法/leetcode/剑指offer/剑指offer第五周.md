# 剑指offer第五周

## 57.数字序列中某一位的数字

数字以0123456789101112131415…的格式序列化到一个字符序列中。

在这个序列中，第5位（从0开始计数）是5，第13位是1，第19位是4，等等。

请写一个函数求任意位对应的数字。

#### 样例

```
输入：13

输出：1
```

> 思路全在代码中

```java
class Solution {
    public int digitAtIndex(int n) {
        // base 为当前位数的起点（0，10，100，1000）
        // i 当前数的位数是多少（10-99 = 2， 100 - 999 = 3）
        // s 则n在第几位的区间上
        long base = 1, i = 1,s = 9;
        while(n > i * s){
            n -= i * s;
            i ++;
            s *= 10;
            base *= 10;
        }
        // 此时n为该区间上所处的位数
        // number 求出 n 所处的数 (n + i - 1) / i ---（n/i向上取整） 减1则是因为 base开始算比如第3个数实际上
        // 是 1000 + 3 - 1 = 1002嘛 
        long number = base + (n + i - 1) / i - 1;
        // r求出 当前n所处的位数是第几位，如果n % i == 0 则是最后一位
        long r = n % i == 0 ? i : n % i;
        // 去掉r为
        for(int j = 0; j < i - r; j ++)number /= 10;
        return (int)number % 10;
    }
}
```

## 58. 把数组排成最小的数

输入一个正整数数组，把数组里所有数字拼接起来排成一个数，打印能拼接出的所有数字中最小的一个。

例如输入数组[3, 32, 321]，则打印出这3个数字能排成的最小数字321323。

#### 样例

```
输入：[3, 32, 321]

输出：321323
```

**注意**：输出数字的格式为字符串。

```java
class Solution {
    public String printMinNumber(int[] nums) {
        int n = nums.length;
        String[] str = new String[n];
        for(int i = 0; i < n; i++) str[i] = String.valueOf(nums[i]);
        Arrays.sort(str, new Comparator<String>(){
            public int compare(String x, String y){
                String ab = x + y, ba = y + x;
                return ab.compareTo(ba);
            }
        });
        
        StringBuilder sb = new StringBuilder();
        for(String num : str)sb.append(num);
        return sb.toString();
    }
}
```

## 59.把数字翻译成字符串

给定一个数字，我们按照如下规则把它翻译为字符串：

0翻译成”a”，1翻译成”b”，……，11翻译成”l”，……，25翻译成”z”。

一个数字可能有多个翻译。例如12258有5种不同的翻译，它们分别是”bccfi”、”bwfi”、”bczi”、”mcfi”和”mzi”。

请编程实现一个函数用来计算一个数字有多少种不同的翻译方法。

#### 样例

```
输入："12258"

输出：5
```

> f[i] : 代表第i位时，的方案数
>
> 如果i自成一位则f[i] = f[i - 1];
>
> 如果i 可以和 i -1 组成一对 则 f[i] = f[i - 1] + f[i - 2];

```java
class Solution {
    // "12258"
    // i f[i] = f[i - 1];
    // i - 1 f[i] = f[i - 1] + f[i - 2];
    public int getTranslationCount(String s) {
        int n = s.length(),pre = Integer.parseInt(s.substring(0,1));
        int[] f = new int[n + 1];
        f[0] = 1;
        for(int i = 1; i <= n; i ++){
            f[i] = f[i - 1];
            int cur = Integer.parseInt(s.substring(i - 1,i));
            if(i > 1 && pre != 0 && pre * 10 + cur <= 25)f[i] = Math.max(f[i],f[i - 1] + f[i - 2]);
            pre = cur;
        }
        return f[n];
    }
}
```

## 60.礼物的最大价值

在一个m×n的棋盘的每一格都放有一个礼物，每个礼物都有一定的价值（价值大于0）。

你可以从棋盘的左上角开始拿格子里的礼物，并每次向右或者向下移动一格直到到达棋盘的右下角。

给定一个棋盘及其上面的礼物，请计算你最多能拿到多少价值的礼物？

**注意：**

- m,n>0m,n>0

样例：

```
输入：
[
  [2,3,1],
  [1,7,1],
  [4,6,1]
]

输出：19

解释：沿着路径 2→3→7→6→1 可以得到拿到最大价值礼物。
```

> dp

```java
class Solution {
    public int getMaxValue(int[][] grid) {
        int n = grid.length,m = grid[0].length;
        int[][] f = new int[n + 1][m + 1];
        for(int i = 1; i <= n; i ++){
            for(int j = 1; j <= m; j ++){
                f[i][j] = Math.max(f[i - 1][j],f[i][j - 1]) + grid[i - 1][j - 1];
            }
        }
        return f[n][m];
    }
}
```

## 61. 最长不含重复字符的子字符串

请从字符串中找出一个最长的不包含重复字符的子字符串，计算该最长子字符串的长度。

假设字符串中只包含从’a’到’z’的字符。

#### 样例

```
输入："abcabc"

输出：3
```

> 双指针算法

```java
class Solution {
    public int longestSubstringWithoutDuplication(String s) {
        if(s.length() == 0 || s.length() == 1)return s.length();
        int i = 0, res = 0;
        Set<Character> set = new HashSet<>();
        for(int k = 0; k < s.length(); k ++){
            char ch = s.charAt(k);
            boolean bool = set.add(ch);
            if(bool)res = Math.max(k - i + 1,res);
            else{
                while(!set.add(ch)){
                    set.remove(s.charAt(i));
                    i++;
                }
            }
        }
        return res;
    }
}
```

## 62. 丑数

我们把只包含质因子2、3和5的数称作丑数（Ugly Number）。

例如6、8都是丑数，但14不是，因为它包含质因子7。

求第n个丑数的值。

#### 样例

```
输入：5

输出：5
```

**注意**：习惯上我们把1当做第一个丑数。

> 样例: 1,2,3,4,5,6,8,9,10
>
> 故输出5；
>
> 由题可知当时其序列全部由1，2，3，5组成。
>
> 故从1开始，乘2，3，5，选最小的放在序列后面即可组成该序列

```java
class Solution {
    public int getUglyNumber(int n) {
        int i = 0, j = 0, k = 0;
        List<Integer> list = new ArrayList<>();
        list.add(1);
        while(--n > 0){
            int t = Math.min(list.get(i) * 2,Math.min(list.get(j) * 3,list.get(k) * 5));
            list.add(t);
            if(t == list.get(i) * 2)i++;
            if(t == list.get(j) * 3)j++;
            if(t == list.get(k) * 5)k++;
        }
        
        return list.get(list.size() - 1);
    }
}
```

## 63. 字符串中第一个只出现一次的字符

在字符串中找出第一个只出现一次的字符。

如输入`"abaccdeff"`，则输出`b`。

如果字符串中不存在只出现一次的字符，返回#字符。

#### 样例：

```
输入："abaccdeff"

输出：'b'
```

> 利用hash存储出现次数，再循环字符串将第一个出现次数为1的字符返回即可

```java
class Solution {
    public char firstNotRepeatingChar(String s) {
        Map<Character,Integer> map = new HashMap<>();
        for(int i = 0; i < s.length(); i ++){
            char c = s.charAt(i);
            map.put(c,map.getOrDefault(c,0) + 1);
        }
        for(int i = 0; i < s.length(); i ++){
            char c = s.charAt(i);
            int idx = map.get(c);
            if(idx == 1)return c;
        }
        return '#';
    }
}
```

## 64. 字符流中第一个只出现一次的字符

请实现一个函数用来找出字符流中第一个只出现一次的字符。

例如，当从字符流中只读出前两个字符”go”时，第一个只出现一次的字符是’g’。

当从该字符流中读出前六个字符”google”时，第一个只出现一次的字符是’l’。

如果当前字符流没有存在出现一次的字符，返回#字符。

#### 样例

```
输入："google"

输出："ggg#ll"

解释：每当字符流读入一个字符，就进行一次判断并输出当前的第一个只出现一次的字符。
```

**当出现字符，配合出现次数时-------请想hash**

> 利用hash存储字符和出现的次数。
>
> 详情见代码注释：

```java
class Solution {    
    Map<Character,Integer> map = new HashMap<>();
    String str = "";
    int i = 0; // 前一个返回的位置
    //Insert one char from stringstream   
    public void insert(char ch){
        // 每次插入放入 map中
        map.put(ch,map.getOrDefault(ch,0) + 1);
        str += ch;
    }
    //return the first appearence once char in current stringstream
    public char firstAppearingOnce(){
        int n = str.length();
        // 看上一个 返回的位置符不符合要求
        char c = str.charAt(i);
        int t = map.get(c);
        if(t == 1)return c;
        else{
            // 如果不符合要求， 将指针i 往后移
            for(; i < n; i ++){
                c = str.charAt(i);
                t = map.get(c);
                if(t == 1)return c;
            }
        }
        return '#';
    }
}

```

## 65. 数组中的逆序对

在数组中的两个数字如果前面一个数字大于后面的数字，则这两个数字组成一个逆序对。

输入一个数组，求出这个数组中的逆序对的总数。

#### 样例

```
输入：[1,2,3,4,5,6,0]

输出：6
```

> 归并排序求解：
>
> 在归并的双指针中作文章: 当nums[i] >  nums[j] 时 则 i ~ mid的值都比nums[j] 大，故就有 mid - i + 1个逆序对

```java
class Solution {
    int res = 0;
    int[] temp;
    public int inversePairs(int[] nums) {
        temp = new int[nums.length];
        merge_sort(nums,0,nums.length - 1);
        return res;
    }
    public void merge_sort(int[] nums,int l, int r){
        if(l >= r)return ;
        int mid = l + r >> 1;
        merge_sort(nums,l,mid);merge_sort(nums,mid + 1,r);
        int i = l, j = mid + 1,k = 0;
        while(i <= mid && j <= r){
            if(nums[i] <= nums[j])temp[k++] = nums[i++];
            else{
                res += mid - i + 1;a
                temp[k++] = nums[j++];
            }
        }
        while(i <= mid)temp[k++] = nums[i++];
        while(j <= r)temp[k++] = nums[j++];
        
        for(i = l,k = 0; i <= r;)nums[i++] = temp[k++];
    }
}
```

## 66. 两个链表的第一个公共结点

输入两个链表，找出它们的第一个公共结点。

当不存在公共节点时，返回空节点。

#### 样例

```
给出两个链表如下所示：
A：        a1 → a2
                   ↘
                     c1 → c2 → c3
                   ↗            
B:     b1 → b2 → b3

输出第一个公共节点c1
```

> 利用hash表,第一个相同的节点则是公共节点

```java
class Solution {
    public ListNode findFirstCommonNode(ListNode headA, ListNode headB) {
        Set<ListNode> set = new HashSet<>();
        while(headA != null){
            set.add(headA);
            headA = headA.next;
        }
        while(headB != null){
            if(!set.add(headB))return headB;
            headB = headB.next;
        }
        return null;
    }
}
```

> 利用距离求解：假设两个链表a,b,公共部分c
>
> 我们发现a+c+b = b+c+a(这里大家看不懂的可以搜下，加法的交换律，（滑稽）)
>
> 也就是说当我a+c这条链走完时，我就走b链，b+c走完时，就走a链，最后它们相等的地方就是公共节点。
>
> 如果它们不相交，则它们都等于null时相等，a+b = b+a（c == null）

```java
class Solution {
    public ListNode findFirstCommonNode(ListNode headA, ListNode headB) {
        ListNode p = headA, q = headB;
        while(p != q){
            if(p != null)p = p.next;
            else p = headB;
            if(q != null)q = q.next;
            else q = headA;
        }
        return q;
    }
}
```

## 67.数字在排序数组中出现的次数

统计一个数字在排序数组中出现的次数。

例如输入排序数组[1, 2, 3, 3, 3, 3, 4, 5]和数字3，由于3在这个数组中出现了4次，因此输出4。

#### 样例

```
输入：[1, 2, 3, 3, 3, 3, 4, 5] ,  3

输出：4
```

> 二分法

```java
class Solution {
    public int getNumberOfK(int[] nums, int k) {
        int res = 0, l = 0, r = nums.length-1,ansl = 0,ansr = 0;
        if(r == -1)return 0;
        while(l < r){
            int mid = l + r >> 1;
            if(nums[mid] >= k)r = mid;
            else l = mid + 1;
        }
        ansl = l;
        l = 0; r = nums.length - 1;
        while(l < r){
            int mid = l + r + 1 >> 1;
            if(nums[mid] <= k)l = mid;
            else r = mid - 1;
        }
        ansr = l;
        if(nums[ansl] != k && nums[ansr] != k) return 0;
        // System.out.println(ansr + " " + ansl);
        return ansr - ansl + 1;
    }
}
```

