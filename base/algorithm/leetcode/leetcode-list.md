# leetcode-链表

## leetcode-141-环形链表

题目大意：给定一个链表，判断该链表是存在环；

1、快慢指针

- 设定一个快指针，一个慢指针。
- 若有环，且慢指针在环外，快指针出不去，将一直在环中循环，直到慢指针入环，被快指针追上，相遇；
- 若无环，快指针将到达链表尾部；

```java
public class Solution {
    public boolean hasCycle(ListNode head) {
        if(head == null || head.next == null)return false;
        ListNode fast = head;
        ListNode slow = head;
        while(fast != null && fast.next != null){
            fast = fast.next.next;
            slow = slow.next;
            if(fast == slow)return true;
        }
        return false;
    }
}
```

2、哈希 set

- 若有环，环内节点会走到两次或以上；

```java
public class Solution {
    public boolean hasCycle(ListNode head) {
        if(head == null || head.next == null)return false;
        Set<ListNode> set = new HashSet<>();
        while(head != null){
            if(set.add(head));
            else return true;
            head = head.next;
        }
        return false;
    }
}
```

## leetcode-160-相交链表

题目大意：找到两个链表的相交的起始节点
![如下图节点](https://assets.leetcode-cn.com/aliyun-lc-upload/uploads/2018/12/14/160_statement.png)
此节点为 c1;

1、 暴力解法

```java
public class Solution {
    public ListNode getIntersectionNode(ListNode headA, ListNode headB) {
        if(headA == null || headB == null)return null;
        for(ListNode la = headA; la != null; la = la.next){
            for(ListNode lb = headB; lb != null; lb = lb.next){
                if(la == lb)return la;
            }
        }
        return null;
    }
}
```

2、 双指针

- a = a 非共同部分的长度,
- b = b 非共同部分的长度
- all =共同部分的长度
- 故 a+b+all = b+a=all
- 则可用双指针，当指针 a 循环完 a 链表则调到 b 链表首，当 a 第二次经过共同部分时，b 肯定也恰好到达共同部分；

```java
public class Solution {
    public ListNode getIntersectionNode(ListNode headA, ListNode headB) {
        if(headA == null || headB == null)return null;
        ListNode la = headA,lb = headB;
        boolean f = false;
        while(la != null){
            if(la == lb)return la;
            la = la.next;
            lb = lb.next;
            if(la == null && !f){
                f = !f;
                la = headB;
            }
            if(lb == null)lb = headA;
        }
        return null;
    }
}
```

## leetcode-203-移除链表

题意：删除链表中的一个值;

ps:我的思路简单循环即可,但是自己的代码很蠢;

```java
class Solution {
    public ListNode removeElements(ListNode head, int val) {
        while(head != null && head.val == val )head = head.next;
        if(head == null)return null;
        ListNode q, pre = head;//可以只用p.next省略掉麻烦的pre;
        if(head.next != null) q = head.next;
        else return head;
        while(q != null){
            if(q.val == val){//如果此节点要删除，则pre节点不动;
                pre.next = q.next;
                q = q.next;
            }
            else{
                pre = q;
                q = q.next;
            }
        }
        return head;
    }
}
```

用 q.next 代替 q, q 代替 pre;少写一个 pre，代码简介易懂，还不麻烦;

```java
class Solution {
    public ListNode removeElements(ListNode head, int val) {
        while(head != null && head.val == val )head = head.next;
        if(head == null)return null;
        ListNode q = head;
        while(q.next != null){
            if(q.next.val == val){
                q.next = q.next.next;
            }
            else{
                q = q.next;
            }
        }
        return head;
    }
}
```

递归做法，现在对递归的掌握还没熟练的应用进各个习题，对每个题目的第一个反应就是暴力，自己很愚蠢，总学不会（哪有总是学，天天玩。分明就是自己不刷题），慢慢来吧;

```java
class Solution {
    public ListNode removeElements(ListNode head, int val) {
        if(head == null)return null;
        head.next = removeElements(head.next,val);//通过该函数的返回值，一个个连起来;
        if(head.val == val)return head.next;
        return head;
    }
}
```

## leetcode-206-反转链表

示例：

    **输入**: 1->2->3->4->5->NULL
    **输出**: 5->4->3->2->1->NULL

愚蠢的我，还是不会做递归

```java
class Solution {
    public ListNode reverseList(ListNode head) {
        if(head == null)return null;
        ListNode prev = null, curr = head;
        while(curr != null){
            ListNode temp = curr.next;
            curr.next = prev;
            prev = curr;
            curr = temp;
        }
        return prev;
    }
}
```

2、递归

leetcode 官方题解：

假设列表为：

n1​→...→ nk−1 ​→ nk ​→ nk+1​ →...→ nm ​→ ∅

节点 nm - nk+1 已经反转，我们在 nk 位置;

n1 ​→...→ nk−1 ​→ nk ​→ nk+1 ​←...← nm

我们希望 nk+1 的下一个节点指向 nk​。

所以，nk​.next.next = nk​。

要小心的是 n1​ 的下一个必须指向 Ø 。如果你忽略了这一点，你的链表中可能会产生循环。如果使用大小为 2 的链表测试代码，则可能会捕获此错误。

<font color = red>刚学的递归，这题又没想出来。想到用递归，确不知道怎么做</font>

```java
class Solution {
    public ListNode reverseList(ListNode head) {
        if(head == null || head.next == null)return head;
        ListNode p = reverseList(head.next);
        head.next.next = head;
        head.next = null;
        // 到末尾停止，一直返回的末尾节点
        return p;
    }
}
```

## leetcode-234-回文链表

\*\*示例 1：

> **输入**: 1->2
> **输出**: false
> **输入**: 1->2->2->1
> **输出**: true

双指针牛批大法：

> 利用快慢指针，将前半段反转;

```java
class Solution {
    public boolean isPalindrome(ListNode head) {
        if(head == null || head.next == null)return true;
        ListNode fast = head,slow = head;
        ListNode pre = head, prepre = null;
        while(fast != null && fast.next != null){
            pre = slow;
            slow = slow.next;
            fast = fast.next.next;
            pre.next = prepre;
            prepre = pre;
        }
        if(fast != null)slow = slow.next;//当为奇数时，跳过中间的数;
        while(pre != null && slow != null){
            if(pre.val != slow.val)return false;
            pre = pre.next;
            slow = slow.next;
        }
        return true;
    }
}
```

## leetcode-876-链表的中间节点

题意：

> 删除链表的中间节点，当为偶数数时，删除第二个中间节点;

ps:利用上一道题的快慢指针很好做，正好学到;

```java
class Solution {
    public ListNode middleNode(ListNode head) {
        ListNode fast = head,slow = head;
        while(fast != null && fast.next != null){
            fast = fast.next.next;
            slow = slow.next;
        }
        return slow;
    }
}
```

另一种解法是将其链表全部放入数组，然后返回 a[length/2];

## leetcode-1290-二进制的链转整数

> **输入**：head = [1,0,1] > **输出**：5
> **解释**：二进制数 (101) 转化为十进制数 (5)

```java
class Solution {
    public int getDecimalValue(ListNode head) {
        int ans = 0 , cnt = 1;
        ListNode p = reverseList(head);
        while(p != null){
            ans += p.val * cnt;
            cnt *= 2;
            p = p.next;
        }
        return ans;
    }
    public ListNode reverseList(ListNode head){
        if(head == null || head.next == null) return head;
        ListNode p = reverseList(head.next);
        head.next.next = head;
        head.next = null;
        return p;
    }
}
```

熟悉了下反转操作。最后看题解可以不反转，人啊，我的第一反应就是先反转链表，曰了；

<font color = bule size = 10>我好蠢</font>

```java
class Solution {
    public int getDecimalValue(ListNode head) {
        int ans = 0;
        while(head != null){
            ans = ans * 2 + head.val;
            head = head.next;
        }
        return ans;
    }
}
```

## leetcode-19-删除链表的倒数第 N 个节点

**示例**：

> 给定一个链表: 1->2->3->4->5, 和 n = 2.
> 当删除了倒数第二个节点后，链表变为 1->2->3->5.

利用前面学的递归知识，真是很开心的就用上了，fighting!!

```java
class Solution {
    int idx = 0;
    public ListNode removeNthFromEnd(ListNode head, int n) {
        if(n == 0)return head;
        if(head == null)return null;
        head.next = removeNthFromEnd(head.next,n);
        idx ++;
        if(idx == n)return head.next;
        return head;
    }
}
```

官方题解:双指针

> 利用快指针先走 n+1 步，最后快指针到末尾时，慢指针刚好到达要删的节点之前;

```java
class Solution {
    public ListNode removeNthFromEnd(ListNode head, int n) {
        ListNode dump = new ListNode(0);//创建一个dump是防止快指针走n+1步后超过null 和 方便删除头结点，因为由题得n不会大于head的长度。
        dump.next = head;
        ListNode slow = dump, fast = dump;
        for(int i = 0; i <= n ; i++){
            fast = fast.next;
        }
        while(fast != null){
            fast = fast.next;
            slow = slow.next;
        }
        slow.next = slow.next.next;
        return dump.next;
    }
}
```

## leetcode-24-两两交换链表中的结点

---

给定一个链表，两两交换其中相邻的节点，并返回交换后的链表。

**你不能只是单纯的改变节点内部的值**，而是需要实际的进行节点交换。

> 给定 1->2->3->4, 你应该返回 2->1->4->3.

```java
class Solution {
    public ListNode swapPairs(ListNode head) {
        ListNode dump = new ListNode(0);//利用上一题的思想。
        dump.next = head;
        ListNode head1 = head,prev = dump;
        while(head1 != null && head1.next != null){
            ListNode temp = head1.next.next;
            prev.next = head1.next;
            head1.next.next = head1;
            head1.next = temp;
            prev = head1;
            head1 = temp;
        }
        return dump.next;
    }
}
```

递归做法：

```java
class Solution {
    public ListNode swapPairs(ListNode head) {
        if(head == null || head.next == null)return head;
        ListNode next = head.next;
        head.next = swapPairs(next.next);//上一个结点的下一个指向返回的这个

        next.next = head;//第二个结点指向第一个结点；
        return next;//返回值，为第二个结点
    }
}
```
## leetcode-82-删除排序链表中的重复元素
---

题目描述：
给定一个排序链表，删除所有含有重复数字的节点，只保留原始链表中 没有重复出现 的数字。
```
示例 1:
输入: 1->2->3->3->4->4->5
输出: 1->2->5

示例 2:
输入: 1->1->1->2->3
输出: 2->3
```
常规思路删除即可，创建一个新链表,表头为head的前一个结点，方便删除；
```java
class Solution {
    public ListNode deleteDuplicates(ListNode head) {
        if(head == null || head.next == null)return head;
        ListNode dump = new ListNode(0),p1 = dump;
        dump.next = head;
        while(dump.next != null && dump.next.next != null){
            if(dump.next.val == dump.next.next.val){
                ListNode p = dump.next.next;
                while(p.next != null && p.next.val == dump.next.val){//找到要删除结点的最后一个位置；
                    p = p.next;
                }
                dump.next = p.next;
            }
            else
            dump = dump.next;
        }
        return p1.next;
    }
}
```

## leetcode-445.两数相加 II

给你两个 非空 链表来代表两个非负整数。数字最高位位于链表开始位置。它们的每个节点只存储一位数字。将这两数相加会返回一个新的链表。

你可以假设除了数字 0 之外，这两个数字都不会以零开头。

**进阶：**

>  如果输入链表不能修改该如何处理？换句话说，你不能对列表中的节点进行翻转。

**示例：**

> 输入：(7 -> 2 -> 4 -> 3) + (5 -> 6 -> 4)
> 输出：7 -> 8 -> 0 -> 7

```java
class Solution {
    public ListNode addTwoNumbers(ListNode l1, ListNode l2) {
        // 链表翻转
        ListNode prev = null, curr = l1;
        while(curr != null){
            ListNode temp = curr.next;
            curr.next = prev;
            prev = curr;
            curr = temp;
        }
        // 将l1 指向最后一个节点
        l1 = prev;
        // l2 同上
        prev = null; curr = l2;
        while(curr != null){
            ListNode temp = curr.next;
            curr.next = prev;
            prev = curr;
            curr = temp;
        }
        l2 = prev;


        // 利用头插法链表 做加法
        ListNode res = new ListNode(-1);
        res.next = null;
        int cnt = 0;
        while(l1 != null && l2 != null){
            ListNode now = new ListNode((l1.val + l2.val + cnt) % 10);
            now.next = res.next;
            res.next = now;
            if(l1.val + l2.val + cnt >= 10)cnt = 1;
            else cnt = 0;
            l1 = l1.next;
            l2 = l2.next;
        }
        //将剩余的 链补上
        while(l1 != null){
            ListNode now = new ListNode((l1.val + cnt)%10);
            if((l1.val + cnt) >= 10)cnt = 1;
            else cnt = 0;
            now.next = res.next;
            res.next = now;
            l1 = l1.next;
        }
        while(l2 != null){
            ListNode now = new ListNode((l2.val + cnt) % 10);
            if((l2.val + cnt) >= 10)cnt = 1;
            else cnt = 0;
            now.next = res.next;
            res.next = now;
            l2 = l2.next;
        }
        if(cnt > 0){
            ListNode now = new ListNode(cnt);
            now.next = res.next;
            res.next = now;
        }

        return res.next;
    }
}
```

## leetcode-25 K个一组翻转链表

给你一个链表，每 k 个节点一组进行翻转，请你返回翻转后的链表。

k 是一个正整数，它的值小于或等于链表的长度。

如果节点总数不是 k 的整数倍，那么请将最后剩余的节点保持原有顺序。

 

示例：

给你这个链表：1->2->3->4->5

当 k = 2 时，应当返回: 2->1->4->3->5

当 k = 3 时，应当返回: 3->2->1->4->5

说明：

你的算法只能使用常数的额外空间。
你不能只是单纯的改变节点内部的值，而是需要实际进行节点交换

> 利用栈翻转的特性

```java
class Solution {
    public ListNode reverseKGroup(ListNode head, int k) {
        Stack<ListNode> stack = new Stack<>();
        ListNode dump = new ListNode(-1),temp = head;
        ListNode ans = dump;
        dump.next = head;
        while(true){
            int count = 0;
            head = temp;
            while(temp != null && count < k){
                stack.push(temp);
                temp = temp.next;
                count ++;
            }
            if(count < k){
                dump.next = head;
                break;
            }
            if(count == k){
                while(!stack.isEmpty()){
                    dump.next = stack.pop();
                    dump = dump.next;
                }
            }
        }
        return ans.next;
    }
}
```

> 递归

```java
class Solution {
    public ListNode reverseKGroup(ListNode head, int k) {
        if(head == null || head.next == null)return head;
        ListNode tail = head;
        for(int i = 1; i <= k; i ++){
            if(tail == null)return head;
            tail = tail.next;
        }
        ListNode newHead = reverse(head, tail);
        head.next = reverseKGroup(tail, k);
        return newHead;
    }
    public ListNode reverse(ListNode head,ListNode tail){
        ListNode pre = null,next = null;
        while(head != tail){
            next = head.next;
            head.next = pre;
            pre = head;
            head = next;
        }
        return pre;
    }
}
```

> 非递归

```java
class Solution {
    public ListNode reverseKGroup(ListNode head, int k) {
        ListNode dump = new ListNode(-1);
        dump.next = head;
        ListNode ans = dump;
        while(true){
            ListNode tail = head;
            for(int i = 0; i < k; i ++){
                if(tail == null){
                    dump.next = head;
                    return ans.next;
                }
                tail = tail.next;
            }
            ListNode next = null, pre = null,temp = head;
            while(head != tail){
                next = head.next;
                head.next = pre;
                pre = head;
                head = next;
            }
            dump.next = pre;
            dump = temp;
        }
    }
}
```



