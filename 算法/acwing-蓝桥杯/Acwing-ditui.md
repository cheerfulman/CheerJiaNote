Acwing-95-费解的开关
---
你玩过“拉灯”游戏吗？25盏灯排成一个5x5的方形。每一个灯都有一个开关，游戏者可以改变它的状态。每一步，游戏者可以改变某一个灯的状态。游戏者改变一个灯的状态会产生连锁反应：和这个灯上下左右相邻的灯也要相应地改变其状态。

我们用数字“1”表示一盏开着的灯，用数字“0”表示关着的灯。下面这种状态

10111
01101
10111
10000
11011
在改变了最左上角的灯的状态后将变成：

01111
11101
10111
10000
11011
再改变它正中间的灯后状态将变成：

01111
11001
11001
10100
11011
给定一些游戏的初始状态，编写程序判断游戏者是否可能在6步以内使所有的灯都变亮。

**输入格式**
第一行输入正整数n，代表数据中共有n个待解决的游戏初始状态。

以下若干行数据分为n组，每组数据有5行，每行5个字符。每组数据描述了一个游戏的初始状态。各组数据间用一个空行分隔。

**输出格式**
一共输出n行数据，每行有一个小于等于6的整数，它表示对于输入数据中对应的游戏状态最少需要几步才能使所有灯变亮。

对于某一个游戏初始状态，若6步以内无法使所有灯变亮，则输出“-1”。

**数据范围**
> 0<n≤500

**输入样例：**
3
00111
01011
10001
11010
11100

11101
11101
11110
11111
11111

01111
11111
11111
11111
11111
**输出样例：**

3
2
-1

尽量用最小的步数，打开所有的开关；如果大于6步就输出-1；
此题最大的核心就是，当你处于第二排的时候，如果第一排有灯是关着的，那么你必须在此灯的正下方，进行操作才可以关掉上面的灯。
故：当你第一排的灯操作完毕之后，从第二排开始，每一个操作都是固定死的了；
也就是说 如果a[i][j]是关的，则必须操作a[i+1][j]将其打开。


所以我们可以枚举所有第一排可能的开关灯情况，可用递归实现第一排的指数型枚举，然后判断2-5排的操作，有没有成功的；
```java
import java.io.BufferedInputStream;
import java.io.BufferedWriter;
import java.io.IOException;
import java.io.InputStream;
import java.io.OutputStreamWriter;
import java.util.Arrays;
import java.util.Scanner;

public class Main {
	static BufferedWriter out = new BufferedWriter(new OutputStreamWriter(System.out));
	static Scanner in = new Scanner(new BufferedInputStream(System.in));
	static int n ,ans;
	static int used[] = new int[10];
	static int[][] map = new int[10][10];
	static int[][] map1 = new int[10][10];
	public static void main(String[] args) throws Exception {
		n = in.nextInt();
		while(n-- > 0) {
			ans = 99;
			for(int i = 1; i <= 5; i++) {
				String k = in.next();
				for(int j = 1; j <= 5; j++) {
					map1[i][j] = k.charAt(j-1) - '0';
				}
			}
			dfs(1);
			if(ans > 6)out.write("-1\n");
			
			else out.write(ans + "\n");
			out.flush();
		}
	}
	static void dfs(int u) {
		if(u > 5) {
			for(int i = 1; i <= 5; i++) {
				for(int j = 1; j <= 5; j++) {
					map[i][j] = map1[i][j];
				}
			}
			int step = 0;
			for(int i = 1; i <= 5; i++) {
				if(used[i] == 1) {
					step ++;
					map[1][i] ^= 1;
					map[1][i-1] ^= 1;
					map[1][i+1] ^= 1;
					map[2][i] ^= 1;
				}
			}
			for(int i = 2; i <= 5; i++) {
				for(int j = 1; j <= 5; j++) {
					if(step > 6)return;
					if(map[i-1][j] == 0) {
						step ++;
						map[i][j] ^= 1;
						map[i-1][j] ^= 1;
						map[i][j+1] ^= 1;
						map[i][j-1] ^= 1;
						map[i+1][j] ^= 1;
					}
				}
			}
			if(step < ans) {
				for(int j = 1; j <= 5; j++) {
					if(map[5][j] != 1)return ;
				}
				ans = step;
			}
			return ;
		}
		used[u] = 1;
		dfs(u+1);
		used[u] = 0;
		dfs(u+1);
	}
}

```

