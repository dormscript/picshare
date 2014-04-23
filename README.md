windows安装

1：安装wamp环境
2：修改function.php中的“图片路径”
3：启动gd2库
4：将“图片路径”添加虚拟目录，将http.cnf改为utf-8编码
如:修改http.cnf

Alias /share G:/公司照片共享
<Directory "G:/公司照片共享/">
    Options Indexes FollowSymLinks MultiViews
    AllowOverride All
    Order Deny,Allow
    Allow from all
</Directory>




linux安装方法：
1：  新建虚拟主机，将此程序放到虚拟主机根目录 
2:   在nginx重定向图片目录；
	location /share/{
            alias /Network/photo/;
        }
3: 修改程序中存储的图片目录位置： （根目录下function.php第2行）
   define('PIC_DIR',  "/Network/photo");  
   define('PIC_URL',  "/share");

4: 确认图片目录有写入权限


附虚拟主机配置:
    server {
        listen       80;
        server_name  www.picshare.com;
        root   /Volumes/Data/webroot/picshare;
        index  index.html index.htm index.php;
        location / {   
            if (!-f $request_filename){
               rewrite ^/(.+)$ /index.php?$1& last;
            }
        }
        location ~ \.php$ {
            fastcgi_pass   127.0.0.1:9000;
            fastcgi_index  index.php;
            include        fastcgi.conf;
        }
        location /share/{
            alias /Network/photo/;
        }
    }


