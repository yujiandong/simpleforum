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
3. 插件系统需要用到scandir方法，如php.ini中disable_functions设置屏蔽掉了这个方法，请删除后重启。
4. core/config目录下db.php,params.php,plugins.php,web.php文件要求写权限
5. core/runtime目录及里面的目录文件要有写权限
6. 根目录下的assets目录，avatar目录和upload目录要求写权限

**安装步骤：**

1. 下载simpleforum安装文件，解压
2. 将整个安装包上传到网站空间
3. 在浏览器中输入网址： http://你的网址/install.php 进入安装界面
4. 安装第1步：会显示你的网站空间环境是否符合安装条件，如果符合请点击进入下一步
5. 安装第2步：填写mysql数据库信息，填写完后，会执行sql生成表及插入数据，自动进入下一步
6. 安装第3步：创建管理员帐号，如果你的网站空间开启了opcache等加速扩展，可能会报错，请稍等片刻，再重新填写。
7. 管理员帐号创建后，安装操作就完成了。

**帮助文档：**

1. Nginx配置 https://simpleforum.org/t/23
2. SMTP服务器设置 https://simpleforum.org/t/24
3. 开启验证码 https://simpleforum.org/t/25
4. 编辑器选择 https://simpleforum.org/t/26
5. 开启缓存 https://simpleforum.org/t/27
6. 开启第三方帐号登录 https://simpleforum.org/t/28
7. 用户名过滤 https://simpleforum.org/t/29
8. 网站验证 https://simpleforum.org/t/30
9. 上传设置 https://simpleforum.org/t/31
10. 用户组设定 https://simpleforum.org/t/32
10. 模板设计 https://simpleforum.org/t/33
11. 图片上传设定的修改 https://simpleforum.org/t/34
12. 积分设置 https://simpleforum.org/t/35
13. 启用https的方法 https://simpleforum.org/t/20
