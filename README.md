# php_tiny_iframe

只用来做接口的PHP微型框架

### 统一入口

主目录下的interface.php为唯一入口，无需配置路由，接口文件按指定规则命名，一个文件对应一个接口

interface.php对输入参数，通过禁用危险字符防止sql注入，则实际接口中无需再校验参数

nginx配置只开放interface.php，其他文件都禁止访问，配置如：

	server {
    	listen      80;
    	server_name localhost;
    	location ^~ /interface.php {
	 		root /data/php_interface;
         		fastcgi_pass   127.0.0.1:9000;
         		include        fastcgi_params;
         		fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
         		fastcgi_param SERVER_NAME $http_host;
         		fastcgi_ignore_client_abort on;
         		fastcgi_connect_timeout 30;
         		fastcgi_send_timeout 30;
         		fastcgi_read_timeout 30;
         		fastcgi_buffer_size 64k;
         		fastcgi_buffers 4 64k;
         		fastcgi_busy_buffers_size 128k;
         		fastcgi_temp_file_write_size 128k;
		}
		location / {
			deny all;
		}
	}

### 系统配置文件

conf/common.inc.ini，控制开发、测试、生产环境，各有一个单独.ini文件，具体见conf/目录下


### 读配置基础类

common/ConfFactory.php，直接使用getConf($key)读取配置文件，读取示例参见dao/dao_local_db.class.php


### 访问权限控制

common/Auth.php，由isAuthAllowIn()统一控制，示例代码只限制了访问IP白名单，IP白名单配置见系统配置.ini文件


### 日志基本类

common/SysLog.php，直接使用函数sys_log($msg)打日志

日志文件放在log/目录下，按天生成日志文件，注意给log目录设置777权限：chmod 777 log

每条日志默认加前缀：“[时间][进程ID][客户端IP][服务器IP][执行PHP文件][函数][行数] ”


### 系统公用函数

common/GlobalFunction.php，主要包含curl的get、post请求，系统返回值格式化

及一些常规字符串校验，获取客户端、服务器IP等


### 错误码定义

common/ErrorCode.php，其中0表示成功，其他值都是错误


### 数据库操作类

dao/dao.class.php为基础类，实际使用方法见dao/dao_local_db.class.php


### 开发接口

interface/目录下放实际开发接口，文件按指定规则命名

如 Test_FirstInterface.php，以下划线分割，test为目录，支持目录嵌套

具体路由规则见common/GlobalFunction.php中的instance($interfaceName)函数

### 调用方式

http://127.0.0.1/interface.php?interfaceName=Test_FirstInterface&name=xxx


