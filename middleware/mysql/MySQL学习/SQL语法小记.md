## SQL函数语法小记

+ avg(): 求平均值忽略null，并不将其作为0参与计算；
+ count(): 
  + count(*): 对表中行数计数，**不管是否有null**
  + count(字段名)： 对特定列进行计数，排除null；

+ sum(): 对单个列求和，忽略null

+ all(): 对于null，来说null最大

  + > SELECT * FROM grouptest WHERE value >=  ALL(SELECT value FROM grouptest  WHERE value > 0)
    >
    > 正常返回grouptest 表中value最大的，但是如果去掉WHERE value > 0， 则返回Null

+ concat(): 用来追加字符串的

  + > CONCAT(ROUND(population/(SELECT population FROM world  WHERE name = 'Germany')*100, 0), '%')
    >
    > 该语句不保留小数，并追加%

+ round(): 保留小数