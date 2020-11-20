## lumen

### 增删查改

前面讲了laravel框架如何从url到controller，再从controller处理完数据返回给前端的一系列MVC流程，今天来看看数据库的操作；

首先在`.env`配置文件中配好如下参数：

```env
DB_CONNECTION=mysql
DB_HOST=主机地址默认localhost
DB_PORT=mysql端口号:默认 3306
DB_DATABASE=数据库名称
DB_USERNAME=用户名
DB_PASSWORD=密码
```

我们在`web.php`中配置一条路由：

```php
Route::get('/test1',  'StudentController@test1');
```

接下来使用数据库进行增删改查：

**插入：**

```php
DB::insert('insert into student(name,age,sex) values(?,?,?)',
           ["lomontzhu",19,01]);
```

**查询**：

```php
$student = DB::select("select * from student where id > ?",[1001]);
dd($student); // 输出到前端页面
```

**删除：**

```php
$row = DB::delete("delete from student where id > ?",[1002]);
dd($row); // 显示影响的行数
```

**修改：**

```php
$row = DB::update("update student set age = ? where name = ?",
                  [18,"lomont"]);
var_dump($row);
```

> 我们发现使用DB:: 开头即可

### 查询构造器

利用查询构造器进行插入：

```php
$id = DB::table('student')->insertGetId(
    ['name' => "ali", 'age' => '99']
);// 得到id
```

插入多行：

```php
$bool1 = DB::table('student')->insert([
    ['name' => 'kelly11', 'age' => 46],
    ['name' => 'ob', 'gae' => 93],
]);
```

更新：

```php
$num = DB::table('student')->where('id',1001)->update(['age' => 24]);
```

自增和自减：

```php
// 默认自增1
DB::table('student')->increment('age');
// 自增3
DB::table('student')->increment('age',3);
// 加上where条件，自减3
DB::table('student')->where('id',1001)
->decrement('age',24);
// 自减并且修改其它列
DB::table('student')->where('id',1002)
->decrement('age',2,['name'=>'qq']);
```

删除：

```php
//         删除id = 1003的数据
DB::table('student')->where('id',1003)
    ->delete();

//         删除id >= 1009的数据
DB::table('student')->where('id','>=',1009)
    ->delete();

//          将表清空
DB::table('student')->truncate();
```

查询：

```php
// 查询所以数据
$student = DB::table('student')->get();
// 第一条数据
$student = DB::table('student')->first();


// 取出大于1005的
$student = DB::table('student')->where('id','>=',1005)
    ->get();

// 多重条件
$student = DB::table('student')->
    whereRaw('id >= ? and age >= ?', [1003,40])
    ->get();
// 使用pluck可以返回固定的列
$student = DB::table('student')->
    whereRaw('id >= ? and age >= ?', [1003,40])
    ->pluck('name');
//
$student = DB::table('student')->
    whereRaw('id >= ? and age >= ?', [1003,40])
    ->select('id','name','age')->get();

//        // chunk 每次查询2个
echo "<pre>";
DB::table('student')->orderBy('id')->chunk(2,function ($student){
    var_dump($student);
});

// 聚合函数
echo DB::table('student')->max('age') . "<br>";
echo DB::table('student')->min('age'). "<br>";
echo DB::table('student')->average('age'). "<br>";
```

