**介绍：**

极简论坛系统，功能简单，界面简洁，移动优先。

功能简单
只有基本的发帖，回帖，及一些会员服务功能

界面简洁
没有花哨的设计，简单明了

移动优先
前端采用bootstrap，适配电脑，平板，手机

**空间及环境要求：**

1. PHP 5.4.0及以上
2. 必须安装open_ssl扩展
3. core/config目录下db.php和params.php文件要求写权限
4. core/runtime目录及里面的目录文件要有写权限
5. 根目录下的assets目录和avatar目录要求写权限

**安装步骤：**

1. 下载simpleforum安装文件，解压
2. 修改core/config/web.php第19行，随便改几位就行了，这是检证cookie用的
 ```
 'cookieValidationKey' => 'hwdn8-iyIh5LylPLpD1PoplqjUka98Ba',
 ```
3. 将整个安装包上传到网站空间
4. 在浏览器中输入网址： http://你的网址/install.php  进入安装界面
5. 安装第一步：会显示你的网站空间环境是否符合安装条件，如果符合请点击进入下一步
6. 安装第二部：填写mysql数据库信息，填写完后，会执行sql生成表及插入数据，自动进入下一步
7. 安装第三部：创建管理员帐号，如果你的网站空间开启了opcache等加速扩展，可能会报错，请稍等片刻，再重新填写。
8. 管理员帐号创建后，安装操作就完成了。

**帮助文档：**

1. Nginx配置 http://simpleforum.org/t/49
2. SMTP服务器设置 http://simpleforum.org/t/39
3. 开启验证码 http://simpleforum.org/t/43
4. 编辑器选择 http://simpleforum.org/t/41
5. 开启缓存 http://simpleforum.org/t/3
6. 开启第三方帐号登录 http://simpleforum.org/t/2
7. 用户名过滤 http://simpleforum.org/t/54
8. 网站验证 http://simpleforum.org/t/55
9. 上传设置 http://simpleforum.org/t/83
