# xml

### xml的约束条件dtd
* 创建一个文件 后缀名 .dtd

 步骤：

    1. xml中有几个元素则写几个<!EMEMENT>
    2. 复杂元素：有子元素的元素
    <!ELEMENT 元素名 (子元素,子元素)>
    3. 简单元素<!ELEMENT 元素名 (#PCDATA)>
    4. 在xml中引入dtd语法
    <!DCTYPE 根元素名称 SYSTEM "dtd文件路径">

三种dtd引入方式:

    1. 引入外部dtd<!DOCTYPE 根元素名称 SYSTEM  "dtd路径">

    2. 使用内部dtd <!DOCTYPE 根元素 [
        内容...
    ]>

    3. 使用外部dtd文件（网络上的故用public）<!DOCTYPE 根元素 "dtd名称" "dtd文档的URL">
    
使用DTD定义元素：

+ 简单元素
    1. #PCDATA: 约束元素为字符串
    2. EMPTY: 元素为空(<age></age>)没有内容
    3. ANY: 任意

+ 复杂元素

    +  +：表示一次或多次
    +  ？：零次或者一次
    +  *：任意次数
    + |多选一
    + ，按顺序出现


实体定义：

    <!ENTITY 实体名称 "实体的值">
    用& ;使用

xml解析方式： dom 和 sax
+ dom 解析xml 在内存中分配一个树形结构 
+ sax 边读边解析

dom优缺点：可能会内存溢出，但易于查询，修改。

sax优缺点：不会内存溢出，能查询，但不能增删改;

使用jaxp查询操作：
1. 先创建解析工厂DocumentBuilderFactory.newInstance();
2.  解析器:实例.newDocumentBuilder();
3.  得到文件 document:
解析器实例.parse("路径");
4. 得到某种元素 NodeList list = doc.getElementsByTagName("name");
5. 循环遍历,并用.getTextContent()获得内容；



### schema约束文件
---
后缀为.xsd

xmlns="http://www.w3.org/2001/XMLSchema"，表示为约束文档而已；
targetNamespace="http://www.example.org/NewXMLSchema" ，表示路径，要使用这个约束文件，通过这个地址引入。


约束语法简单代码
```java
<?xml version="1.0" encoding="UTF-8"?>
<schema xmlns="http://www.w3.org/2001/XMLSchema" 
targetNamespace="http://www.example.org/NewXMLSchema" 
elementFormDefault="qualified">

<element name="person">
	<complexType>
		<sequence>
			<element name = "name" type = "string"></element>
			<element name = "name" type = "string"></element>
			<element name = "age" type = "int"></element>
		</sequence>
	</complexType>
</element>
</schema>
```
 

引入语法
```java
<根元素 xmlns="http://www.w3.org/2001/XMLSchema-instance"
xmlns:xls = "http://www.example.org/NewXMLSchema"
xls:schemaLocation="http://www.example.org/NewXMLSchema 2.xsd">
```

```
<sequeue></sequeue>按顺序出现
<all></all>只能出现一次
<choice></choice>只能出现其中的一个
maxOccurs = "unbounded"代表可以该元素可以出现无限次
```
> 约束属性
> ```<attribute name = "id1" type = "int" use = "required"></attribute>```
> 写在```</complexType>之前```
> name ：属性名称
> type : 属性类型
> use : 属性是否必须出现


使用Schema的sax方法操作xml：
```java
public class Test2 {
	public static void main(String[] args) throws Exception {
		// TODO Auto-generated method stub
		SAXParserFactory saxFactory = SAXParserFactory.newInstance();
		SAXParser sax = saxFactory.newSAXParser();
		sax.parse("src/1.xml", new Default1());
	}
}

class Default1 extends DefaultHandler{
	int id = 1;
	boolean flag = false;
	@Override
	public void startElement(String uri, String localName, String qName, Attributes attributes) throws SAXException {
		// TODO Auto-generated method stub
		if("name".equals(qName)) {
			flag = true;
		}
	}
	@Override
	public void characters(char[] ch, int start, int length) throws SAXException {
		// TODO Auto-generated method stub
		if(flag == true && id == 2)
		System.out.print(new String(ch,start,length));
	}
	@Override
	public void endElement(String uri, String localName, String qName) throws SAXException {
		// TODO Auto-generated method stub
		if("name".equals(qName)) {
			flag = false;
			id++;
		}
	}
}
```
Node与Document,Element的关系;
![](https://images0.cnblogs.com/blog2015/705279/201505/211329244328059.png)

### dom4j解析xml
---
dom4j不是java se的一部分，第一步要导入jar包

1、使用dom4j
1. 得到document
    ```java
    SAXReader reder = new SAXReader();
    Document document = reder.red(url);
    ```
2. document中 getRootElement() ： 获取父节点，返回的是Element;

3. Element 也是一个接口；
    Element和Node中的方法
    - getParent() : 获取父节点
    - addElement() : 添加标签

2、dom4j查询xml方法
+ element(qname)
获取标签下的第一个子标签
qname:标签名字
+ elements(qname)
获取标签下是这个名称的所有子标签,用List接；
qname:标签名字
+ elements()
获取标签下的所有一层子标签



### XPATH
---
直接获取某个元素，不需要一层层解析
1、 /AAA/DDD/BBB：一个 / 表示一层
2、 //BBB ： 不管哪个BBB，只要名称相同则得到;
3、 /AAA/CCC/DDD/* : /*表示所有
4、 /AAA/BBB[1] : 中括号里面表示 第几个，此表示AAA中第一个BBB； [last()]表示最后一个BBB；
5、 //@id ： 得到 属性,id //BBB[@id] 只要BBB上有id属性就都得到
6、 //BBB[@id = 'b1'] ： BBB上id属性等于b1；