## Educational DP Contest

### A-Frog 1

题意:青蛙可以跳一格或者两格，跳到某格的花费是|hi−hj|；求最少的花费

```java
public static void main(String[] args) {
    int N = sc.nextInt();
    int[] a = new int[N + 1],f = new int[N + 1];
    for(int i = 1; i <= N; i ++) a[i] = sc.nextInt();
    // 第一格的花销是0，故直接从2开始，
    for(int i = 2; i <= N; i++){
        if(i == 2)f[i] = f[i - 1] + Math.abs(a[i] - a[i - 1]);
        else f[i] = Math.min(Math.abs(a[i] - a[i - 1]) + f[i - 1],Math.abs(a[i] - a[i - 2]) + f[i - 2]);
    }
    System.out.println(f[N]);
}
```

### B-Frog 2

题意:同上题，只是可以跳K格

```java
static final int inf = 0x3f3f3f3f;
public static void main(String[] args) {
    int N = sc.nextInt(), k = sc.nextInt();
    int[] a = new int[N + 1],f = new int[N + 1];
    for(int i = 1; i <= N; i ++) a[i] = sc.nextInt();
    Arrays.fill(f,inf);
    f[1] = 0;

    // 注释掉的为 正跳，未注释的表示 当前位置从哪里来的递推
    for(int i = 2; i <= N; i++){
        for(int j = 1; j <= k; j ++){
            //                if(i + j <= N){
            //                    f[i + j] = Math.min(Math.abs(a[i] - a[i + j]) + f[i],f[i + j]);
            //                }else break;
            if(i > j){
                f[i] = Math.min(Math.abs(a[i] - a[i - j]) + f[i - j],f[i]);
                //                    else f[i] = Math.abs(a[i] - a[i - j]) + f[i - j];
            }else break;
        }
    }
    //        for(int i = 1; i <= N; i++) System.out.print(f[i] + " ");
    System.out.println(f[N]);
}
```

### C-Vacation

题意：每天有三种选择，每种选择有一定的幸福值，连续两天不能选一样的，求最大的幸福值

> 递推，记录一下上一天的选择。

```java
public static void main(String[] args) {
    int N = sc.nextInt();
    int[] a = new int[N  + 1], b = new int[N + 1], c = new int[N + 1];
    int[][] f = new int[N + 1][3];
    for(int i = 1; i <= N; i ++){
        a[i] = sc.nextInt();
        b[i] = sc.nextInt();
        c[i] = sc.nextInt();
    }
    // f[i][0] 表示当前选择 第 1 项运动。
    for(int i = 1; i <= N; i ++){
        f[i][0] = Math.max(f[i - 1][1],f[i - 1][2]) + a[i];
        f[i][1] = Math.max(f[i - 1][0],f[i - 1][2]) + b[i];
        f[i][2] = Math.max(f[i - 1][1],f[i - 1][0]) + c[i];
    }

    System.out.println(Math.max(Math.max(f[N][0],f[N][1]),f[N][2]));
}
```

### D-Knapsack 1

题意: 单纯的0，1背包。

```java
public static void main(String[] args) {
    int N = sc.nextInt(), W = sc.nextInt();
    int[] v = new int[N + 1], w = new int[N + 1];
    long[] f = new long[W  + 1];

    for(int i = 1; i <= N; i ++){
        v[i] = sc.nextInt();
        w[i] = sc.nextInt();
    }
    for(int i = 1; i <= N; i++){
        for(int j = W; j >= v[i]; j --){
            f[j] = Math.max(f[j],f[j - v[i]] + w[i]);
        }
    }
    System.out.println(f[W]);
}
```

### E-Knapsack 2

题意：将0，1背包的体积变成10^9,故不可以直接枚举体积；

> 而其价值在1e3以内，故枚举价值，f[j] 代表，当前价值为 j的 最小重量；

