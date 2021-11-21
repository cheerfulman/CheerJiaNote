# GORM

## 模型定义

### 约定

GORM 倾向于约定，而不是配置。默认情况下，GORM 使用 `ID` 作为主键，使用结构体名的 `蛇形复数` 作为表名，字段名的 `蛇形` 作为列名，并使用 `CreatedAt`、`UpdatedAt` 字段追踪创建、更新时间

遵循 GORM 已有的约定，可以减少您的配置和代码量。如果约定不符合您的需求，[GORM 允许您自定义配置它们](https://gorm.io/zh_CN/docs/conventions.html)

### gorm.Model

GORM定义了一个gorm.Model结构体。

```go
// gorm.Model 的定义
type Model struct {
  ID        uint           `gorm:"primaryKey"`
  CreatedAt time.Time
  UpdatedAt time.Time
  DeletedAt gorm.DeletedAt `gorm:"index"`
}
```

可以嵌入到结构体中，以包含这几个字段

```go
type User struct {
  gorm.Model
  Name string
}
// 等效于
type User struct {
  ID        uint           `gorm:"primaryKey"`
  CreatedAt time.Time
  UpdatedAt time.Time
  DeletedAt gorm.DeletedAt `gorm:"index"`
  Name string
}

func (User) TableName() string {
    return "user_table"
}
```

### 字段级权限控制

GORM可以对字段进行权限控制

```go
type User struct {
  Name string `gorm:"<-:create"` // 允许读和创建
  Name string `gorm:"<-:update"` // 允许读和更新
  Name string `gorm:"<-"`        // 允许读和写（创建和更新）
  Name string `gorm:"<-:false"`  // 允许读，禁止写
  Name string `gorm:"->"`        // 只读（除非有自定义配置，否则禁止写）
  Name string `gorm:"->;<-:create"` // 允许读和写
  Name string `gorm:"->:false;<-:create"` // 仅创建（禁止从 db 读）
  Name string `gorm:"-"`  // 通过 struct 读写会忽略该字段
}
```

### 嵌入结构体

对于正常的结构体字段，你也可以通过标签 `embedded` 将其嵌入，例如：

```go
type Author struct {
    Name  string
    Email string
}

type Blog struct {
  ID      int
  Author  Author `gorm:"embedded"`
  Upvotes int32
}
// 等效于
type Blog struct {
  ID    int64
  Name  string
  Email string
  Upvotes  int32
}
```

并且，您可以使用标签 `embeddedPrefix` 来为 db 中的字段名添加前缀，例如：

```go
type Blog struct {
  ID      int
  Author  Author `gorm:"embedded;embeddedPrefix:author_"`
  Upvotes int32
}
// 等效于
type Blog struct {
  ID          int64
    AuthorName  string
    AuthorEmail string
  Upvotes     int32
}
```

## 创建

```go
type StudentInfo struct {
	gorm.Model
    Name string
	Age uint32
	Gender string
}

func (s StudentInfo) TableName() string {
	return "students"
}
```

```go
student := model.StudentInfo{
    Name: "lomont", 
    Gender: "male", 
    Age: 16,
}
result := db.Create(&student)
if result.Error != nil {
    panic(result.Error)
}
fmt.Println(student.ID, result.RowsAffected)
```

### 用指定的字段创建记录

```go
result := db.Select("Name", "Age", "UpdatedAt").Create(&student)
result := db.Omit("Name", "Age", "UpdatedAt").Create(&student)
```

### 批量创建

```go
// 批量插入
var students = []model.StudentInfo{
    student,
    {Name: "lomont1", Age: 0, Gender: "male"},
    {Name: "lomont2", Age: 19, Gender: "famale"},
}

result := db.Create(&students)
// 分批创建
//db.CreateInBatches(&students, 20)
if result.Error != nil {
    panic(result.Error)
}
fmt.Println(result.RowsAffected)
for _, stud := range students {
    fmt.Println(stud.ID)
}

输出：
3
27
28
29
```

## 查询

### 检索单个对象

GORM 提供了 `First`、`Take`、`Last` 方法，以便从数据库中检索单个对象。当查询数据库时它添加了 `LIMIT 1` 条件，且没有找到记录时，它会返回 `ErrRecordNotFound` 错误

```go
db := GetDB()
var student model.StudentInfo
// result := db.First(&student) --- 根据主键升序
// result := db.Take(&student)  --- 无序
// last -- 主键降序
result := db.Last(&student)	   
fmt.Println(result.RowsAffected, student)
```

### 使用主键检索

```go
// 主键检索
//result := db.First(&student, 15)
//result := db.First(&student, "18")
result := db.Debug().Find(&students, []int{15,23,24})
// SELECT * FROM students WHERE id IN (15,23,24);
for _, stud := range students {
    fmt.Println(stud.ID)
}
fmt.Println(result.RowsAffected)
```

### 条件查询

```go
db.Where("age > ? AND name = ?", 14, "lomont").Find(&students)
db.Not("name", []string{"lomont", "lomont1"}).Find(&students)
// SELECT * FROM `students` WHERE `name` NOT IN ('lomont','lomont1')

db.Debug().Where("name", "lomont").Or("name", "lomont1").Find(&students)
// SELECT * FROM `students` WHERE `name` = 'lomont' OR `name` = 'lomont1'

db.Debug().Where("name", "lomont").Or("name", "lomont1").Select("name", "age").Find(&students)
// SELECT `name`,`age` FROM `students` WHERE `name` = 'lomont' OR `name` = 'lomont1'

db.Debug().Table("students").Select("COALESCE(age,?)", 42).Rows()
// SELECT COALESCE(age,42) FROM `students`

db.Debug().Where("name", "lomont").Or("name", "lomont1").Order("age").Select("name", "age").Find(&students)
// SELECT `name`,`age` FROM `students` WHERE `name` = 'lomont' OR `name` = 'lomont1' ORDER BY age

db.Debug().Where("name", "lomont").Order("age DESC, name").Select("name", "age").Find(&students)
// SELECT `name`,`age` FROM `students` WHERE `name` = 'lomont' ORDER BY age DESC, name

db.Debug().Where("name", "lomont").Order("age DESC, name").Limit(3).Offset(1).Select("name", "age").Find(&students)
// SELECT `name`,`age` FROM `students` WHERE `name` = 'lomont' ORDER BY age DESC, name LIMIT 3 OFFSET 1

db.Debug().Model(&model.StudentInfo{}).Select("name", "sum(age) as total").Where("name LIKE ?", "lomon%").Group("name").Rows()
// SELECT `name`,sum(age) as total FROM `students` WHERE name LIKE 'lomon%' GROUP BY `name`

db.Debug().Where("age > (?)", db.Table("students").Select("AVG(age)")).Find(&students)
// SELECT * FROM `students` WHERE age > (SELECT AVG(age) FROM `students`)
```

**where 详细使用**

```go
// 获取第一条匹配的记录
db.Where("name = ?", "jinzhu").First(&user)
// SELECT * FROM users WHERE name = 'jinzhu' ORDER BY id LIMIT 1;

// 获取全部匹配的记录
db.Where("name <> ?", "jinzhu").Find(&users)
// SELECT * FROM users WHERE name <> 'jinzhu';

// IN
db.Where("name IN ?", []string{"jinzhu", "jinzhu 2"}).Find(&users)
// SELECT * FROM users WHERE name IN ('jinzhu','jinzhu 2');

// LIKE
db.Where("name LIKE ?", "%jin%").Find(&users)
// SELECT * FROM users WHERE name LIKE '%jin%';

// AND
db.Where("name = ? AND age >= ?", "jinzhu", "22").Find(&users)
// SELECT * FROM users WHERE name = 'jinzhu' AND age >= 22;

// Time
db.Where("updated_at > ?", lastWeek).Find(&users)
// SELECT * FROM users WHERE updated_at > '2000-01-01 00:00:00';

// BETWEEN
db.Where("created_at BETWEEN ? AND ?", lastWeek, today).Find(&users)
// SELECT * FROM users WHERE created_at BETWEEN '2000-01-01 00:00:00' AND '2000-01-08 00:00:00';
```

## 更新

常规更新

```go
db.First(&student)
student.Name = "lonmot100"
student.Age = 100
db.Save(&student)
```

### 使用update

更新多个字段**updates**

单个 **update**

```go
db.Debug().Where("name","lomont1").Updates(model.StudentInfo{Gender: "male", Age: 18})
// UPDATE `students` SET `updated_at`='2021-11-21 13:15:54.722',`age`=18,`gender`='male' WHERE `name` = 'lomont1'

db.Debug().Model(&student).Updates(model.StudentInfo{Gender: "male", Age: 18})
// UPDATE `students` SET `updated_at`='2021-11-21 13:18:21.472',`age`=18,`gender`='male' WHERE `id` = 15
```

### 批量更新

```go
db.Model(User{}).Where("role = ?", "admin").Updates(User{Name: "hello", Age: 18})
// UPDATE users SET name='hello', age=18 WHERE role = 'admin';
```

### 使用表达式更新

```go
db.Debug().Model(&student).Update("age", gorm.Expr("age * ? + ?", 2, 1))
// UPDATE `students` SET `age`=age * 2 + 1,`updated_at`='2021-11-21 13:21:38.329' WHERE `id` = 15
```

## 关联

### Belongs to

定义的关联

```go
type StudentInfo struct {
	gorm.Model
	Name string `json:"name"`
	Age uint32 `json:"age"`
	Gender string `json:"gender"`

	ClassroomID int `json:"classroom_id"`
	Classrooms Classroom `json:"classroom" gorm:"foreignKey:ID;references:ClassroomID"`
}

func (s StudentInfo) TableName() string {
	return "students"
}


type Classroom struct {
	gorm.Model
	Name string
}
```

预加载

```go
db.Debug().Preload("Classrooms").First(&student)
// SELECT * FROM `students` ORDER BY `students`.`id` LIMIT 1
// SELECT * FROM `classrooms` WHERE `classrooms`.`id` = 1 AND `classrooms`.`deleted_at` IS NULL

db.Debug().Preload("Classrooms").Find(&students, "id < ?", 19)
// SELECT * FROM `students` WHERE id < 19
// SELECT * FROM `classrooms` WHERE `classrooms`.`id` IN (1,2) AND `classrooms`.`deleted_at` IS NULL
```

### Has One

```go
type User struct {
  gorm.Model
  CreditCard CreditCard `gorm:"foreignKey:UserName"`
  // 使用 UserName 作为外键
}

type CreditCard struct {
  gorm.Model
  Number   string
  UserName string
}
```

### Has Many

```go
// User 有多张 CreditCard，UserID 是外键
type User struct {
  gorm.Model
  CreditCards []CreditCard
}

type CreditCard struct {
  gorm.Model
  Number string
  UserID uint
}
```

### Many to Many

```go
// User 拥有并属于多种 language，`user_languages` 是连接表
type User struct {
  gorm.Model
  Languages []Language `gorm:"many2many:user_languages;"`
}

type Language struct {
  gorm.Model
  Name string
}
```

```go
type User struct {
  gorm.Model
  Languages []Language `gorm:"many2many:user_languages;"`
}

type Language struct {
  gorm.Model
  Name string
}
```

