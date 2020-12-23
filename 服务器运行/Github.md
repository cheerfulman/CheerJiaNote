## Github使用

### 常用词

- watch：会持续收到该项目的动态
- fork：复制某个仓库到自己的Github仓库中
- star：点赞
- clone：将项目下载至本地
- follow：关注

![image-20200820093251822](https://cdn.jsdelivr.net/gh/cheerfulman/PigGo-img/img/20200820093319.png)

### 如何搜索

#### in关键词

- 公式：`XXX关键字 in:name 或 description 或 readme`
- xxx in:name 项目名称含有XXX的
- xxx in:description 项目描述含有XXX的
- xxx in:readme 项目的readme文件中包含XXX的
- 组合使用
  - xxx in:name,readme 项目的名称和readme中包含xxx的

演示：

![image-20200820093909592](https://cdn.jsdelivr.net/gh/cheerfulman/PigGo-img/img/20200820093909.png)

#### star和fork

根据**点赞**和**转发**来查找

- 公式：
  - `xxx关键字 stars 通配符` :> 或者 :>=
  - 区间范围数字： `stars:数字1..数字2`
- 案例
  - 查找stars数大于等于5000的Springboot项目：springboot stars:>=5000
  - 查找forks数在1000~2000之间的springboot项目：springboot forks:1000..5000
- 组合使用
  - 查找star大于1000，fork数在500到1000：`springboot stars:>1000 forks:500..1000`

演示：

![image-20200820094717642](https://cdn.jsdelivr.net/gh/cheerfulman/PigGo-img/img/20200820094731.png)

#### awesome加强搜索

公式：`awesome 关键字`：awesome系列，一般用来收集学习、工具、书籍类相关的项目

演示：

![image-20200820095258515](https://cdn.jsdelivr.net/gh/cheerfulman/PigGo-img/img/20200820095258.png)

#### 高亮Github代码

如要分享给别人代码时，关键开使的代码可以在url后+ #L(行数)

例如：

- 一行：地址后面紧跟 #L10
  - `https://github.com/febsteam/FEBS-Shiro/blob/v2.0/src/main/java/cc/mrbird/febs/generator/mapper/GeneratorMapper.java#L15`则会在15行高亮
- 多行：地址后面紧跟 #Lx - #Ln
  - `https://github.com/febsteam/FEBS-Shiro/blob/v2.0/src/main/java/cc/mrbird/febs/generator/mapper/GeneratorMapper.java#L15-L21` 则会在15-21行高亮

演示：单行

![image-20200820095748285](https://cdn.jsdelivr.net/gh/cheerfulman/PigGo-img/img/20200820095936.png)

多行：

![image-20200820095930369](https://cdn.jsdelivr.net/gh/cheerfulman/PigGo-img/img/20200820095936.png)

#### 项目内搜索

+ 按下英文`t`即可开启

第一步进入一个项目主页：

![image-20200820100219566](https://cdn.jsdelivr.net/gh/cheerfulman/PigGo-img/img/20200820100219.png)

按下`t`后：enter下一层，esc返回上一层

![image-20200820100242137](https://cdn.jsdelivr.net/gh/cheerfulman/PigGo-img/img/20200820100242.png)

#### 面基神器

公式：

- location：地区
- language：语言

示例：`location:beijing language:java`

演示：查找湘潭的好朋友

![image-20200820100813720](https://cdn.jsdelivr.net/gh/cheerfulman/PigGo-img/img/20200820100813.png)