```java
static final int inf = 0x3f3f3f3f;
public static void main(String[] args) {
    int N = sc.nextInt(), W = sc.nextInt();
    int bound = 100 * 1000;
    int[] v = new int[N + 1], w = new int[N + 1];
    long[] f = new long[bound  + 1];
    Arrays.fill(f,inf);
    f[0] = 0;
    for(int i = 1; i <= N; i ++){
        v[i] = sc.nextInt();
        w[i] = sc.nextInt();
    }
    // f[j] 代表，当前价值为 j的 最小重量；
    for(int i = 1; i <= N; i++){
        for(int j = bound; j >= w[i]; j --){
            f[j] = Math.min(f[j],f[j - w[i]] + v[i]);
        }
    }
    int res = 0;
    for(int i = 0; i <= bound; i ++){
        // 如果 重量小于W 代表可以取，则价值为 i;
        if(f[i] <= W)res = i;
    }

    System.out.println(res);
}
```

### F-LCS

题意:最长公共子序列

> 用last【】【】记录当前dp数组走的方向，方便逆推；
>
> 也可以直接判断，不需要last数组，详情见代码

```java
public static void main(String[] args) {
    String s = sc.next(), t = sc.next();
    char[] s1 = s.toCharArray(), t1 = t.toCharArray();
    int[][] f = new int[s1.length + 1][t1.length + 1],last = new int[s1.length + 1][t1.length + 1];
    StringBuilder sb = new StringBuilder();
    // 第二种 回溯，可以借助一个数组，标记 dp的方向；
    for(int i = 1; i <= s1.length; i ++) {
        for (int j = 1; j <= t1.length; j++) {
            if (s1[i - 1] == t1[j - 1]) {
                f[i][j] = f[i - 1][j - 1] + 1;
                last[i][j] = 3; // 记录方向 左上角
            } else {
                if (f[i][j - 1] > f[i - 1][j]) {
                    f[i][j] = f[i][j - 1];
                    last[i][j] = 1; // 记录方向 左
                } else {
                    f[i][j] = f[i - 1][j];
                    last[i][j] = 2;// 记录方向 上
                }
            }
        }
    }
    int x = s1.length - 1, y = t1.length - 1;
    while(f[x + 1][y + 1] > 0){
        if(last[x + 1][y + 1] == 3){
            sb.append(s1[x]);
            x--;
            y--;
        }else if(last[x + 1][y + 1] == 2)x--;
        else y--;
    }

    /*  第一种回溯
        for(int i = 1; i <= s1.length; i ++){
            for(int j = 1; j <= t1.length; j ++){
                if(s1[i - 1] == t1[j - 1])f[i][j] = f[i - 1][j - 1] + 1;
                else {f[i][j] = Math.max(f[i][j - 1],f[i - 1][j]);
            }
        }

        int x = s1.length - 1, y = t1.length - 1;
        // 当前 序列长度为0时，出循环
        while(f[x + 1][y + 1] > 0){ 
            if(s1[x] == t1[y]){  // 如果两个序列值相等，回到左上角
                sb.append(s1[x]);
                x--;y--;
            }
            else{
                if(f[x + 1][y + 1] == f[x + 1][y])y--;
                else x--;
            }
        }
        */

    System.out.println(sb.reverse().toString());
}
```

### G - Longest Path

题意:求DAG中求最长的一条路径；

> 拓扑排序+dp;

```java
import java.util.ArrayDeque;
import java.util.ArrayList;
import java.util.Deque;
import java.util.Scanner;

public class Main {
    static Scanner sc = new Scanner(System.in);

    public static void main(String[] args) {
        int N = sc.nextInt(), M = sc.nextInt(), res = 0;
        int[] f = new int[N + 1],degree = new int[N + 1];
        ArrayList[] edges = new ArrayList[N + 1];
        Deque<Integer> q = new ArrayDeque<>();
        for(int i = 0; i <= N; i ++)edges[i] = new ArrayList<Integer>();

        for(int i = 1; i <= M; i ++){
            int v = sc.nextInt(), u = sc.nextInt();
            edges[v].add(u);
            degree[u] ++; // 入度值
        }
        // 当入度值为0，入队
        for(int i = 1; i <= N; i ++)if(degree[i] == 0)q.offer(i);
        while(!q.isEmpty()){
            int cur = q.poll();
            for(Object end : edges[cur]){
                Integer e = (Integer)end;
                f[e] = Math.max(f[cur] + 1,f[e]);
                res = Math.max(res,f[e]);
                // 入读值减一，当没有点指向该点时，入队
                degree[e] --;
                if(degree[e] == 0)q.offer(e);
            }
        }
        
        System.out.println(res);
    }
}
```

### H-Grid 1

题意: 一个迷宫，只能向右和向下走，求从左上角到右下角的路径有多少；