利用位运算存储的做法，本质：将其代替 boolean used[]的标记；
```java
import java.io.BufferedInputStream;
import java.io.BufferedWriter;
import java.io.IOException;
import java.io.InputStream;
import java.io.OutputStreamWriter;
import java.util.Arrays;
import java.util.Scanner;



public class Main {
	static BufferedWriter out = new BufferedWriter(new OutputStreamWriter(System.out));
	static Scanner in = new Scanner(new BufferedInputStream(System.in));
	static int[][] map = new int[10][10];
	static int[][] tmp = new int[10][10];
	public static void main(String[] args) throws Exception {
		
		int n = in.nextInt();
		while(n-- > 0) {
			for(int i = 0; i < 5; i++) {
				String k = in.next();
				for(int j = 0; j < 5; j++) {
					map[i][j] = k.charAt(j) - '0';
				}
			}
			
			int ans = 99;
			for(int state = 0; state < 32; state++) {
				int step = 0;
				for(int i = 0; i < 5; i++)System.arraycopy(map[i], 0, tmp[i], 0, map[i].length);
				
				for(int i = 0; i < 5; i++) {
					if((state >> i & 1) == 1) {
						step ++;
						turn(0,i);
					}
				}
				
				for(int i = 1; i < 5; i++) {
					for(int j = 0; j < 5; j++) {
						if(tmp[i-1][j] == 0) {
							step ++;
							turn(i,j);
						}
					}
				}
				boolean flag = true;
				for(int i = 0; i < 5; i++)
					if(tmp[4][i] != 1) {
						flag = false;
						break;
					}
				if(flag) ans = Math.min(ans, step);
			}
			if(ans > 6)ans = -1;
			out.write(ans + "\n");
			out.flush();
		}
		
	}
	static void turn(int x, int y) {
		int[] dx = {0,0,0,1,-1};
		int[] dy = {0,-1,1,0,0};
		for(int i = 0; i < 5; i++) {
			int tx = x + dx[i];
			int ty = y + dy[i];
			if(tx >= 0 && tx < 5 && ty >= 0 && ty < 5)
			tmp[tx][ty] ^= 1;
		}
	}
}
```

Acwing-1208-翻硬币
---
小明正在玩一个“翻硬币”的游戏。

桌上放着排成一排的若干硬币。我们用 * 表示正面，用 o 表示反面（是小写字母，不是零）。

比如，可能情形是：**oo***oooo

如果同时翻转左边的两个硬币，则变为：oooo***oooo

现在小明的问题是：如果已知了初始状态和要达到的目标状态，每次只能同时翻转相邻的两个硬币,那么对特定的局面，最少要翻动多少次呢？

我们约定：把翻动相邻的两个硬币叫做一步操作。

**输入格式**
两行等长的字符串，分别表示初始状态和要达到的目标状态。

**输出格式**
一个整数，表示最小操作步数

**数据范围**
输入字符串的长度均不超过100。
数据保证答案一定有解。

**输入样例1：**
**********
o****o****
**输出样例1：**
5
**输入样例2：**
*o**o***o***
*o***o**o***
**输出样例2：**
1
```java
import java.io.BufferedInputStream;
import java.io.BufferedWriter;
import java.io.OutputStreamWriter;
import java.util.Scanner;

public class Main {
	static BufferedWriter out = new BufferedWriter(new OutputStreamWriter(System.out));
	static Scanner in = new Scanner(new BufferedInputStream(System.in));
	public static void main(String[] args) {
		String text1 = in.next();
		String des1 = in.next();
		char[] text = text1.toCharArray();
		char[] des = des1.toCharArray();
		int step = 0;
		for(int i = 0; i < text.length; i++) {
			if(text[i] != des[i]) {
				step ++;
				if(text[i] == 'o')text[i] = '*';
				else text[i] = 'o';
				
				if(text[i+1] == 'o')text[i+1] = '*';
				else text[i+1] = 'o';
			}
		}
		System.out.println(step);
	}
}
```

Acwing-116-飞行员兄弟
---
飞行员兄弟”这个游戏，需要玩家顺利的打开一个拥有16个把手的冰箱。

已知每个把手可以处于以下两种状态之一：打开或关闭。

只有当所有把手都打开时，冰箱才会打开。

把手可以表示为一个4х4的矩阵，您可以改变任何一个位置[i,j]上把手的状态。

但是，这也会使得第i行和第j列上的所有把手的状态也随着改变。

请你求出打开冰箱所需的切换把手的次数最小值是多少。

**输入格式**
输入一共包含四行，每行包含四个把手的初始状态。

符号“+”表示把手处于闭合状态，而符号“-”表示把手处于打开状态。

至少一个手柄的初始状态是关闭的。

**输出格式**
第一行输出一个整数N，表示所需的最小切换把手次数。

接下来N行描述切换顺序，每行输入两个整数，代表被切换状态的把手的行号和列号，数字之间用空格隔开。

注意：如果存在多种打开冰箱的方式，则按照优先级整体从上到下，同行从左到右打开。

**数据范围**
1≤i,j≤4
**输入样例：**
-+--
----
----
-+--
**输出样例：**
6
1 1
1 3
1 4
4 1
4 3
4 4


## 飞行员兄弟两种解法



解法一:
---
1. 此题同一个开关按一下即可，多按无用

利用此特证，枚举每一个开关是否被按即可；
普通的暴力判断:利用计算机内部二进制存储的特点，将每一位，代表4*4矩阵的一个(0,1)型特征，枚举每一种开关状态的情况，选取最小值。

