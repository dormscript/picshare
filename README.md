## windows安装 
* 1：安装wamp环境
* 2：复制该程序到根目录，修改function.php中的“图片路径”（根目录下function.php第2行），如:“G:/公司照片共享”
* 3：启动gd2库
* 4：将“图片路径”添加别名，将http.cnf改为utf-8编码

	如:修改http.cnf
	Alias /share G:/公司照片共享
	<Directory "G:/公司照片共享/">
	    Options Indexes FollowSymLinks MultiViews
	    AllowOverride All
	    Order Deny,Allow
	    Allow from all
	</Directory>


## linux安装方法：
* 1：  新建虚拟主机，将此程序放到虚拟主机根目录 
* 2:   在nginx重定向图片目录；
	location /share/{<br/>
            alias /Network/photo/;<br/>
        }<br/>
3: 修改程序中存储的图片目录位置： （根目录下function.php第2行）<br/>
   define('PIC_DIR',  "/Network/photo");  <br/>
   define('PIC_URL',  "/share");<br/><br/>

4: 确认图片目录有写入权限<br/>


附:虚拟主机配置:<br/>
    server {
        listen       80;<br/>
        server_name  www.picshare.com;<br/>
        root   /Volumes/Data/webroot/picshare;<br/>
        index  index.html index.htm index.php;<br/>
        location / {   <br/>
            if (!-f $request_filename){<br/>
               rewrite ^/(.+)$ /index.php?$1& last;<br/>
            }<br/>
        }<br/>
        location ~ \.php$ {<br/>
            fastcgi_pass   127.0.0.1:9000;<br/>
            fastcgi_index  index.php;<br/>
            include        fastcgi.conf;<br/>
        }<br/>
        location /share/{<br/>
            alias /Network/photo/;<br/>
        }<br/>
    }