> dp 求解， 该点的方案等于上面点的方案 + 左边的方案；
>
> dp(i,j) = dp(i - 1,j) + dp(i,j - 1);

```java
static final int MOD = (int)1e9 + 7;
public static void main(String[] args) {
    int H = sc.nextInt(), W = sc.nextInt();
    char[][] maze = new char[H + 1][W + 1];
    int[][] f = new int[H + 1][W + 1];
    f[1][1] = 1;
    for(int i = 1; i <= H; i ++){
        String s = sc.next();
        for(int j = 1; j <= W; j ++){
            maze[i][j] = s.charAt(j - 1);
        }
    }
    // 地推
    for(int i = 1; i <= H; i ++){
        for(int j = 1; j <= W; j ++){
            if(i == 1 && j == 1)continue;
            if(maze[i][j] == '.')f[i][j] = (f[i - 1][j] + f[i][j - 1]) % MOD;
        }
    }
    System.out.println(f[H][W]);
}
```

## I-Coins

题意: 求硬币正面朝上大于朝下的概率

```java
public static void main(String[] args) {
    int N = sc.nextInt();
    double[] p = new double[N + 1];
    double[][] f = new double[N + 1][N + 1];
    for(int i = 1; i <= N;  i++)p[i] = sc.nextDouble();

    //         投i次，向上次数为j;
    f[0][0] = 1;
    for(int i = 1; i <= N; i ++){
        f[i][0] = f[i - 1][0] * (1 - p[i]);
        for(int j = 1; j <= i; j ++){
            f[i][j] = f[i - 1][j - 1] * p[i] + f[i - 1][j] * (1 - p[i]);
        }
    }
    int cnt;
    if((N&1) == 1) cnt = N + 1 >> 1;
    else cnt = (N >> 1) + 1;
    double res = 0.0;
    for(int i = cnt; i <= N; i++)res += f[N][i];
    System.out.println(res);


    //         向上次数为i，投j次
    //        f[0][0] = 1;
    //        for(int j = 1; j <= N; j ++) {
    //            f[0][j] = f[0][j - 1] * (1 - p[j]);
    //        }
    //        for(int i = 1; i <= N; i++){
    //            for(int j = 1; j <= N; j ++){
    //                f[i][j] = f[i - 1][j - 1] * p[j] + f[i][j - 1] * (1 - p[j]);
    //            }
    //        }
    //        double res = 0.0;
    //        for(int i = 0; i <= N; i ++){
    //            if(i > (N - i))res += f[i][N];
    //        }
    //        System.out.println(res);
}
```



## leetcode-221.最大正方形

题目链接：https://leetcode-cn.com/problems/maximal-square/

dp求解，其左上角，上方，左方的最小值。

```java
class Solution {
    public int maximalSquare(char[][] matrix) {
        int r = matrix.length;
        if(r == 0)return 0;
        int c = matrix[0].length,res = 0;
        int[][] f = new int[r + 1][c + 1];

        for(int i = 1; i <= r; i ++){
            for(int j = 1; j <= c; j ++){
                if(matrix[i - 1][j - 1] == '1'){
                    f[i][j] = Math.min(Math.min(f[i - 1][j - 1],f[i - 1][j]),f[i][j - 1]) + 1;
                    res = Math.max(f[i][j],res);
                }else f[i][j] = 0;
            }
        }
        return res * res;
    }
}
```

> 空间优化，dp[j] 为上方，pre 存左上方，dp[j-1]为左方。

```java
class Solution {
    public int maximalSquare(char[][] matrix) {
        int r = matrix.length;
        if(r == 0)return 0;
        int c = matrix[0].length,res = 0, pre = 0;
        int[] f = new int[c + 1];

        for(int i = 1; i <= r; i ++){
            int temp = 0;
            for(int j = 1; j <= c; j ++){
                temp = f[j];
                if(matrix[i - 1][j - 1] == '1'){
                    // 此时f[j] = f[i - 1][j],f[j - 1] = f[i][j - 1], pre = f[i - 1][j - 1];
                    f[j] = Math.min(Math.min(f[j - 1],f[j]),pre) + 1;
                    res = Math.max(res,f[j]);
                }else f[j] = 0;
                pre = temp;
            }
        }
        return res * res;
    }
}
```

