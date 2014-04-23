<?php
$db = array(
		'host'=>'192.168.2.163',
		'user'=>'gcstatistic',
		'pswd'=>'gcstatistic7232275'
);
 
$querytype = array(
		'user' =>array(
			'name' => '用户',
			'child' => array(
				'user_number' => array(
						'name' => '注册用户数量' ,
						'sql' => "select '注册用户数量', count(*) from gongchanginfo.gc_userinfo where regtime > {starttime}  and regtime < {endtime}"
					)
				)
		),
		'offer' => array(
			'name' => '询价/采购',
			'child' => array(
				'sendenq' => array(
					'name' => '采购 + 询价',
					'sql' => "select '采购 + 询价', count(*) from productoffer.pd_sendenq where pubtime > {starttime}  and pubtime < {endtime}"
				),
				'sendenq1' => array(
					'name' => '询价数量',
					'sql' => "select '询价数量', count(*) from productoffer.pd_recenquiry  WHERE  pubtime > {starttime}  and pubtime < {endtime} and `enquirytype` =1 "
				)
			)
		),
		'product' => array(
				'name' => '产品',
				'child' => array(
				 'pd_public' => array(
						'name' => '发布产品总数' ,
						'sql' => "select '发布产品总数', count(*) from productinfo.pd_info where pubtime > {starttime}  and pubtime < {endtime}"
					),
					'pd_cid_public' => array(
						'name' => '注册企业并发布产品的产品数量' ,
						'sql' => "select '注册企业并发布产品的产品数量', count(*) from productinfo.pd_info a, gongchanginfo.gc_company b where a.cid = b.cid and a.pubtime > {starttime} and a.pubtime < {endtime} and b.addtime > {starttime} and b.addtime < {endtime}"
					),
					/*'pd_offer' => array(
						'name' => '求购数量' ,
						'sql' => "select '求购数量', count(*) from productoffer.pd_sendenq where pubtime > {starttime} and pubtime < {endtime}  "
					),
					'pd_cid_offer' => array(
						'name' => '新注册企业求购数量' ,
						'sql' => "select '新注册企业求购数量', count(*) from productoffer.pd_sendenq a, gongchanginfo.gc_company b where a.cid = b.cid and  a.pubtime > {starttime} and a.pubtime < {endtime} and b.addtime > {starttime} and b.addtime < {endtime} ",
					)*/
				)
			),
		//其它分类
	'company' => array(
				'name' => '企业注册',
				'child' => array(
					'com_reg' => array(
						'name' => '注册企业的总量' ,
						'sql' => "select '注册企业的总量', count(*) from  gongchanginfo.gc_company where  addtime > {starttime} and addtime < {endtime}"
						),
					'com_pubproduct' => array(
						'name' => '注册且发布产品的企业数量' ,
						'sql' => "select '注册且发布产品的企业数量', count(*) from gongchanginfo.gc_company a where  addtime > {starttime} and addtime < {endtime} and EXISTS (SELECT pid FROM productinfo.pd_info b where b.cid = a.cid and b.pubtime > {starttime} and b.pubtime  < {endtime}   limit 1) "
						),
					
					'com_takes' => array(
						'name' => '企业申领总量' ,
						'sql' => "select '企业申领总量', count(*) from gcoperate.ad_comclaim where   addtime > {starttime} and  addtime < {endtime} "
					)
				)
			),

		'audit' => array(
				'name' => '审核状态',
				'child' => array(
					'audit_companystatus_true' => array(
						'name' => '通过审核的企业数量' ,
						'sql' => "select '通过审核的企业数量', count(*) from gcoperate.ad_company a, gongchanginfo.gc_company b where a.checktime > {starttime} and a.checktime < {endtime} and a.cid = b.cid	and b.status = 1"
						),
					'audit_companystatus_false' => array(
						'name' => '拒审的企业数量' ,
						'sql' => "select '拒审的企业数量', count(*) from gcoperate.ad_company a, gongchanginfo.gc_company b where a.checktime > {starttime} and a.checktime < {endtime} and a.cid = b.cid	and b.status = -1"
						),
					'audit_company_takes_true' => array(
						'name' => '企业申领通过审核' ,
						'sql' => "select '企业申领通过审核', count(*) from  gcoperate.ad_comclaim   where  addtime > {starttime} and  addtime < {endtime}  and checked = 1",
					),
					'audit_company_takes_false' => array(
						'name' => '企业申领未通过审核' ,
						'sql' => "select '企业申领未通过审核', count(*) from  gcoperate.ad_comclaim where  addtime > {starttime} and  addtime < {endtime}  and checked = -1 ",
					)
				)
			),

		'comtype' => array(
				'name' => '企业分类',
				'child' => array(
				
				'com_busmode' => array(
						'name' => '企业数量' ,
						'sql' => "select busmode, count(*) from gongchanginfo.gc_company  where  addtime > {starttime} and addtime < {endtime}  group by busmode order by busmode"
					),
					'audit_company_true' => array(
						'name' => '审核通过的企业' ,
						'sql' => "select a.busmode ,count(*) from gongchanginfo.gc_company a, gcoperate.ad_company b where a.status = 1 and a.cid =b.cid and a.addtime > {starttime} and a.addtime < {endtime}  group by a.busmode order by busmode"
						),

					'audit_company_false' => array(
						'name' => '被拒审的企业' ,
						'sql' => "select busmode ,count(*) from gongchanginfo.gc_company a, gcoperate.ad_company b where a.status = -1 and a.cid =b.cid and a.addtime > {starttime} and a.addtime < {endtime}  group by a.busmode order by busmode"
						) 
					),

				'replacename' => array(
							'1' => '生产型',
							'3' => '贸易型',
							'4' => '其他型',
							'5' => '服务型'
						   )
			),
		
		'buslicense' => array(
				'name' => '营业执照',
				'child' => array(
					'buslicense_submit' => array(
						'name' => '营业执照提交总量' ,
						'sql' => "select '营业执照提交总量',count(*) from  gongchanginfo.gc_buslicense  where addtime > {starttime} and  addtime < {endtime} ",
						),

					'buslicense_audit_true' => array(
						'name' => '营业执照审核通过' ,
						'sql' => "select '营业执照审核通过', count(*) from  gongchanginfo.gc_buslicense where  addtime > {starttime} and addtime < {endtime} and status = 1",
						) ,
					'buslicense_audit_false' => array(
						'name' => '营业执照拒审' ,
						'sql' => "select '营业执照拒审',count(*) from  gongchanginfo.gc_buslicense where  addtime > {starttime} and addtime < {endtime} and status = -1",
						)
				)
			),

	'industry' => array(
				'name' => '行业分类',
				'child' => array(
					'regcom' => array(
						'name' => '注册企业' ,
						'sql' => "select cate1 ,count(*) from gongchanginfo.gc_company  where addtime > {starttime} and  addtime < {endtime}   group by cate1 order by cate1",
						),
						
					'auditcom2' => array(
						'name' => '认证用户' ,
						'sql' => "select cate1 ,count(*) from gongchanginfo.gc_company a , gongchanginfo.gc_userinfo b  where a.addtime > {starttime} and  a.addtime < {endtime} and a.uid = b.uid and b.usergroup = 3  group by a.cate1 order by cate1",
						) ,

					'gongchanguser' => array(
						'name' => '工厂用户' ,
						'sql' => "select cate1 ,count(*) from gongchanginfo.gc_company a , gongchanginfo.gc_userinfo b where a.addtime > {starttime} and  a.addtime < {endtime} and a.uid = b.uid and b.usergroup = 4 group by a.cate1 order by cate1",
						),

					'payuser' => array(
						'name' => '付费用户' ,
						'sql' => "select cate1 ,count(*) from gongchanginfo.gc_company a , gongchanginfo.gc_userinfo b where a.addtime > {starttime} and  a.addtime < {endtime} and a.uid = b.uid and b.usergroup = 5 group by a.cate1 order by cate1",
						)
				),
				'replacenamefunction' =>  array(
					'function' => 'getcatefromjson',
					'param' => array('http://cate.ch.gongchang.com/cate_json/?name=getcate&key=cate_0')	
				)
			),
	'club_industy' => array(
				'name' => '行业排行',
				'child' => array(
				'club_sortbyenq' => array(
						'name' => '行业排行（按询价数）' ,
							'sql' => "select cate2, count(*) as num from productoffer.pd_sendenq where  pubtime > {starttime} and  pubtime < {endtime}  and cate2 > 0  group by cate2 order by num desc limit 0,50"
						),
					'club_sortbycom' => array(
						'name' => '行业排行（按企业数）' ,
						'sql' => "select cate2, count(*) as num from gongchanginfo.gc_company where  addtime > {starttime} and  addtime < {endtime}  and cate2 > 0  group by cate2 order by num desc limit 0,50",
						),
					'club_sortbybuslogo' => array(
						'name' => '行业排行（按营业执照数）' ,
						'sql' => "select cate2, count(*) as num from gongchanginfo.gc_company where  addtime > {starttime} and  addtime < {endtime}  and licensestatus = 1  and cate2 > 0 group by cate2 order by num desc limit 0,50 ",
						),
				'club_sortbypdinfo' => array(
						'name' => '行业排行（按发布产品数）' ,
						'sql' => "select cate2 ,count(*) as num from productinfo.pd_info where pubtime > {starttime} and pubtime < {endtime} and cate2 > 0  group by cate2  order by num desc limit 0,50",
						),
			   'club_sortbypdinfo' => array(
						'name' => '行业排行（按询价数量）' ,
						'sql' => "select cate2 ,count(*) as num from productoffer.pd_sendenq where pubtime > {starttime} and pubtime < {endtime} and cate2 > 0  group by cate2  order by num desc limit 0,50",
						)
					),
				'orireplace' => array(
					'function' => 'getcate2',
					'param' => 'empty'
				)
		)
	);



