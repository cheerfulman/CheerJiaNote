## sql 训练 1 ~ 40 题

> 建表



首先，创建表咯。一共有4张表。分别是**学生表**，**课程表**，**教师表**，**成绩表** 。

- *学生表Student*

4个字段，SId（学生ID）,Sname(学生姓名)，Sage(学生年龄)，Ssex(学生性别)

```text
create table Student(SId varchar(10),Sname varchar(10),Sage datetime,Ssex varchar(10),PRIMARY key(SId));
insert into Student values('01' , '赵雷' , '1990-01-01' , '男');
insert into Student values('02' , '钱电' , '1990-12-21' , '男');
insert into Student values('03' , '孙风' , '1990-12-20' , '男');
insert into Student values('04' , '李云' , '1990-12-06' , '男');
insert into Student values('05' , '周梅' , '1991-12-01' , '女');
insert into Student values('06' , '吴兰' , '1992-01-01' , '女');
insert into Student values('07' , '郑竹' , '1989-01-01' , '女');
insert into Student values('09' , '张三' , '2017-12-20' , '女');
insert into Student values('10' , '李四' , '2017-12-25' , '女');
insert into Student values('11' , '李四' , '2012-06-06' , '女');
insert into Student values('12' , '赵六' , '2013-06-13' , '女');
insert into Student values('13' , '孙七' , '2014-06-01' , '女');
```

- *教师表Teacher*

2个字段，TId（教师id），Tname（教师姓名）

```text
create table Teacher(TId varchar(10),Tname varchar(10),PRIMARY key (Tid));
insert into Teacher values('01' , '张三');
insert into Teacher values('02' , '李四');
insert into Teacher values('03' , '王五');
```

- *课程表*

3个字段，CId(课程id)，Cname（课程名称），TId（教师id）

```text
create table Course(CId varchar(10),Cname nvarchar(10),TId varchar(10),PRIMARY KEY(CId));
insert into Course values('01' , '语文' , '02');
insert into Course values('02' , '数学' , '01');
insert into Course values('03' , '英语' , '03');
```

- 成绩表

4个字段，skey（由于没有不重复的字段，因此创建了主键字段），SId(学生id)，CId(课程ID)，score（学生成绩）

```text
create table SC(skey int ,SId varchar(10),CId varchar(10),score decimal(18,1),PRIMARY key(skey));
insert into SC values('1','01' , '01' , 80);
insert into SC values('2','01' , '02' , 90);
insert into SC values('3','01' , '03' , 99);
insert into SC values('4','02' , '01' , 70);
insert into SC values('5','02' , '02' , 60);
insert into SC values('6','02' , '03' , 80);
insert into SC values('7','03' , '01' , 80);
insert into SC values('8','03' , '02' , 80);
insert into SC values('9','03' , '03' , 80);
insert into SC values('10','04' , '01' , 50);
insert into SC values('11','04' , '02' , 30);
insert into SC values('12','04' , '03' , 20);
insert into SC values('13','05' , '01' , 76);
insert into SC values('14','05' , '02' , 87);
insert into SC values('15','06' , '01' , 31);
insert into SC values('16','06' , '03' , 34);
insert into SC values('17','07' , '02' , 89);
insert into SC values('18','07' , '03' , 98);
```

---

## 1 ~ 40 题

