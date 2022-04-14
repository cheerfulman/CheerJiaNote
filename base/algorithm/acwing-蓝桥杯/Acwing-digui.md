
## 简单递归题
---
Acwing-92.递归实现指数型枚举
---
从 1~n 这 n 个整数中随机选取任意多个，输出所有可能的选择方案。

**输入格式**
输入一个整数n。

**输出格式**
每行输出一种方案。

同一行内的数必须升序排列，相邻两个数用恰好1个空格隔开。

对于没有选任何数的方案，输出空行。

本题有自定义校验器（SPJ），各行（不同方案）之间的顺序任意。

**数据范围**
> 1≤n≤15

**输入样例：**
> 3

**输出样例：**

> 3
2
2 3
1
1 3
1 2
1 2 3

详细解析：
递归大体就是：
1. 出递归条件；
2. 内容
3. 反复调用

先上代码
```java
import java.util.Scanner;
public class Main{
	private static final int N = 20;
	private static int[] book = new int[N];
	private static int n;
	public static void main(String[] args) throws Exception {
			Scanner sc = new Scanner(System.in);
			n = sc.nextInt();
			//System.out.println(fn(fi));
			dfs(1);
	}
	
	private static void dfs(int u) {
		if(u > n) {//出递归条件
			for(int i = 1; i <= n; i++) {//输出
				if(book[i] == 1)
				System.out.printf("%d ",i);
			}
			System.out.println();
			return;
		}
		
		book[u] = 1;//该数选不选，选了的标记为已选
		dfs(u + 1);
		book[u] = 0;//恢复上一次的状态
		dfs(u + 1);
		
	}
}

```
总题来说是一个树形结构，表示当前的数选或不选。
该题思路递归思路: u当前的数，选了则标记，当达到条件，则输出；


Acwing-94递归实现排列型枚举
---
把 1~n 这 n 个整数排成一行后随机打乱顺序，输出所有可能的次序。

**输入格式**
一个整数n。

**输出格式**
按照从小到大的顺序输出所有方案，每行1个。

首先，同一行相邻两个数用一个空格隔开。

其次，对于两个不同的行，对应下标的数一一比较，字典序较小的排在前面。

**数据范围**
1≤n≤9
**输入样例：**
3
**输出样例：**
1 2 3
1 3 2
2 1 3
2 3 1
3 1 2
3 2 1

题解:此题就说一个全排列，同上
```java
import java.io.BufferedInputStream;
import java.io.BufferedReader;
import java.io.BufferedWriter;
import java.io.InputStreamReader;
import java.io.OutputStreamWriter;
import java.io.PrintWriter;
import java.io.StreamTokenizer;
import java.util.*;

public class Main {
	private static int n;
	private static boolean[] book = new boolean[10];
	private static int[] state = new int[10];
	//static PrintWriter writer = new PrintWriter(new OutputStreamWriter(System.out));
	static StreamTokenizer in = new StreamTokenizer(new BufferedReader(new InputStreamReader(System.in)));
    static PrintWriter out = new PrintWriter(new OutputStreamWriter(System.out));

	public static void main(String[] args) throws Exception {
//		Scanner sc = new Scanner(new BufferedInputStream(System.in));
		//System.out.println(1234);
		while(in.nextToken() != StreamTokenizer.TT_EOF) {
			//System.out.println(123);
			n = (int)in.nval;
			//System.out.println(n);
			//in.nextToken();
			//System.out.println(n);
			dfs(1);
			
			out.flush();
		}
		
//		n = sc.nextInt();
		
		//writer.flush();
		
		
	}
	private static void dfs(int u) throws Exception{
		if(u > n) {
			for(int i = 1; i <= n; i++) {
//				writer.write(state[i] + " ");
				out.print(state[i] + " ");
			}
			out.println();
			return ;
		}
		for(int i = 1; i <= n; i++) {
			if(!book[i]) {
				state[u] = i;
				book[i] = true;
				dfs(u + 1);
				
				book[i] = false;
				//state[u] = 0;
			}
		}
	}

}

```
Acwing - 93. 递归实现组合型枚举
---
从 1~n 这 n 个整数中随机选出 m 个，输出所有可能的选择方案。

**输入格式**
两个整数 n,m ,在同一行用空格隔开。

**输出格式**
按照从小到大的顺序输出所有方案，每行1个。

