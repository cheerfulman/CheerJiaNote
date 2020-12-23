

## 1238.日志统计

小明维护着一个程序员论坛。现在他收集了一份”点赞”日志，日志共有 NN 行。

其中每一行的格式是：

```
ts id  
```

表示在 tsts 时刻编号 idid 的帖子收到一个”赞”。

现在小明想统计有哪些帖子曾经是”热帖”。

如果一个帖子曾在任意一个长度为 DD 的时间段内收到不少于 KK 个赞，小明就认为这个帖子曾是”热帖”。

具体来说，如果存在某个时刻 TT 满足该帖在 [T,T+D)[T,T+D) 这段时间内(注意是左闭右开区间)收到不少于 KK 个赞，该帖就曾是”热帖”。

给定日志，请你帮助小明统计出所有曾是”热帖”的帖子编号。

#### 输入格式

第一行包含三个整数 N,D,KN,D,K。

以下 NN 行每行一条日志，包含两个整数 tsts 和 idid。

#### 输出格式

按从小到大的顺序输出热帖 idid。

每个 idid 占一行。

#### 数据范围

1≤K≤N≤1051≤K≤N≤105,
0≤ts,id≤1050≤ts,id≤105,
1≤D≤100001≤D≤10000

#### 输入样例：

```
7 10 2
0 1
0 10
10 10
10 1
9 1
100 3
100 3
```

#### 输出样例：

```
1
3
```

```java
import java.io.BufferedReader;
import java.io.BufferedWriter;
import java.io.IOException;
import java.io.InputStreamReader;
import java.io.OutputStreamWriter;
import java.util.Arrays;
import java.util.Scanner;

public class Main {
	static Scanner sc = new Scanner(System.in);
	static BufferedReader br = new BufferedReader(new InputStreamReader(System.in));
	static BufferedWriter bw = new BufferedWriter(new OutputStreamWriter(System.out));
	public static void main(String[] args) throws IOException {
		String[] input = br.readLine().split(" ");
		int n = Integer.parseInt(input[0]);
		int d = Integer.parseInt(input[1]);
		int k = Integer.parseInt(input[2]);
		pill[] p = new pill[n + 10];
		int[] cnt = new int[100010];
		boolean[] st = new boolean[100010];
		for(int i  = 0; i < n; i++) {
			String[] s = br.readLine().split(" ");
			p[i] = new pill(Integer.parseInt(s[0]),Integer.parseInt(s[1]));
		}
		
		//按 时间排序
		Arrays.parallelSort(p,0,n);
		
		for(int i = 0, j = 0; i < n; i++) {
			int id = p[i].y;
			cnt[id] ++;
			//当 时间跨度 大于d时， 将前面的剪掉
			while(p[i].x - p[j].x >= d) {
				cnt[p[j].y] --;
				j ++;
			}
			//判断是否 是热贴
			if(cnt[id] >= k)st[id] = true;
		}
		for(int i = 0; i < 100010; i++)
			if(st[i])bw.write(i + "\n"); 
		
		bw.flush();
		bw.close();
	}

}
class pill implements Comparable<pill>{
	int x,y;
	pill(int x, int y){
		this.x = x;
		this.y = y;
	}
	@Override
	public int compareTo(pill o) {
		if(o.x == x)return y - o.y;
		else return x - o.x;
	}
}
```

## 1101.献给阿尔吉侬的花束

最简单的bfs模板

阿尔吉侬是一只聪明又慵懒的小白鼠，它最擅长的就是走各种各样的迷宫。

今天它要挑战一个非常大的迷宫，研究员们为了鼓励阿尔吉侬尽快到达终点，就在终点放了一块阿尔吉侬最喜欢的奶酪。

现在研究员们想知道，如果阿尔吉侬足够聪明，它最少需要多少时间就能吃到奶酪。

迷宫用一个 R×CR×C 的字符矩阵来表示。

字符 S 表示阿尔吉侬所在的位置，字符 E 表示奶酪所在的位置，字符 # 表示墙壁，字符 . 表示可以通行。

阿尔吉侬在 1 个单位时间内可以从当前的位置走到它上下左右四个方向上的任意一个位置，但不能走出地图边界。

#### 输入格式

第一行是一个正整数 TT，表示一共有 TT 组数据。

