## vue插值语法

>vue在`<script>` 中 `</script>` 使用，他是一个js 框架

**重点**

其实vue也是一个对象，其中有

+ el:  // 挂载目标， 可在div中根据id对应
+ data:  数据 --- 一个对象 里面可以有很多字段
+ methods:  你写的方法，可以直接调用，相当于 js 中 function了

语法： 在`<div>`中绑定 id = el 挂在的名字后，可以使用{{}} 直接调值，也可以通过 `<v-text = XXX >`  让vue自动 寻找 data中 字段相等的(xxx)

如果调用msg中有 h5语法的，则需要`<v-html>` 

### vue中的条件判断

+ v-if
+ v-else
+ v-else-if

### v-show

```
v-show="show" 其中show是否为true 来显示
```

但是都会显示，只不过 为flase 时， style = 'display:none' （css渲染），一般切换比较多用v-show, 切换比较少可用v-if判断，比如**登录态** 中显示的不同按钮等

### v-bind

可以绑定到Vue中数据

### v-on

触发js方法

### v-for

Vue 循环，记得加上:key

```html
<ul>
    <li v-for="item in menus">
        <h3>id:{{item.id}} 菜名:{{item.name}}</h3>
    </li>
</ul>

<ul>
    <li v-for="(item,index) in menus" :key="item.id">
        <h3>{{index}}  -  id:{{item.id}} 菜名:{{item.name}}</h3>
    </li>
</ul>
<!--    遍历对象-->
<ol>
    <li v-for="(val,key) in obj" :key="key">
        {{key}} --- {{val}}
    </li>
</ol>
```

### v-model

双向绑定，实时显示

```html
<p>{{msg}}</p>
<input type="text" v-model="msg">
```

![image-20210118181203964](../img/image-20210118181203964.png)

**没看懂的直接看代码，运行下，就懂了**

cdn 开发库引入

```js
<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
```

代码示例：

```html
<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/html">
    <head>
        <meta charset="UTF-8">
        <title>Vue基础</title>


        <style>
            .active{
                color: red;

            }

            .box{
                width: 200px;
                height: 200px;
                background-color: red;
            }
            .active{
                background-color: greenyellow;
            }

        </style>
    </head>
    <body>
        <div id="app">
            <!--        使用 {{}} 即可访问data 中的数据-->
            {{ message }}
            {{ {id:1} }}
            <h3> {{ 1 > 2 ? '对' : '错'  }} </h3>
            <h3> {{ txt.split('').reverse().join('') }} </h3>
            <h3> {{getContent()}} </h3>
            <h3> {{getContent2()}} </h3>
            <h3> {{ msg3 }} </h3>
            <h3 v-html="htmlMsg"></h3>
            <h3 v-text="msg2"></h3>

            <!--        vue 条件判断-->
            <div v-if="Math.random() > 0.5">
                随机数大于0.5
            </div>
            <div v-else> 随机数小于0.5</div>


            <h3 v-show="show"> 测试show 为true显示 </h3>

            <a v-bind:href="res.url" v-bind:title="res.title">{{res.name}}</a>
            <!--        :src == v-bind:src 可以通过 :来省略v-bind-->
            <img :src="imgSrc.src">


            <h3 class='name' v-bind:class="{active:isActive}" class="">v-bind的用法</h3>

            <!--        自定义属性-->
            <!--        <h3 :aaa="res.name">a</h3>-->

            <h4 :style="{color:isColor,fontSize:isFontSize+'px'}">hello bind</h4>

            <h3>{{num}}</h3>
            <button v-on:click.once="handleClick">只能加一次</button>

            <!--        &lt;!&ndash; 提交事件不再重载页面 &ndash;&gt;-->
            <!--        <form v-on:submit.prevent="onSubmit"></form>-->

            <h3>{{num}}</h3>
            <button v-on:click="handleClick">加一</button>

            <div class="box" :class="{active:isActive1}"></div>
            <button @click="changeClick">切换</button>
            </br>
        <!--    按键修饰符  当你按↑ 和 回车时-->
        <input @keyup.up="submit">
        <input @keyup.enter="submit">


        <!--        vue 的 for 循环-->
        <!--    遍历数组-->
        <ul>
            <li v-for="item in menus">
                <h3>id:{{item.id}} 菜名:{{item.name}}</h3>
            </li>
        </ul>

        <ul>
            <li v-for="(item,index) in menus" :key="item.id">
                <h3>{{index}}  -  id:{{item.id}} 菜名:{{item.name}}</h3>
            </li>
        </ul>
        <!--    遍历对象-->
        <ol>
            <li v-for="(val,key) in obj" :key="key">
                {{key}} --- {{val}}
            </li>
        </ol>

        <!--        双向数据绑定-->
        <p>{{msg}}</p>
        <input type="text" v-model="msg">

        <h3>双向数据绑定 lazy版 点击外面才显示</h3>
        <p>{{txt}}</p>
        <input type="text" v-model.lazy="txt">

        </br>
    <!--        复选框单选-->
    <label for="checkbox">{{checked}}</label>
    <input type="checkbox" id="checkbox" v-model="checked">
    </br>
<!--        复选框多选-->
<div class="box1">
    <label for="a">黄瓜</label>
    <input type="checkbox" id="a" value="黄瓜" v-model="checkedName">

    <label for="b">黄瓜1</label>
    <input type="checkbox" id="b" value="黄瓜1" v-model="checkedName">

    <label for="c">黄瓜2</label>
    <input type="checkbox" id="c" value="黄瓜2" v-model="checkedName">
    <br/>
    <span>{{checkedName}}</span>
</div>


<select v-model="selected">
    <option v-for="option in options" :value="option.value">
        {{option.text}}
    </option>
</select>
<span>Selected: {{selected}}</span>
</div>




<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
<script>
    <!--        创建一个 Vue对象 -->
        const vm = new Vue({
            el: '#app', // 挂载目标
            data: {
                message: 'Hello Vue!',
                txt: 'hello',
                msg:'大大',
                msg2: 'content',
                msg3: '<p>插值语法</p>',
                htmlMsg: '<p>插值语法</p>',
                show: true,
                res:{
                    name:'百度',
                    url:'https://www.baidu.com',
                    title:'百度一下'
                },
                imgSrc:{
                    src:'./img/test.jpg'
                },
                isActive:true,
                isActive1:false,
                isColor:'green',
                isFontSize:'30',
                num:0,
                menus:[
                    {id:1,name:'大腰子'},
                    {id:2,name:'小腰子'},
                    {id:3,name:'中腰子'},
                    {id:4,name:'腰子'},
                ],
                obj:{
                    title:'hello 循环',
                    author:'lomont',
                },
                checked:false,
                checkedName:[],
                options:[
                    { text: 'One', value: 'A' },
                    { text: 'Two', value: 'B' },
                    { text: 'Three', value: 'C' }
                ],
                selected: 'A',
                txt:'aaaa'
            },
            methods:{
                getContent(){
                    return 'content';
                },
                getContent2(){
                    return this.message + ' ' + this.msg2;
                },
                handleClick(){
                    this.num += 1
                },
                changeClick(){
                    this.isActive1 = !this.isActive1
                },
                submit(){
                    alert(1)
                }
            }
        });
    //打印 Vue 对象
    console.log(vm.msg2);
</script>
</body>
</html>
```

运行结果：

![image-20210118181243688](../img/image-20210118181243688.png)

**data中的一些字段以及被导出可以直接Vue.msg2，无需Vue.data.msg2**

VUE文档：https://cn.vuejs.org/v2/guide/