```sql
# 1.求每门课程的学生人数。
select Course.Cname,count(SId) 
from course, sc 
where course.CId = sc.CId 
group by course.Cid;

# 2.查询课程编号为 01 且课程成绩在 80 分及以上的学生的学号和姓名

SELECT student.Sid,student.Sname,sc.score
FROM student, sc
where student.Sid = sc.SId
AND sc.Cid = '01' AND sc.score >= 80;

# 3.统计每门课程的学生选修人数（超过 5 人的课程才统计）

SELECT count(Sid) FROM sc
GROUP BY Cid HAVING count(Sid) > 5;


# 4.检索至少选修两门课程的学生学号

select sid from sc 
GROUP BY sid 
having count(cid) > 2

# 5.选修了全部课程的学生信息

SELECT a.Sid,a.Sname,a.Sage,a.Ssex FROM student a,sc b
WHERE a.Sid = b.Sid
GROUP BY Sid HAVING count(Cid) = (select count(*) from course);

# 6.查询存在不及格的课程
SELECT c.Cname FROM Course c, sc s
where c.CId = s.CId
GROUP BY s.CId HAVING min(s.score) < 60;

select DISTINCT Course.Cname from sc ,Course  where sc.cid = Course.cid  
and sc.score < 60

# 7.查询任何一门课程成绩在 70 分以上的学生姓名、课程名称和分数

SELECT a.Sname, b.Cname, c.score FROM student a, course b, sc c
where a.Sid = c.Sid AND c.Cid = b.Cid
AND c.score > 70;

# 8.查询所有学生的课程及分数情况（存在学生没成绩，没选课的情况）

select a.sname,b.cname,c.score from student a left join sc c 
on a.sid=c.sid
left join course b 
on b.cid = c.cid;

# 9.查询课程名称为「数学」，且分数低于 60 的学生姓名和分数
SELECT c.Sname, b.score FROM Course a, sc b, student c 
WHERE a.CId = b.CId AND c.Sid = b.SId AND a.Cname = '数学' AND b.score < 60;

# 10.查询平均成绩大于等于 85 的所有学生的学号、姓名和平均成绩
SELECT b.Sid, b.Sname, avg(a.score)'平均成绩' FROM sc a, student b
where a.SId = b.Sid
GROUP BY a.SId HAVING avg(a.score) >= 85;


#11.查询每门课程的平均成绩，结果按平均成绩降序排列，平均成绩相同时，按课程编号升序排列
SELECT  avg(sc.score)'平均成绩' FROM sc 
GROUP BY sc.CId order by avg(sc.score) desc, sc.CId;

# 12.查询各科成绩最高分、最低分和平均分
SELECT b.CId, b.Cname max(a.score), min(a.score),avg(a.score), count(a.SId),
sum(
		case when score < 60 then 1 else 0 end
)/ count(a.CId)
FROM sc a, course b where a.CId = b.CId GROUP BY a.CId;



select sc.cid,Course.Cname,max(sc.score),min(sc.score),AVG(sc.score),count(sc.SId),
sum(case
	when sc.score < 60 then 1 else 0
	end
)/count(sc.cid)'不及格率',
sum(case
	when sc.score > 60 and sc.score < 70 then 1 else 0
	end
)/count(sc.cid)'及格率',

sum(case
	when sc.score >= 70 AND sc.score < 80 then 1 else 0 
	end
)/count(sc.cid)'中等率',

sum(case
	when sc.score >= 80 AND sc.score < 90 then 1 else 0 
	end
)/count(sc.cid)'优良率',
sum(case
	when sc.score >= 90 AND sc.score <= 100 then 1 else 0 
	end
)/count(sc.cid)'优秀率'
from sc ,Course
where sc.cid =  Course.cid
GROUP BY sc.cid;


# 13.查询男生、女生人数
SELECT Ssex,count(*) FROM student GROUP BY Ssex;


# 14.检索" 01 "课程分数小于 60，按分数降序排列的学生信息
SELECT a.* from student a, sc b where a.Sid = b.SId
AND b.CId = '01' and b.score < 60 order by b.score desc;

# 15.按平均成绩从高到低显示所有学生的所有课程的成绩以及平均成绩
select a.SId,a.score,b.`平均成绩` 
from sc a right join (select SId,avg(score)'平均成绩' from sc GROUP BY SId) b 
on a.SId = b.SId ORDER BY b.平均成绩 DESC;


# 16.查询没学过"张三"老师讲授的任一门课程的学生姓名

SELECT student.Sname from student where student.Sid not in(
select a.sid from sc a, teacher b, course c
where a.CId = c.cid and b.tid = c.tid and b.Tname = '张三');


# 17.成绩不重复，查询选修「张三」老师所授课程的学生中，成绩最高的学生信息及其成绩
select b.* from sc a, student b, teacher c,course d 
where a.SId = b.Sid and a.CId = d.CId and c.Tid = d.TId
and c.Tname = '张三' order by a.score desc limit 1;

#18.成绩有重复的情况下，查询选修「张三」老师所授课程的学生中，成绩最高的学生信息及其成绩
-- 先修改数据库，增加一个重复项
UPDATE sc SET score=90
where skey=17

-- 先查出最高的分数，然后再查分数等于最高分数的人
select b.*,a.score from sc a, student b
where a.SId = b.SId and a.score in(
select max(a.score) from sc a, student b, teacher c,course d 
where a.SId = b.Sid and a.CId = d.CId and c.Tid = d.TId
and c.Tname = '张三');

# 19 查询不同课程成绩相同的学生的学生编号、课程编号、学生成绩
select a.cid, a.sid, any_value(a.score) from sc a,sc b 
where a.SId = b.Sid and a.score = b.score and a.CId != b.CId GROUP BY a.cid,b.SId;


# 20.查询每门功课成绩最好的前两名
select any_value(a.sid),any_value(a.cid),any_value(a.score)  
from sc a left join sc b
on a.CId = b.CId and a.score < b.score
group by a.cid,a.sid
having count(b.score) < 2 order by a.cid;



# 21.查询每门课程被选修的学生数
select count(*) from sc group by cid;


# 22.查询出只选修两门课程的学生学号和姓名
select b.sid,b.Sname from sc a, student b where a.SId = b.sid
group by b.sid having count(a.CId) = 2;

# 23查询同名学生名单，并统计同名人数

select Sname, count(1) as number from student
group by Sname having number >= 2;


# 24.查询 1990 年出生的学生名单
select * from Student where year(sage)= 1990;

# 25.查询各学生的年龄.
select sid,sname,TIMESTAMPDIFF(year,sage,CURDATE())from student 

# 26.查询本周过生日的学生
select * from student where 
week(curdate()) = week(Sage)

# 27.查询本月过生日的学生
select * from student where MONTH(CURDATE()) = MONTH(sage);
# 28.查询「李」姓老师的数量
select count(1) from teacher where Tname like '李%';

# 29.查有成绩的学生信息

select * from Student where sid in (select sc.sid from sc)

# 30.查询所有同学的学生编号、学生姓名、选课总数、所有课程的成绩总和
select a.sid,a.Sname,count(1) as number, sum(b.score) as total 
from student a left join sc b 
on a.sid = b.sid
group by a.sid; 

# 31.查询在 SC 表存在成绩的学生信息
SELECT
	* 
FROM
	student 
WHERE
	sid IN ( SELECT DISTINCT sid FROM sc WHERE score IS NOT NULL );


# 32.查询平均成绩大于等于 60 分的同学的学生编号和学生姓名和平均成绩
select a.Sid,a.Sname,AVG(b.score) from student a,sc b
where a.Sid = b.SId group by a.SId having AVG(b.score) >= 60;

# 33.查询不存在" 01 "课程但存在" 02 "课程的情况

select * from sc where cid = '02' and sid not in (select  sid from sc  where cid='01')

# 34.查询存在" 01 "课程但可能不存在" 02 "课程的情况

select  * from  sc where cid ='01';

# 35.按各科成绩进行排序，并显示排名 Score 重复时保留名次空缺
select a.CId,a.SId,any_value(a.score),count(b.score)+1 as '名次' from sc a left join sc b on a.CId = b.CId and a.score < b.score
group by a.CId,a.SId ORDER BY a.CId,count(b.sid)+1 ;

# 36.查询" 01 "课程比" 02 "课程成绩高的学生的信息及课程分数
select a.*,b.score,c.score from student a,
(select score, sid from sc where cid= '01')b,
(select score, sid from sc where cid= '02')c
where a.sid = b.sid and a.sid = c.sid and b.score > c.score;

# 37查询学过「张三」老师授课的同学的信息

select * from student
where sid in (select sid from teacher a,course b, sc c 
where c.cid = b.cid and a.tid = b.tid and a.tname = '张三');

# 38查询没有学全所有课程的同学的信息

select a.* from student a left join sc b on a.Sid = b.SId
GROUP BY a.Sid HAVING count(b.cid) < (select count(cid) from course)


# 39.查询至少有一门课与学号为" 01 "的同学所学相同的同学的信息

select a.* from student a ,sc b where a.sid = b.sid and b.cid in 
(select cid from sc where sid ='01')
group by a.sid;

# 40.查询和" 01 "号的同学学习的课程完全相同的其他同学的信息
select a.* from student a
where Sid in (select sid from sc where sid not in (select sid from sc where cid not in (select cid from sc where sid = '01')) 
GROUP BY sid having count(*) = (select count(cid) from sc where sid = '01') and sid != '01');

```

学习于：https://www.zhihu.com/collection/435712228