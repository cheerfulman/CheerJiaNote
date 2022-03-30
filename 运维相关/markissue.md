## 如何线上查看日志

机器上的`nginx`配置文件是很多的，我们如何找到我们想要查看的`nginx`并且找到错误日志的目录呢？

>  根据你要查看的域名如：`www.baidu.com`

那么我们`nginx`下很可能`server_name` 会是`www.baidu.com`

我们可以使用正则匹配 **grep** : `grep  www.baidu.com /etc/nginx/conf.d/*.conf`

本机的`conf`文件一般放置在`/etc/nginx/conf.d`上；

由此可以查到对应server_name的nginx文件路径；

**之后就可以cat 文件路径直接查看访问日志access.log 和 error.log了；**

**也可以查看root下目录的项目日志；**

## linux 各个文件的作用

bin: 可执行二进制文件目录；

boot ： linux启动时用到的文件

dev:  linux下的设备文件

**ect**: 系统的配置文件，如nginx的配置文件等

home: 用户目录，新增用户账号时，用户的家目录放此目录下

lib: 系统函数库目录需要调用一些额外的参数时需要函数库的协助

root : 系统管理员root的家目录，系统第一个启动的分区为 /，所以最好将 /root和 /放置在一个分区下。

sbin：放置系统管理员使用的可执行命令

**tmp**: 一般用户或正在执行的程序临时存放文件的目录,任何人都可以访问,重要数据不可放置在此目录下

srv：服务启动之后需要访问的数据目录，如 www 服务需要访问的网页数据存放在 /srv/www 内。

usr：应用程序存放目录

var：放置系统执行过程中经常变化的文件，如随时更改的日志文件