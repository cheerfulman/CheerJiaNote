## HTTP 相关库

注册默认路由：`http.HandleFunc`

```go
// 第一个参数是请求路由，第二个表示需要处理的事情
func HandleFunc(pattern string, handler func(ResponseWriter, *Request)) {
   DefaultServeMux.HandleFunc(pattern, handler)
}
```

开启服务方法：创建了一个Server类型的数据，通过addr 和handler 来初始化。

```go
func ListenAndServe(addr string, handler Handler) error {
   server := &Server{Addr: addr, Handler: handler}
   return server.ListenAndServe()
}
```

主服务器代码：

```go
package main

import (
	"gocode/gcrawlerImooc/errorhanlding/filelistingserver/filelisting"
	"log"
	"net/http"
	_ "net/http/pprof"
	"os"
)

type appHandler func(writer http.ResponseWriter, request *http.Request) error
// 将HandleFunc 中的 handler 提出，并且 设置一个 type appHandler 用来接收返回的erro
// 在 errWrapper中对错误进行统一的处理
func errWrapper(handler appHandler) func(http.ResponseWriter, *http.Request) {
	return func(writer http.ResponseWriter, request *http.Request) {
		err := handler(writer,request)
		if err != nil {
			// 日志
			log.Printf("Error handing request: %s", err.Error())
			code := http.StatusOK
			switch {
			case os.IsNotExist(err):
				code = http.StatusNotFound
			case os.IsPermission(err):
				code = http.StatusForbidden
			default:
				code = http.StatusInternalServerError
			}
			http.Error(writer,http.StatusText(code),code)
		}
	}
}

func main() {
	http.HandleFunc("/list/", errWrapper(filelisting.HandleFileListing))

	err := http.ListenAndServe(":8888", nil)
	if err != nil {
		panic(err)
	}
}
```

handler函数：

```go
package filelisting

import (
	"io/ioutil"
	"net/http"
	"os"
)

const prefix = "/list/"

func HandleFileListing(writer http.ResponseWriter, request *http.Request) error {
	//fmt.Println(request.URL.Path,len("/list/"))
	path := request.URL.Path[len(prefix):]
	file, err := os.Open(path)
	if err != nil {
		//panic(err)
		//http.Error(writer,err.Error(),http.StatusInternalServerError)
		return err
	}
	defer file.Close()

	all, err := ioutil.ReadAll(file)
	if err != nil {
		//panic(err)
		return err
	}

	writer.Write(all)
	return nil
}
```

客户端方面：

```go
package main

import (
	"fmt"
	"net/http"
	"net/http/httputil"
)

func main() {
	// http 请求
	request, err := http.NewRequest(http.MethodGet, "http://www.imooc.com", nil)
	// 给其加上头部
	request.Header.Add("User-Agent",
		"Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1")
	// 创建客户端
	client := http.Client{
		// 查看其转发路径
		CheckRedirect: func(req *http.Request, via []*http.Request) error {
			fmt.Println("Redirect:", req)
			return nil
		},
	}
	// 发生请求
	resp, err := client.Do(request)
	//resp, err := http.DefaultClient.Do(request)

	// 获取 头部，日期，路由via 等信息
	//resp, err := http.Get("http://www.imooc.com")
	fmt.Printf("%v\n",resp)
	if err != nil {
		panic(err)
	}
	defer resp.Body.Close()
	// 得到返回的body 和 header
	s, err := httputil.DumpResponse(resp,true)
	if err != nil {
		panic(err)
	}

	fmt.Printf("Received is %s\n", s)

}
```

+ godoc -http 8888 可以生成文档 