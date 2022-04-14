## 记录

### @RestController和@Controller区别

@RestController 代表@Controller+@ResponseBody，表示返回结构直接写入**HTTP resp Body**中，可以返回**json**数据

@Controller通常返回到跳转路径，如果路径错误则404 Not Found。