每一组数据的第一行包含了两个用空格分开的正整数 RR 和 CC，表示地图是一个 R×CR×C 的矩阵。

接下来的 RR 行描述了地图的具体内容，每一行包含了 CC 个字符。字符含义如题目描述中所述。保证有且仅有一个 S 和 E。

#### 输出格式

对于每一组数据，输出阿尔吉侬吃到奶酪的最少单位时间。

若阿尔吉侬无法吃到奶酪，则输出“oop!”（只输出引号里面的内容，不输出引号）。

每组数据的输出结果占一行。

#### 数据范围

1<T≤101<T≤10,
2≤R,C≤2002≤R,C≤200

#### 输入样例：

```
3
3 4
.S..
###.
..E.
3 4
.S..
.E..
....
3 4
.S..
####
..E.
```

#### 输出样例：

```
5
1
oop!
```

```java
import java.io.BufferedReader;
import java.io.BufferedWriter;
import java.io.InputStreamReader;
import java.io.OutputStreamWriter;
import java.util.ArrayDeque;
import java.util.Scanner;

public class Main {
	static BufferedReader br = new BufferedReader(new InputStreamReader(System.in));
	static BufferedWriter bw = new BufferedWriter(new OutputStreamWriter(System.out));
	static Scanner sc = new Scanner(System.in);
	static int n,m;
	static int[] dx = {-1,1,0,0},dy = {0,0,1,-1};
	
	public static void main(String[] args) {
		int T = sc.nextInt();
		
		while(T -- > 0) {
			n = sc.nextInt();m = sc.nextInt(); 
			char[][] maze = new char[n + 5][m + 5];
			int start_x = 0,start_y = 0,end_x = 0,end_y = 0;
			
			for(int i = 1; i <= n; i++ ) {
				String input = sc.next();
//				System.out.println(input);
				for(int j = 1; j <= m; j++) {
					char ch = input.charAt(j - 1);
					maze[i][j] = ch;
					if(ch == 'S') {
						start_x = i;start_y = j;
					}
					if(ch == 'E') {
						end_x = i; end_y = j;
					}
				}
			}
			
			int ans = bfs(start_x,start_y, end_x, end_y,maze);
			if(ans == -1)System.out.println("oop!");
			else System.out.println(ans);
		}

	}
	private static int bfs(int start_x,int start_y,int end_x,int end_y,char[][] maze) {
		ArrayDeque<p> q = new ArrayDeque<>();
		q.offer(new p(start_x,start_y,0));
		
		while(!q.isEmpty()) {
			p temp = q.poll();
			int x = temp.x, y = temp.y;
			if(x == end_x && y == end_y)return temp.step;
				
			for(int i = 0; i < 4; i++) {
				int new_x = x + dx[i], new_y = y + dy[i];
				if(new_x >= 1 && new_x <= n && new_y >= 1 && new_y <= m && (maze[new_x][new_y] == '.' || maze[new_x][new_y] == 'E')) {
					maze[new_x][new_y] = '#';
//					System.out.println(new_x + " " + new_y);
					q.offer(new p(new_x,new_y,temp.step + 1));
				}
			}
			
		}
		return -1;
	}

}
class p{
	int x,y;
	int step;
	p(int x, int y,int step){
		this.x = x;
		this.y = y;
		this.step = step;
	}
}
```

## 1113.红与黑

有一间长方形的房子，地上铺了红色、黑色两种颜色的正方形瓷砖。

你站在其中一块黑色的瓷砖上，只能向相邻（上下左右四个方向）的黑色瓷砖移动。

请写一个程序，计算你总共能够到达多少块黑色的瓷砖。

#### 输入格式

输入包括多个数据集合。

每个数据集合的第一行是两个整数 WW 和 HH，分别表示 xx 方向和 yy 方向瓷砖的数量。

在接下来的 HH 行中，每行包括 WW 个字符。每个字符表示一块瓷砖的颜色，规则如下

1）‘.’：黑色的瓷砖；
2）‘#’：白色的瓷砖；
3）‘@’：黑色的瓷砖，并且你站在这块瓷砖上。该字符在每个数据集合中唯一出现一次。

当在一行中读入的是两个零时，表示输入结束。

#### 输出格式

对每个数据集合，分别输出一行，显示你从初始位置出发能到达的瓷砖数(记数时包括初始位置的瓷砖)。

#### 数据范围

