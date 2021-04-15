## lumen初级

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

### 使用Eloquent ORM

先配置一个Model:

```php
namespace App;
use Illuminate\Database\Eloquent\Model;
class Student extends Model{
    // 指定表名
    protected $table = 'student';
    // 指定主键
    protected $primaryKey = 'id';
}
```

#### 增删查改：

```php
// 查询表所有记录
$student = Student::all();
// 根据主键查找
$student = Student::find(1002);
// 查询不到就抛出异常
$student = Student::findOrFail(1002);
// 获得所有
$student = Student::get();

$student = Student::where('id','>', 1003)->orderBy('age','desc')->first();
$student = Student::where('age', '>', '21')->max('age');
```

#### 增加：利用Eloquent ORM非常方便

直接new一个数据即可

```php
// 新增数据
$student = new Student();
$student->name = 'lomont1';
$student->age = 20;
$bool = $student->save();
dd($student);
```

执行后数据库中自动新加了一条数据，并且代了时间戳：

![image-20201120202850515](../img/image-20201120202850515.png)

如果我们想关闭，则可以在Model文件中设置`public $timestamps = false;`即可

```php
class Student extends Model{
    // 指定表名
    protected $table = 'student';
    // 指定主键
    protected $primaryKey = 'id';
    // 自动维护时间戳
    public $timestamps = false;
}
```

如果想要毫秒级可以新增方法

```php
protected function getDateFormat()
{
    return time();
}
```

![image-20201120203533682](../img/image-20201120203533682.png)

输出时间：

```php
$student = Student::find(1015);
echo $student->created_at;
输出：
2020-11-20 12:35:21
```

如果想要时间输出时间变成毫秒级：新增asDateTime

```php
protected function asDateTime($value)
{
    return $value;
}
```

此时如果想输出格式化时间则要自定义格式：

```php
$student = Student::find(1015);
echo date('Y-M-D H:i:s', $student->updated_at);
```

#### **使用create创建：**

```php
$student = Student::create(
    ['name' => 'mabaoguo', 'age'=>69]
);
var_dump($student);
```

但是要设置允许批量增加字段：

```php
// 指定允许批量赋值的字段
protected $fillable = ['name','age'];
```

#### firstOrCreate

如果没有则创建，否则查询

```php
$student = Student::firstOrCreate(
    ['name' => 'mabaoguo123']
);
dd($student);
```

#### 修改

使用ORM操作数据库，就像是操作对象一样；

```php
$student = Student::find(1005);
$student->name = 'keai';
$student->save();
// id为1005的name 修改成了 'keai'

Student::where('id','>',1016)->update(
    ['age'=>33]
);
```

#### 删除

```php
//        // 通过模型删除
$student = Student::find(1017);
$student->delete();
//
//        // 通过主键删除
Student::destroy(1013);
//        // 通过指定条件删除
Student::where('id','>',1014)->delete();
```

## Blade模板引擎

+ 可以在视图(view)中使用原生的php代码
+ 所有的Blade视图页面都被编译成原生的php代码并缓存起来，除非模板文件被修改了，否则不会重新编译

我们很多页面头部尾部等都是相同的，而在我们Blade中如何复用呢？

直接在页面中加上

```php
@section('foot')
    底部
@show
    
在零一个页面中直接：
@section('content')
    aa
@stop
就可以实现复用模式，而修改里面的内容
```

#### 基础语法以及include

在student文件下创建`common1.blade.php`并填入如下内容

```php
<p>wo hen hao {{$message}}</p>
```



```php
<!-- 输出$name的值 -->
    <p>{{$name}}</p>
    <!-- 查看$name 是否在数组中 -->
    <p>{{in_array($name,$arr) ? 'true' : 'false'}}</p>
    <!-- 输出数组 -->
    <p>{{var_dump($arr)}}</p>

    <!-- 有则输出原值否则输出 default -->
    <p>{{isset($name) ? $name : 'default'}}</p>

    <!-- 有则输出原值否则输出 default -->
    <p>{{$name or 'default'}}</p>
    <!-- 原样输出 -->
    <p>@{{ $name }}</p>
{{--    模板注释 F12查看源码看不见--}}

{{--    通过@include调用其它的 blade文件，并且可以传参--}}
@include('student.common1',['message' => ' Love?'])
```

#### 流程控制

```php
{{--    if else 用法--}}
@if($name == 'php')
    I am php
@elseif ($name == 'php1') I am php1
@else who am i
@endif
{{--for --}}
@for($i = 0; $i <= 10; $i ++)
    <p>{{$i}}</p>
@endfor

@foreach($students as $student)
    <p>{{$student->name}}</p>
@endforeach
{{-- 如果$student 有数据则输出 否则 输出 @empty后面的东西--}}
@forelse($students as $student)
    <p>{{$student->name}}</p>
@empty
    <p>null</p>
@endforelse
```

#### 模板中的url

+ url
+ action
+ route()

```php
{{--    通过url--}}
<a href="{{url('urlTest')}}">url()</a>
{{-- 方法名--}}
<a href="{{action('StudentController@urlTest')}}">url()</a>
{{-- 别名， as 后面的别名--}}
<a href="{{route('url')}}">url()</a>
```