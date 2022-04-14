# Docker入门学习

## 前言介绍

物理机缺点：部署慢、资源浪费、难以迁移扩展、可能被限定硬件厂商

虚拟机：一个物理机部署多个app、每个app可运行在一个VM中、每个虚拟机都是一个完整的操作系统(吃资源)

![image-20220329141512945](https://cdn.jsdelivr.net/gh/cheerfulman/picGo/img/20220329141544.png)

虚拟化技术：将计算工作，放到云上执行。比如阿里的RDS数据库服务，不需要自己搭建数据库。	

虚拟化工具有：vmware workstation(大家熟知的)、vmware esxi(企业版虚拟化)、kvm(linux下虚拟机工具)

二进制翻译技术：比如vmware 通过翻译，将Linux命令翻译成windows能识别的指令从而在windows上执行

**由于虚拟出一台计算机的成本比较高，而虚拟出一个隔离的环境成本比较低，docker油然而生**。

## docker学习

docker的隔离是通过隔离进程实现。

程序员可通过编写dockerfile编译成镜像，即可发布到各个环境运行。

![image-20220329152427061](https://cdn.jsdelivr.net/gh/cheerfulman/picGo/img/20220329152427.png)

使用docker的基本形式：

![image-20220329154335982](https://cdn.jsdelivr.net/gh/cheerfulman/picGo/img/20220329154336.png)

### docker简介

+ image镜像，构建容器。好比应用程序所需要运行的环境
+ container,容器，跑相关的应用程序
+ 镜像仓库(dockerhub)（保留镜像文件、提供上传、下载镜像）好比gitlab
+ Dockerfile: 将你部署项目的操作，写成一个脚本，还可以构建出镜像文件

### 启动第一个docker容器

```txt
1. 获取镜像
2. 运行镜像，生成容器。
```

利用docker运行Nginx

>1. 获取镜像，从配置好的镜像站中，拉取Nginx镜像
>
> docker search nginx
>
>2. 拉取下载镜像
>
>docker pull nginx
>
>![image-20220329162836966](https://cdn.jsdelivr.net/gh/cheerfulman/picGo/img/20220329162837.png)
>
>3. 查看自己是否下载成功
>
>docker image ls
>
>如果nginx镜像已存在，我们可以删除
>
>docker rmi {id}
>
>4. 运行该nginx镜像，此时就在容器中跑了一个Nginx服务
>
>docker run 参数  {镜像名/id}
>
>-d 后台运行容器
>
>-p 80:80 端口映射， 宿主机端口:容器端口， 访问宿主机端口及访问到容器内端口
>
>docker run -d -p 80:80 nginx
>
>5. 运行后返回容器内id
>
>C:\Users\admin>docker run -d -p 80:80 nginx
>160b0ed31f814d9a867caba742a8e450d69a529adc7571d7597e3aea749ef475
>
>使用docker ps查看运行的容器
>
>C:\Users\admin>docker ps
>CONTAINER ID   IMAGE     COMMAND                  CREATED          STATUS          PORTS                NAMES
>160b0ed31f81   nginx     "/docker-entrypoint.…"   48 seconds ago   Up 46 seconds   0.0.0.0:80->80/tcp   priceless_pascal
>
>6. 即可通过ip:80 访问nginx服务
>7. 停止具体容器
>
>docker stop 160b0ed31f8
>
>8. 再次启动
>
>docker start 160b0ed31f8 即可

### docker生命周期

一图看懂docker生命周期：

![image-20220329164826126](https://cdn.jsdelivr.net/gh/cheerfulman/picGo/img/20220329164826.png)

### docker镜像原理

我们获取镜像时，发现下载了多行信息，最终得到一个完整的镜像文件。

```powershell
C:\Users\admin>docker pull nginx
Using default tag: latest
latest: Pulling from library/nginx
ae13dd578326: Pull complete
6c0ee9353e13: Pull complete
dca7733b187e: Pull complete
352e5a6cac26: Pull complete
9eaf108767c7: Pull complete
be0c016df0be: Pull complete
Digest: sha256:4ed64c2e0857ad21c38b98345ebb5edb01791a0a10b0e9e3d9ddde185cdbd31a
Status: Downloaded newer image for nginx:latest
docker.io/library/nginx:latest
```

常用的centos7系统由两部分组成：

+ linux内核
+ centos发行版（还有ubuntu、suse发行版）

如果要切换不同的发行版即可通过docker，对不同的镜像进行切换即可。内核都是Linux内核。

![image-20220329170518723](https://cdn.jsdelivr.net/gh/cheerfulman/picGo/img/20220329170518.png)

docker镜像原理理解第一步：

![image-20220329213853010](https://cdn.jsdelivr.net/gh/cheerfulman/picGo/img/20220329213853.png)

docker镜像定义，如果我们需要一个Mysql5.6的镜像：

+ 获取基础镜像，选择一个发行平台(ubutu,centos)
+ 在centos镜像中安装mysql5.6

从导出过程可以看出，是一层层添加的，底层是centos镜像，上层是mysql

![image-20220329214806291](https://cdn.jsdelivr.net/gh/cheerfulman/picGo/img/20220329214806.png)

> 进入docker容器
>
> docker ps
>
> **-id 代表进入交互模式**
>
> docker exec -it {运行的id} bash
>
> 在Linux 上 cat /etc/os-release 可以看到自己的运行的系统版本

通过这种分层镜像，可以**共享资源**

> 既然docker是共享底层镜像，那如何进行文件的增删改查的，在 容器1删除基础镜像是否会影响容器2？

![image-20220329215833838](https://cdn.jsdelivr.net/gh/cheerfulman/picGo/img/20220329215834.png)

![image-20220329215953125](https://cdn.jsdelivr.net/gh/cheerfulman/picGo/img/20220329215953.png)

### docker镜像实际使用

#### 获取镜像

镜像托管仓库，好比yum镜像源：默认docker仓库，dockerhub有大量优质的镜像，以及用户自己上传的镜像

+ docker pull {镜像}  来拉取镜像
+ docker info | grep "Root" /var/lib/docker 查看docker镜像目录的地方
+ 如果cat某具体文件，得到一个json文件，记录镜像和容器的配置关系

#### 查看docker镜像

docker images {镜像} 查看具体镜像

docker images 查看所有镜像

docker images {镜像}:tag 查看具体版本镜像

docker images -q 查看所有镜像id

docker images --format "{{.ID}}--{{.Repository}}" 格式化显示镜像

docker images --format "table {{.ID}}\t{{.Repository}}\t{{.Tag}}"  制表查看镜像

docker search {镜像名} 搜索dockerhub镜像

#### 删除镜像

docker pull {镜像}拉取镜像

docker rmi {镜像名等} 根据镜像id，名字，摘要删除

#### 镜像综合用法

```txt
docker rmi `docker images -aq`： 批量删除镜像
docker rm `docker ps -aq`： 批量删除容器
docker commit 
docker image save {镜像名:版本号} > '{导出地址}' :导出镜像
docker image load -i {文件地址}:导入镜像
docker info:查看相关信息
docker image inspect {镜像id} : 看到列出的Json信息
```

### docker容器管理

`docker run` 等于创建+启动

**容器内进程必须处于前台运行状态，否则会直接退出，容器内什么也没做，容器也会挂掉**

后台则是：nohub {启动进程} &, 可通过jobs 查看，然后利用fg {后台进程id}让其跑在前台。

```perl
# 1. 运行挂掉的容器
docker run {容器id} -- 这样会产生多条容器记录，因为该容器内没有程序在跑。
# 2. 运行容器，且进入容器执行某命令
docker run -it {镜像名:tag} sh|bash
# 3. 开启一个容器并让其帮你运行某个程序,会卡住一个中断
docker run {镜像名:tag} ping baidu.com
#4. 运行活着的容器，docker ps可以查看到 
-d 参数，
--rm 容器挂掉后自动删除
--name 给容器命名
docker run -d {镜像名:tag} ping baidu.com : 返回一个容器id
# 5. 查看容器日志
docker logs -f {容器id}
# 6. 进入容器空间内
exec 用来进入容器内
docker exec -it {容器id} bash
# 7. 查看容器详细信息
docker container inspect {容器id}
# 8. 容器端口映射
-p 82:80 则宿主82映射80
-P 后面什么都不加，则是随机一个端口映射
# 9. 查看容器转发情况
docker port {容器Id}
# 10. 容器的提交
docker commit {容器id} 新的镜像名
# 11. 改名
docker tag {容器id} {new_name}
# 12. 查看容器
docker ps | docker container ls
# 13. 查看容器内进程信息
docker top {容器id}
# 13. 查看容器内资源信息
docker status {容器id}
```

### dockerFile学习

基础镜像 FROM centos:7.8

制作镜像操作指令 RUN yum install mysql

容器启动时执行指令 CMD ['/bin/bash']

MAINTAINER 指定谁维护信息，可以没有

ADD 添加一个宿主机文件到容器内 (copy文件，会自动解压)

COPY 作用和add一样，都是拷贝文件到容器内，而ADD有自动解压功能

workdir 设置当前工作目录

volume 设置挂载主机目录

expose 指定对外的端口

#### ENTRYPOINT

和RUN指令一样，分两种格式

+ exec
+ run

当指定了entrypoint之后，cmd指令就变成，把cmd内容当参数传递给entrypoint

#### ARG和ENV

设置环境变量env

```perl
ENV NAME="Cheer"
ENV AGE="18"
ENV MYSQL_VERSION=5.6

ARG于ENV都一样 设置环境变量
区别ENV 无论在镜像构建还是该容器运行都可以用
而ARG只用于构建镜像，容器运行时消失
```

#### VOLUME

容器运行时，应保证在存储层不写入任何数据，容器内产生的数据，推荐挂在写入到宿主机上

```perl
VOLUME /data # 将容器内的/data文件夹，在容器运行时将该目录挂载为匿名卷，任何在该目录写入数据的操作，都不会被容器记录。

FROM centos
MAINTAINER Cheer
VOLUME ["/data1", "/data2"]
```

1. 通过dockerfile指定volume
2. 通过docker run -v参数直接设置挂载目录

#### EXPOSE

 指定对外提供的端口服务

```perl
docker port 容器
docker run -p 宿主机端口:容器端口
docker run -P 随机宿主机端口:容器内端口
```

#### WORKDIR

用于dockerfile中，目录切换，更改工作目录

WORKDIR /opt

#### USER

改变环境，用于切换用户

```perl
USER root
USER CheerJia
```

