USE mysql;

INSERT INTO user (Host,User,Password,Select_priv,Insert_priv,Update_priv,Create_priv, Alter_priv, Delete_priv)
VALUES('localhost','NEWUSER_MYSQL',PASSWORD('NEWPWD_MYSQL'),'Y','Y','Y','Y','Y');

FLUSH privileges;