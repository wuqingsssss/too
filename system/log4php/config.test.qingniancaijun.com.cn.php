<?php
return array ('appenders' => array (
				'default' => array (
						'class' => 'LoggerAppenderFile',
						'params' => array (
								'file' => DIR_LOGS . 'log.log',
								'pattern'=>'{"_id":"%date{YmdHis}.%r{_t}.%def{DIR_DIR}%req{route}.%C.%M:%L.%ld{order_id}.%ld{telephone|shipping_mobile|mobile}","timestamp":"%date{Y-m-d H:i:s}.%ses{customer_id}","times":"%r", "logger":"%logger", "pid":"%pid", "level":"%level","client":"%s{REMOTE_ADDR}:%s{REMOTE_PORT} %.220s{HTTP_USER_AGENT}", "server":"%s{HTTP_HOST}(%s{LOCAL_ADDR}%s{SERVER_ADDR}):%s{SERVER_PORT}", "user_id":"%ses{user_id}", "method":"%def{DIR_DIR}%req{route}::%C.%M:%L", "message":"%.4000message"}'	
						) 
				),
				/*'sys' => array (
						'class' => 'LoggerAppenderMongoDB',
						'params' => array (
								'host' => 'mongodb://10.173.29.26',
								'port'=>'27017',
								'username'=>'log_qncj',
								'password'=>'LOG14070810PassQNCJ',
								'databaseName' => 'web_log_test',
								'collectionName' => 'log' ,
								'pattern'=>'{"_id":"%date{YmdHis}.%r{_t}.%def{DIR_DIR}%req{route}.%C.%M:%L.%ld{order_id}.%ld{telephone|shipping_mobile|mobile}","timestamp":"%date{Y-m-d H:i:s}.%ses{customer_id}", "logger":"%logger", "pid":"%pid", "level":"%level","client":"%s{REMOTE_ADDR}:%s{REMOTE_PORT} %.220s{HTTP_USER_AGENT}", "server":"%s{HTTP_HOST}(%s{LOCAL_ADDR}%s{SERVER_ADDR}):%s{SERVER_PORT}", "user_id":"%ses{user_id}", "method":"%def{DIR_DIR}%req{route}::%C.%M:%L", "message":"%.4000message"}'
						) 
				),
				'order_ref' => array (
						'class' => 'LoggerAppenderMongoDB',
						'params' => array (
								'host' => 'mongodb://10.173.29.26',
								'port'=>'27017',
								'username'=>'log_qncj',
								'password'=>'LOG14070810PassQNCJ',
								'databaseName' => 'web_log_test',
								'collectionName' => 'log_order' ,
								'pattern'=>'{"_id":"%date{YmdHis}.%r{_t}.%def{DIR_DIR}%req{route}.%C.%M:%L.%ld{order_id}.%ld{telephone|shipping_mobile|mobile}","timestamp":"%date{Y-m-d H:i:s}", "logger":"%logger", "pid":"%pid", "level":"%level","client":"%s{REMOTE_ADDR}:%s{REMOTE_PORT} %.220s{HTTP_USER_AGENT}", "server":"%s{HTTP_HOST}(%s{LOCAL_ADDR}%s{SERVER_ADDR}):%s{SERVER_PORT}", "user_id":"%ses{user_id}", "method":"%def{DIR_DIR}%req{route}::%C.%M:%L", "message":"%.4000message"}'
						)
				),
				'payment_ref' => array (
						'class' => 'LoggerAppenderMongoDB',
						'params' => array (
								'host' => 'mongodb://10.173.29.26',
								'port'=>'27017',
								'username'=>'log_qncj',
								'password'=>'LOG14070810PassQNCJ',
								'databaseName' => 'web_log_test',
								'collectionName' => 'log_payment' ,
								'pattern'=>'{"_id":"%date{YmdHis}.%r{_t}.%def{DIR_DIR}%req{route}.%C.%M:%L.%ld{order_id}.%ld{telephone|shipping_mobile|mobile}","timestamp":"%date{Y-m-d H:i:s}", "logger":"%logger", "pid":"%pid", "level":"%level","client":"%s{REMOTE_ADDR}:%s{REMOTE_PORT} %.220s{HTTP_USER_AGENT}", "server":"%s{HTTP_HOST}(%s{LOCAL_ADDR}%s{SERVER_ADDR}):%s{SERVER_PORT}", "user_id":"%ses{user_id}", "method":"%def{DIR_DIR}%req{route}::%C.%M:%L", "message":"%.4000message"}'
						)
				),
				'admin_ref' => array (
						'class' => 'LoggerAppenderMongoDB',
						'params' => array (
								'host' => 'mongodb://10.173.29.26',
								'port'=>'27017',
								'username'=>'log_qncj',
								'password'=>'LOG14070810PassQNCJ',
								'databaseName' => 'web_log_test',
								'collectionName' => 'log_admin' ,
								'pattern'=>'{"_id":"%date{YmdHis}.%r{_t}.%def{DIR_DIR}%req{route}.%C.%M:%L.%ld{order_id}.%ld{telephone|shipping_mobile|mobile}.%ses{customer_id}","timestamp":"%date{Y-m-d H:i:s}","times":"%r", "logger":"%logger", "pid":"%pid", "level":"%level","client":"%s{REMOTE_ADDR}:%s{REMOTE_PORT} %.220s{HTTP_USER_AGENT}", "server":"%s{HTTP_HOST}(%s{LOCAL_ADDR}%s{SERVER_ADDR}):%s{SERVER_PORT}", "user_id":"%ses{user_id}", "method":"%def{DIR_DIR}%req{route}::%C.%M:%L", "message":"%.4000message"}'
						)
				),
				'sql_ref' => array (
						'class' => 'LoggerAppenderMongoDB',
						'params' => array (
								'host' => 'mongodb://10.173.29.26',
								'port'=>'27017',
								'username'=>'log_qncj',
								'password'=>'LOG14070810PassQNCJ',
								'databaseName' => 'web_log_test',
								'collectionName' => 'log_sql' ,
								'pattern'=>'{"_id":"%date{YmdHis}.%r{_t}.%def{DIR_DIR}%req{route}.%C.%M:%L.%ld{order_id}.%ld{telephone|shipping_mobile|mobile}","timestamp":"%date{Y-m-d H:i:s}","times":"%r", "logger":"%logger", "pid":"%pid", "level":"%level","client":"%s{REMOTE_ADDR}:%s{REMOTE_PORT} %.220s{HTTP_USER_AGENT}", "server":"%s{HTTP_HOST}(%s{LOCAL_ADDR}%s{SERVER_ADDR}):%s{SERVER_PORT}", "method":"%def{DIR_DIR}%req{route}::%C.%M:%L", "message":"%.4000message"}'
						)
				)*/
		'order_ref' => array (
				'class' => 'LoggerAppenderFile',
				'params' => array (
						'file' => DIR_LOGS . 'log_order.log',
						'pattern'=>'{"_id":"%date{YmdHis}.%r{_t}.%def{DIR_DIR}%req{route}.%C.%M:%L.%ld{order_id}.%ld{telephone|shipping_mobile|mobile}","timestamp":"%date{Y-m-d H:i:s}.%ses{customer_id}", "logger":"%logger", "pid":"%pid", "level":"%level","client":"%s{REMOTE_ADDR}:%s{REMOTE_PORT} %.220s{HTTP_USER_AGENT}", "server":"%s{HTTP_HOST}(%s{LOCAL_ADDR}%s{SERVER_ADDR}):%s{SERVER_PORT}", "user_id":"%ses{user_id}", "method":"%def{DIR_DIR}%req{route}::%C.%M:%L", "message":"%.4000message"}'
				)
		),
		'payment_ref' => array (
				'class' => 'LoggerAppenderFile',
				'params' => array (
						'file' => DIR_LOGS . 'log_payment.log',
						'pattern'=>'{"_id":"%date{YmdHis}.%r{_t}.%def{DIR_DIR}%req{route}.%C.%M:%L.%ld{order_id}.%ld{telephone|shipping_mobile|mobile}","timestamp":"%date{Y-m-d H:i:s}.%ses{customer_id}", "logger":"%logger", "pid":"%pid", "level":"%level","client":"%s{REMOTE_ADDR}:%s{REMOTE_PORT} %.220s{HTTP_USER_AGENT}", "server":"%s{HTTP_HOST}(%s{LOCAL_ADDR}%s{SERVER_ADDR}):%s{SERVER_PORT}", "user_id":"%ses{user_id}", "method":"%def{DIR_DIR}%req{route}::%C.%M:%L", "message":"%.4000message"}'
				)
		),
		'admin_ref' => array (
				'class' => 'LoggerAppenderFile',
				'params' => array (
						'file' => DIR_LOGS . 'log_admin.log',
						'pattern'=>'{"_id":"%date{YmdHis}.%r{_t}.%def{DIR_DIR}%req{route}.%C.%M:%L.%ld{order_id}.%ld{telephone|shipping_mobile|mobile}","timestamp":"%date{Y-m-d H:i:s}.%ses{customer_id}","times":"%r", "logger":"%logger", "pid":"%pid", "level":"%level","client":"%s{REMOTE_ADDR}:%s{REMOTE_PORT} %.220s{HTTP_USER_AGENT}", "server":"%s{HTTP_HOST}(%s{LOCAL_ADDR}%s{SERVER_ADDR}):%s{SERVER_PORT}", "user_id":"%ses{user_id}", "method":"%def{DIR_DIR}%req{route}::%C.%M:%L", "message":"%.4000message"}'
				)
		),
		'sql_ref' => array (
				'class' => 'LoggerAppenderFile',
				'params' => array (
						'file' => DIR_LOGS . 'log_sql.log',
						'pattern'=>'{"_id":"%date{YmdHis}.%r{_t}.%def{DIR_DIR}%req{route}.%C.%M:%L.%ld{order_id}.%ld{telephone|shipping_mobile|mobile}","timestamp":"%date{Y-m-d H:i:s}.%ses{customer_id}","times":"%r", "logger":"%logger", "pid":"%pid", "level":"%level","client":"%s{REMOTE_ADDR}:%s{REMOTE_PORT} %.220s{HTTP_USER_AGENT}", "server":"%s{HTTP_HOST}(%s{LOCAL_ADDR}%s{SERVER_ADDR}):%s{SERVER_PORT}", "user_id":"%ses{user_id}", "method":"%def{DIR_DIR}%req{route}::%C.%M:%L", "message":"%.4000message"}'
				)
		)
		
		),
		'rootLogger' => array (
				'closed'=>false,
				'level'=>'info',
				'appenders' => array ('default')
		),
		'loggers'=>array (
		        'order' => array (
		        		'additivity'=>false,
		        		'level'=>'info',
				        'appenders' => array ('order_ref')
		        ),
		        'payment' => array (
		        		'level'=>'info',
		        		'additivity'=>false,
				        'appenders' => array ('payment_ref')
		        ),
		        'admin' => array (
		        		'level'=>'info',
		        		'additivity'=>false,
				        'appenders' => array ('admin_ref')
		        ),
		        'database' => array (
		        		'level'=>'debug',
		        		'additivity'=>false,
				        'appenders' => array ('sql_ref')
		        )
		) 
);