首先，同一行内的数升序排列，相邻两个数用一个空格隔开。

其次，对于两个不同的行，对应下标的数一一比较，字典序较小的排在前面（例如1 3 5 7排在1 3 6 8前面）。

**数据范围**
n>0 ,
0≤m≤n ,
n+(n−m)≤25
**输入样例：**
5 3
**输出样例：**
1 2 3 
1 2 4 
1 2 5 
1 3 4 
1 3 5 
1 4 5 
2 3 4 
2 3 5 
2 4 5 
3 4 5 

从几个数中，选几个数进行全排列
state代表（n）位二进制的数，当他第x位为1时，代表x被选了
```java
import java.io.BufferedInputStream;
import java.io.BufferedWriter;
import java.io.IOException;
import java.io.OutputStream;
import java.io.OutputStreamWriter;
import java.util.HashSet;
import java.util.Scanner;
import java.util.Set;

public class Main {
	static BufferedWriter writer = new BufferedWriter(new OutputStreamWriter(System.out));
	static Scanner sc = new Scanner(new BufferedInputStream(System.in));
	
	static int n = sc.nextInt();
	static int m = sc.nextInt();
	static boolean[] used = new boolean[30];
	//static int[] state = new int[30];
	public static void main(String[] args) throws Exception{
		dfs(0,0,0);
		writer.flush();
	}
	
	private static void dfs(int u,int sum,int state) throws Exception {
		if(n - u + sum < m)return ;
		if(sum == m) {
			for(int i = 1; i <= n; i++) {
				if((state >> (i - 1) & 1) == 1)writer.write(i + " ");
			}
			writer.write("\n");
			return ;
		}
		dfs(u+1,sum+1,state | 1 << u);
		dfs(u+1,sum,state);
	}
}
```
Acwing-1209. 带分数
---
100  可以表示为带分数的形式：100=3+69258714
还可以表示为：100=82+3546197
注意特征：带分数中，数字 1∼9 分别出现且只出现一次（不包含 0）。

类似这样的带分数，100 有 11 种表示法。

**输入格式**
一个正整数。

**输出格式**
输出输入数字用数码 1∼9 不重复不遗漏地组成带分数表示的全部种数。

**数据范围**
1≤N<106
**输入样例1：**
100
**输出样例1：**
11
**输入样例2：**
105
**输出样例2：**
6

此题，把它想象成是9位数的一个全排列，然后再在其中 用两块板子分割开来，表示b,c；
也就是说n = a + b/c；
全排列后，再枚举板子在哪；
这里有剪支；
```java
import java.io.BufferedInputStream;
import java.io.BufferedWriter;
import java.io.IOException;
import java.io.OutputStreamWriter;
import java.util.Arrays;
import java.util.Scanner;

public class Main {
	static Scanner in = new Scanner(new BufferedInputStream(System.in));
	static BufferedWriter out = new BufferedWriter(new OutputStreamWriter(System.out));
	static int n ,ans = 0;
	static boolean[] used = new boolean[15];
	public static void main(String[] args) throws Exception {
		n = in.nextInt();
		//out.write(String.valueOf(ans));
		dfs_a(1,0);
		out.write(String.valueOf(ans));
		
		out.flush();
	}
	static void dfs_a(int u,int a) {
		if(a >= n || u > 9)return ;
		if(a > 0)dfs_c(u,a,0);
		for(int i = 1; i <= 9; i++) {
			if(!used[i]) {
				used[i] = true;
				dfs_a(u + 1, a * 10 + i);
				used[i] = false;
			}
		}
	}
	static void dfs_c(int u,int a,int c) {
		if(u > 9)return ;
		if(check(a,c)) {
			ans++;
			return ;
		}
		for(int i = 1; i <= 9; i++) {
			if(!used[i]) {
				used[i] = true;
				dfs_c(u + 1,a,c*10+i);
				used[i] = false;
			}
		}
	}
	static boolean check(int a,int c) {
		long b = n*(long)c - a*c;
		if(b==0 || a==0 || c == 0)return false;
		boolean[] Newc = Arrays.copyOf(used,used.length);
		while(b > 0) {
			int temp = (int) (b % 10);
			b /= 10;
			if(temp == 0 || Newc[temp])return false;
			Newc[temp] = true;
		}
		
		for(int i = 1; i <= 9; i++) {
			if(!Newc[i])return false;
		}
		return true;
	}
	
}

```