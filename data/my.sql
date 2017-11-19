DROP TABLE  `sys_user`;
CREATE TABLE `sys_user` (
  `uid` INT NOT NULL UNSIGNED AUTO_INCREMENT COMMENT '用户ID',
  `user_name` VARCHAR(45) NOT NULL DEFAULT '' COMMENT '用户名',
  `user_pass` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '密码',
  `user_mobile` VARCHAR(16) NOT NULL DEFAULT '' COMMENT '用户手机号码',
  `mobile_bind` TINYINT(2) NOT NULL DEFAULT 0 COMMENT '手机是否绑定 0 未绑定 1 绑定',
  `user_email` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '用户邮箱',
  `email_bind` TINYINT(2) NOT NULL DEFAULT 0 COMMENT '邮箱是否绑定 0 未绑定 1 绑定',
  `nickname` VARCHAR(64) NOT NULL DEFAULT 'HAI用户' COMMENT '用户昵称',
  `avatar` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '用户头像',
  `sex` TINYINT(2) UNSIGNED NOT NULL DEFAULT '0' COMMENT '用户性别 0：未知 1：男 ：女。',
  `current_login_type` TINYINT NOT NULL DEFAULT 0 COMMENT '当前登陆终端类型 0 未知 1 PC 2手机 3微信 ...',
  `last_login_type` TINYINT NOT NULL DEFAULT 0 COMMENT '最后一次登陆终端类型',
  `current_login_ip` VARCHAR(20) NOT NULL DEFAULT '' COMMENT  '当前登陆IP',
  `last_login_ip` VARCHAR(20) NOT NULL DEFAULT '' COMMENT  '最后一次登陆IP',
  `create_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`uid`));
}
ENGINE INNODB
AUTO_INCREMENT=0
DEFAULT CHARSET = UTF8
COMMENT='系统用户数据表';