1≤W,H≤201≤W,H≤20

#### 输入样例：

```
6 9 
....#. 
.....# 
...... 
...... 
...... 
...... 
...... 
#@...# 
.#..#. 
0 0
```

#### 输出样例：

```
45
```

dfs搜索模板题：

```java
import java.io.BufferedReader;
import java.io.BufferedWriter;
import java.io.InputStreamReader;
import java.io.OutputStreamWriter;
import java.util.Scanner;

public class Main {
	static BufferedReader br = new BufferedReader(new InputStreamReader(System.in));
	static BufferedWriter bw = new BufferedWriter(new OutputStreamWriter(System.out));
	static Scanner sc = new Scanner(System.in);
	static int n, m, ans;
	public static void main(String[] args) {
		
		while(sc.hasNext()) {
			m = sc.nextInt(); n = sc.nextInt();
			if(m == 0 && n == 0) break;
			ans = 0;
			char[][] maze = new char[n + 10][m + 10];
			int start_x = 0,start_y = 0;
			for(int i = 0; i< n; i++) {
				String input = sc.next();
				for(int j = 0; j < m; j++) {
					char ch = input.charAt(j);
					maze[i][j] = ch;
					if(ch == '@') {
						start_x = i;start_y = j;
					}
					
				}
			}
			
			dfs(start_x,start_y,maze);
			System.out.println(ans);
		}
	}
	private static void dfs(int st_x,int st_y,char[][] maze) {
		if(st_x < 0 || st_x >= n || st_y < 0 || st_y >= m || maze[st_x][st_y] == '#')return ;
		else {
			ans ++;
			maze[st_x][st_y] = '#';
			dfs(st_x + 1,st_y,maze);
			dfs(st_x - 1,st_y,maze);
			dfs(st_x,st_y + 1,maze);
			dfs(st_x,st_y - 1,maze);
		}
	}

}
```

## 1224.交换瓶子

有 NN 个瓶子，编号 1∼N1∼N，放在架子上。

比如有 55 个瓶子：

```
2 1 3 5 4
```

要求每次拿起 22 个瓶子，交换它们的位置。

经过若干次后，使得瓶子的序号为：

```
1 2 3 4 5
```

对于这么简单的情况，显然，至少需要交换 22 次就可以复位。

如果瓶子更多呢？你可以通过编程来解决。

#### 输入格式

第一行包含一个整数 NN，表示瓶子数量。

第二行包含 NN 个整数，表示瓶子目前的排列状况。

#### 输出格式

输出一个正整数，表示至少交换多少次，才能完成排序。

#### 数据范围

1≤N≤100001≤N≤10000,

#### 输入样例1：

```
5
3 1 2 5 4
```

#### 输出样例1：

```
3
```

#### 输入样例2：

```
5
5 4 3 2 1
```

#### 输出样例2：

```
2
```

```java
import java.io.BufferedReader;
import java.io.BufferedWriter;
import java.io.InputStreamReader;
import java.io.OutputStreamWriter;
import java.util.Scanner;

public class Main {
	static BufferedReader br = new BufferedReader(new InputStreamReader(System.in));
	static BufferedWriter bw = new BufferedWriter(new OutputStreamWriter(System.out));
	static Scanner sc = new Scanner(System.in);
	static boolean[] st = new boolean[10010];
	public static void main(String[] args) {
		int n = sc.nextInt();
		int[] a = new int[n + 10];
		for(int i = 1; i <= n ;i ++)a[i] = sc.nextInt();
		int ans = 0;
		for(int i = 1; i <= n; i++) {
			if(!st[i]) {
				ans ++;
				for(int j = i; !st[j]; j = a[j]) {
					st[j] = true;
				}
			}
		}
		System.out.println(n - ans);
	}
}
```

## 1240.完全二叉树的权值

给定一棵包含 NN 个节点的完全二叉树，树上每个节点都有一个权值，按从上到下、从左到右的顺序依次是 A1,A2,⋅⋅⋅ANA1,A2,···AN，如下图所示：