```java
import java.io.BufferedInputStream;
import java.io.BufferedOutputStream;
import java.io.BufferedWriter;
import java.io.IOException;
import java.io.OutputStreamWriter;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;
import java.util.Map.Entry;
import java.util.Scanner;
class pair{
	int x,y;
	pair(int x,int y){
		this.x = x;
		this.y = y;
	}
}
public class Main {
	static BufferedWriter out = new BufferedWriter(new OutputStreamWriter(System.out));
	static Scanner in = new Scanner(new BufferedInputStream(System.in));
	static char[][] matrix = new char[5][5];
	static char[][] ma = new char[5][5];
	static final int inf = 0x3f3f3f3f;
	public static void main(String[] args) throws Exception {
		List<pair> list = new ArrayList<pair>();
		List<pair> list1 = new ArrayList<pair>();
		for(int i = 0; i < 4; i++) {
			String k = in.next();
			for(int j = 0; j < 4; j++) {
				matrix[i][j] = k.charAt(j);
			}
		}
		int ans = inf;
		for(long op = 0; op < (1 << 16); op++) {
			int step = 0;
			for(int i = 0; i < 4; i++)System.arraycopy(matrix[i], 0, ma[i], 0, matrix[i].length);
			list.clear();
			for(int i = 0; i < 4; i++) {
				for(int j = 0; j < 4; j++) {
					if((op >> ((i)*4+(j)) & 1) == 1) {
						list.add(new pair(i+1,j+1));
						step ++;
						turn(i,j);
					}
				}
			}
			boolean flag = true;
			for(int i = 0; i < 4; i++) {
				if(flag == false)break;
				for(int j = 0; j < 4; j++) {
					if(ma[i][j] != '-') {
						flag = false;
						break;
					}
				}
			}
			if(flag && step < ans) {
			    list1.clear();
				ans = step;
				for(pair p : list)list1.add(p);
			}
		}
		
		out.write(ans + "\n");
		for(pair p : list1) {
			out.write(p.x + " " + p.y + "\n");
		}
		out.flush();
	}
	static void turn(int x, int y) {
		if(ma[x][y] == '+')ma[x][y] = '-';
		else ma[x][y] = '+';
		for(int i = 0; i < 4; i++) {
			if(ma[x][i] == '+')ma[x][i] = '-';
			else ma[x][i] = '+';
			
			if(ma[i][y] == '+')ma[i][y] = '-';
			else ma[i][y] = '+';
		}
		
	}
}
```



解法二:
---

位运算的预处理：详情见代码
巧妙处：利用位运算的特征，将所有开关的操作事先存在一个二维数组中，这样就节省，每一次按下时翻转的时间（跟打表类似）
```java
import java.io.BufferedInputStream;
import java.io.BufferedOutputStream;
import java.io.BufferedWriter;
import java.io.IOException;
import java.io.OutputStreamWriter;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;
import java.util.Map.Entry;
import java.util.Scanner;
class pair{
	int x,y;
	pair(int x,int y){
		this.x = x;
		this.y = y;
	}
}
public class Main {
	static BufferedWriter out = new BufferedWriter(new OutputStreamWriter(System.out));
	static Scanner in = new Scanner(new BufferedInputStream(System.in));
	static int state = 0;//代表4*4的矩阵，一共16位，每位开关1（打开），0（关闭）
	static int[][] change = new int[5][5];
	public static void main(String[] args) throws IOException, CloneNotSupportedException {
		
		for(int i = 0; i < 4; i++) {
			String k = in.next();
			for(int j = 0; j < 4 ; j++) {
				if(k.charAt(j) == '+') {
					state += 1 << (i*4+j);
				}
				Change(i,j);       //预处理，存上 要异或的值，与此值异或，相当于对该行该列取反，完成操作；
			}
		}
		
		List<pair> ans = null;//因为其一定有答案，故可以赋值为Null;
		for(int op = 0; op < 1 << 16; op++) {
			List<pair> tmp = new ArrayList<pair>();
			int now = state;
			for(int i = 0; i < 16; i++) {
				if((op >> i & 1) == 1) {
					int x = i / 4; int y = i % 4;
					now ^= change[x][y];
					tmp.add(new pair(x+1,y+1));
				}
			}
			if(now == 0 && (ans == null || tmp.size() < ans.size()))ans = tmp;
		}
		
		out.write(ans.size() + "\n");
		for(pair a : ans)out.write(a.x + " " + a.y + "\n");
		out.flush();
	}
	
	/**
	 * change[x][y]代表在位置(x,y)上的点,要异或的值
	 * 例如 0^0,1^0都等于其本身， 0^1,1^1都为取反；
	 * 故我们在change[x][y]上的值 为 x行上,y列上的只全部为1，其他16-7=9的位置全部为0，做异或。
	 * 异或后x行和y列的值就全部取反，其他不变了。
	 * @param x 行
	 * @param y 列
	 */
	static void Change(int x,int y) {
		for(int i = 0; i < 4; i++) {
			change[x][y] += (1 << x*4+i) + (1 << i*4 + y);
		}
		change[x][y] -= (1 << x*4+y);
	}
}
```