![QQ截图20191205124611.png](https://cdn.acwing.com/media/article/image/2019/12/05/19_2f0cae5817-QQ%E6%88%AA%E5%9B%BE20191205124611.png)

现在小明要把相同深度的节点的权值加在一起，他想知道哪个深度的节点权值之和最大？

如果有多个深度的权值和同为最大，请你输出其中最小的深度。

注：根的深度是 11。

#### 输入格式

第一行包含一个整数 NN。

第二行包含 NN 个整数 A1,A2,⋅⋅⋅ANA1,A2,···AN。

#### 输出格式

输出一个整数代表答案。

#### 数据范围

1≤N≤1051≤N≤105,
−105≤Ai≤105−105≤Ai≤105

#### 输入样例：

```
7
1 6 5 4 3 2 1
```

#### 输出样例：

```
2
```

```java
import java.io.BufferedReader;
import java.io.BufferedWriter;
import java.io.InputStreamReader;
import java.io.OutputStreamWriter;
import java.util.Scanner;

public class Main {
	static BufferedReader br = new BufferedReader(new InputStreamReader(System.in));
	static BufferedWriter bw = new BufferedWriter(new OutputStreamWriter(System.out));
	static Scanner sc = new Scanner(System.in);
	public static void main(String[] args) {
		int n = sc.nextInt();
		int[] a = new int[n + 10];
		for(int i = 0; i < n; i++)a[i] = sc.nextInt();
		
		int  deep = 1, j = 1,ans_deep = 0,k;
		long maxx = 0 ;
		for(int i = 0; i < n; i++) {
			long temp = 0;
			for(k = i; k < n && k < i + j; k++) {
				temp += a[k];
			}
			i = k - 1;
			if(temp > maxx) {
				maxx = temp;
				ans_deep = deep;
			}
			j *= 2;
			deep++;
		}
		System.out.println(ans_deep + " " + maxx + " " + deep);
	}

}
```

## 1096.地牢大师

你现在被困在一个三维地牢中，需要找到最快脱离的出路！

地牢由若干个单位立方体组成，其中部分不含岩石障碍可以直接通过，部分包含岩石障碍无法通过。

向北，向南，向东，向西，向上或向下移动一个单元距离均需要一分钟。

你不能沿对角线移动，迷宫边界都是坚硬的岩石，你不能走出边界范围。

请问，你有可能逃脱吗？

如果可以，需要多长时间？

#### 输入格式

输入包含多组测试数据。

每组数据第一行包含三个整数 L,R,CL,R,C 分别表示地牢层数，以及每一层地牢的行数和列数。

接下来是 LL 个 RR 行 CC 列的字符矩阵，用来表示每一层地牢的具体状况。

每个字符用来描述一个地牢单元的具体状况。

其中, 充满岩石障碍的单元格用”#”表示，不含障碍的空单元格用”.”表示，你的起始位置用”S”表示，终点用”E”表示。

每一个字符矩阵后面都会包含一个空行。

当输入一行为”0 0 0”时，表示输入终止。

#### 输出格式

每组数据输出一个结果，每个结果占一行。

如果能够逃脱地牢，则输出”Escaped in x minute(s).”，其中X为逃脱所需最短时间。

如果不能逃脱地牢，则输出”Trapped!”。

#### 数据范围

1≤L,R,C≤1001≤L,R,C≤100

#### 输入样例：

```
3 4 5
S....
.###.
.##..
###.#

#####
#####
##.##
##...

#####
#####
#.###
####E

1 3 3
S##
#E#
###

0 0 0
```

#### 输出样例：

```
Escaped in 11 minute(s).
Trapped!
```

简单的三维迷宫模板题：

```java
import java.io.BufferedReader;
import java.io.BufferedWriter;
import java.io.IOException;
import java.io.InputStreamReader;
import java.io.OutputStreamWriter;
import java.util.ArrayDeque;
import java.util.Scanner;

public class Main {
	static BufferedReader br = new BufferedReader(new InputStreamReader(System.in));
	static BufferedWriter bw = new BufferedWriter(new OutputStreamWriter(System.out));
	static Scanner sc = new Scanner(System.in);
	static int l,r,c;
	static int[] dx = {-1,1,0,0,0,0},dy = {0,0,1,-1,0,0},dz = {0,0,0,0,1,-1};
	static int st_l = 0,st_r = 0,st_c = 0,end_l = 0,end_r = 0,end_c = 0;
	public static void main(String[] args) throws IOException {

		while(true) {
			String[] s = br.readLine().split(" ");
            l = Integer.valueOf(s[0]);
            r = Integer.valueOf(s[1]);
            c = Integer.valueOf(s[2]);
			if(l == 0 && r == 0 && c == 0)break;
			st_l = 0;st_r = 0;st_c = 0;end_l = 0;end_r = 0;end_c = 0;
			
			char[][][] maze = new char[l + 2][r + 2][c + 2];
			
			for(int i = 1; i <= l; i++) {
				for(int j = 1; j <= r; j++ ) {
					String input = br.readLine();
					for(int k = 1; k <= c; k++) {
						char ch = input.charAt(k - 1);
						maze[i][j][k] = ch;
						if(ch == 'S') {
							st_l = i;st_r = j; st_c = k;
						}
						if(ch == 'E') {
							end_l = i; end_r = j;end_c = k;
						}
					}
				}
				br.readLine();
			}
			
			int ans = bfs(maze);
			if(ans == -1)System.out.println("Trapped!");
			else System.out.println("Escaped in " + ans  + " minute(s).");
		}
		
	}
	private static int bfs(char[][][] maze) {
		ArrayDeque<p> q = new ArrayDeque<>();
		q.offer(new p(st_l,st_r,st_c,0));
		
		while(!q.isEmpty()) {
			p temp = q.poll();
			int x = temp.l, y = temp.r, z = temp.c;
			if(x == end_l && y == end_r && z == end_c)return temp.step;
				
			for(int i = 0; i < 6; i++) {
				int new_x = x + dx[i], new_y = y + dy[i],new_z = z + dz[i];
				if(new_x >= 1 && new_x <= l && new_y >= 1 && new_y <= r && new_z >= 1 && new_z <= c
						&& (maze[new_x][new_y][new_z] == '.' || maze[new_x][new_y][new_z] == 'E')) {
					maze[new_x][new_y][new_z] = '#';
					q.offer(new p(new_x,new_y,new_z,temp.step + 1));
				}
			}
			
		}
		return -1;
	}

}
class p{
	int l,r,c;
	int step;
	p(int l, int r,int c,int step){
		this.l = l;
		this.r = r;
		this.c = c;
		this.step = step;
	}
}
```

## 1233.全球变暖

你有一张某海域 N×NN×N 像素的照片，”.”表示海洋、”#”表示陆地，如下所示：

```
.......
.##....
.##....
....##.
..####.
...###.
.......
```

其中”上下左右”四个方向上连在一起的一片陆地组成一座岛屿，例如上图就有 22 座岛屿。

由于全球变暖导致了海面上升，科学家预测未来几十年，岛屿边缘一个像素的范围会被海水淹没。

具体来说如果一块陆地像素与海洋相邻(上下左右四个相邻像素中有海洋)，它就会被淹没。

例如上图中的海域未来会变成如下样子：

```
.......
.......
.......
.......
....#..
.......
.......
```

请你计算：依照科学家的预测，照片中有多少岛屿会被完全淹没。

#### 输入格式

第一行包含一个整数N。

以下 NN 行 NN 列，包含一个由字符”#”和”.”构成的 N×NN×N 字符矩阵，代表一张海域照片，”#”表示陆地，”.”表示海洋。

照片保证第 11 行、第 11 列、第 NN 行、第 NN 列的像素都是海洋。

#### 输出格式

一个整数表示答案。

#### 数据范围

1≤N≤10001≤N≤1000

#### 输入样例1：

```
7
.......
.##....
.##....
....##.
..####.
...###.
.......
```

#### 输出样例1：

```
1
```

#### 输入样例2：

```
9
.........
.##.##...
.#####...
.##.##...
.........
.##.#....
.#.###...
.#..#....
.........
```

#### 输出样例2：

```
1
```

如果被淹没，则边缘陆地等于总陆地面积；

```java
import java.io.BufferedReader;
import java.io.BufferedWriter;
import java.io.IOException;
import java.io.InputStreamReader;
import java.io.OutputStreamWriter;
import java.util.Scanner;

public class Main {
	static BufferedReader br = new BufferedReader(new InputStreamReader(System.in));
	static BufferedWriter bw = new BufferedWriter(new OutputStreamWriter(System.out));
	static Scanner sc = new Scanner(System.in);
	static int total = 0, bound = 0,n;
	static boolean[][] st;
	static int[] dx = {-1,1,0,0}, dy = {0,0,1,-1};
	static p[] q = new p[1010 * 1010 + 10];
	public static void main(String[] args) throws IOException {
		n = Integer.parseInt(br.readLine());
		char[][] maze = new char[n + 1][n + 1];
		st = new boolean[n + 1][n + 1];
		
        for(int i = 0;i < n;i++)
        {
            char[] ss = br.readLine().toCharArray();
//            char[] ss = sc.next().toCharArray();
            for(int j = 0;j < n;j++)
            {
                maze[i][j] = ss[j];
            }
        }
		
		int ans = 0;
		for(int i = 0; i < n; i++) {
			for(int j = 0; j < n; j++) {
				if(maze[i][j] == '#' && !st[i][j]) {
					total = 0;bound = 0;
					bfs(i,j,maze);
					if(total == bound) ans ++;
				}
			}	
		}
		System.out.println(ans);
				
		
	}
	private static void bfs(int x, int y,char[][] maze) {
		int hh = 0, tt = 0;
		q[0] = new p(x,y);
		st[x][y] = true;
		while(hh <= tt) {
			p temp = q[hh ++];
			total ++;
			boolean is_bound = false;
			for(int i = 0; i < 4; i++) {
				int new_x = temp.x + dx[i], new_y = temp.y + dy[i];
				if(new_x >= 0 && new_x < n && new_y >= 0 && new_y < n && !st[new_x][new_y] 
						&& maze[new_x][new_y] == '#') {
					st[new_x][new_y] = true;
					q[++tt] = new p(new_x,new_y);
				}
				
				if(maze[new_x][new_y] == '.')is_bound = true;
			}
			if(is_bound) bound ++;
			
		}
		
	}
	static class p{
		int x,y;
		p(int x,int y){
			this.x = x;
			this.y = y;
		}
	}
}
```

## 1207.大臣的旅行

很久以前，T王国空前繁荣。

为了更好地管理国家，王国修建了大量的快速路，用于连接首都和王国内的各大城市。

为节省经费，T国的大臣们经过思考，制定了一套优秀的修建方案，使得任何一个大城市都能从首都直接或者通过其他大城市间接到达。

同时，如果不重复经过大城市，从首都到达每个大城市的方案都是唯一的。

J是T国重要大臣，他巡查于各大城市之间，体察民情。

所以，从一个城市马不停蹄地到另一个城市成了J最常做的事情。

他有一个钱袋，用于存放往来城市间的路费。

聪明的J发现，如果不在某个城市停下来修整，在连续行进过程中，他所花的路费与他已走过的距离有关，在走第x千米到第x+1千米这一千米中（x是整数），他花费的路费是x+10这么多。也就是说走1千米花费11，走2千米要花费23。

J大臣想知道：他从某一个城市出发，中间不休息，到达另一个城市，所有可能花费的路费中最多是多少呢？

#### 输入格式

输入的第一行包含一个整数 nn，表示包括首都在内的T王国的城市数。

城市从 11 开始依次编号，11 号城市为首都。

接下来 n−1n−1 行，描述T国的高速路（T国的高速路一定是 n−1n−1 条）。

每行三个整数 Pi,Qi,DiPi,Qi,Di，表示城市 PiPi 和城市 QiQi 之间有一条**双向**高速路，长度为 DiDi 千米。

#### 输出格式

输出一个整数，表示大臣J最多花费的路费是多少。

#### 数据范围

1≤n≤1051≤n≤105,
1≤Pi,Qi≤n1≤Pi,Qi≤n,
1≤Di≤10001≤Di≤1000

#### 输入样例：

```
5 
1  2  2 
1  3  1 
2  4  5 
2  5  4 
```

#### 输出样例：

```
135
```

此题求最长的一条路相当于求树的直径。

两遍dfs，第一遍，求出任意一点到其它的的最远距离u点。

再用u点，求出最远的点。即为路径。

```java
import java.io.BufferedReader;
import java.io.BufferedWriter;
import java.io.IOException;
import java.io.InputStreamReader;
import java.io.OutputStreamWriter;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.Scanner;

public class Main {
	static class Node{
		int id,distance;
		Node(int id,int distance){
            this.id = id;
            this.distance = distance;
        }
	}
	
	static BufferedReader br = new BufferedReader(new InputStreamReader(System.in));
	static BufferedWriter bw = new BufferedWriter(new OutputStreamWriter(System.out));
	static Scanner sc = new Scanner(System.in);
	static ArrayList[] list = new ArrayList[100010];
	static int[] dis = new int[100010];
	public static void main(String[] args) throws NumberFormatException, IOException {
		int n = Integer.parseInt(br.readLine().trim());
		for(int i = 0;i < 100010 ;i ++) list[i] = new ArrayList();
		for(int i = 1; i < n; i++) {
			String [] s = br.readLine().split(" ");
			
			int a = Integer.parseInt(s[0].trim());
			int b = Integer.parseInt(s[1].trim());
			int c = Integer.parseInt(s[2].trim());
			list[a].add(new Node(b, c));
			list[b].add(new Node(a,c));
		}
		
		dfs(1,-1,0);
		int u = 1;
		for(int i = 1; i <= n; i ++) {
			if(dis[u] < dis[i])u = i;
		}
		Arrays.fill(dis, 0);
		
		dfs(u,-1,0);
		u = 0;
		for(int i = 1; i <= n; i ++) {
			if(dis[u] < dis[i])u = i;
		}
		int s = dis[u];
		
		System.out.println(s * 10 + (s * (s + 1L)) / 2 );
		
	}
	private static void dfs(int u,int f, int dist) {
		dis[u] = dist;
		for(Object obj : list[u]) {
			Node node = (Node) obj;
            //不走重复的路
			if(node.id != f)
				dfs(node.id,u,node.distance + dist);
		}
	}

}
```

## 826.单链表

实现一个单链表，链表初始为空，支持三种操作：

(1) 向链表头插入一个数；

(2) 删除第k个插入的数后面的数；

(3) 在第k个插入的数后插入一个数

现在要对该链表进行M次操作，进行完所有操作后，从头到尾输出整个链表。

**注意**:题目中第k个插入的数并不是指当前链表的第k个数。例如操作过程中一共插入了n个数，则按照插入的时间顺序，这n个数依次为：第1个插入的数，第2个插入的数，…第n个插入的数。

#### 输入格式

第一行包含整数M，表示操作次数。

接下来M行，每行包含一个操作命令，操作命令可能为以下几种：

(1) “H x”，表示向链表头插入一个数x。

(2) “D k”，表示删除第k个输入的数后面的数（当k为0时，表示删除头结点）。

(3) “I k x”，表示在第k个输入的数后面插入一个数x（此操作中k均大于0）。

#### 输出格式

共一行，将整个链表从头到尾输出。

#### 数据范围

1≤M≤1000001≤M≤100000
所有操作保证合法。

#### 输入样例：

```
10
H 9
I 1 1
D 1
D 0
H 6
I 3 6
I 4 5
I 4 5
I 3 4
D 6
```

#### 输出样例：

```
6 4 6 5
```

用数组模拟单链表。

```java
import java.util.Scanner;

public class Main {
	static Scanner sc = new Scanner(System.in);
	static final int N = 100010;
	// e 存值， ne 存next， idx代表 数组中的位置
	static int[] e = new int[N], ne = new int[N];
	static int head = -1, idx = 0;
	
	public static void main(String[] args) {
		int op = sc.nextInt();
		while(op -- > 0) {
			char ch = sc.next().charAt(0);
			if(ch == 'H') {
				int x = sc.nextInt();
				add_head(x);
			}else if(ch == 'I') {
				int k = sc.nextInt(), v = sc.nextInt();
				insert(k - 1, v);
			}else {
				int k = sc.nextInt();
				if(k == 0)head = ne[head];
				else delete(k - 1);
				
			}
//			System.out.println("\n" + ch);
		}
		for(int i = head; i != -1; i = ne[i])System.out.print(e[i] + " ");
	}
	// 在首部插入
	private static void add_head(int x) {
		// 将值 存入e数组
		e[idx] = x;
		// 指向head.next (head的值 为 head.next的下标)
		ne[idx] = head;
		// 将head指向 当前值
		head = idx;
		idx ++;
	}
	private static void insert(int k, int v) {
		e[idx] = v;
		// 此方法表示要插入在k后面，故 指向 k.next
		ne[idx] = ne[k];
		// k.next = idx；
		ne[k] = idx++;
	}
	// 直接删除， 舍弃 在ne[k]这个位置的 空间
	private static void delete(int k) {
		ne[k] = ne[ne[k]];
	}

}
```

