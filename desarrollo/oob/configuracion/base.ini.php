[main]
title = "OOB-n1"
main_lang = "es-ar"
accepted_lang = "es-ar"
name = "OOB-n1"

allow-cache = "true"
debug = "false"
homeelement ="/seguridad/login"
urlhandler =""
defaultlogin = "/about"
email = "info@nutus.com.ar"
delivery = "correo.nutus.com.ar"
smtpuser = "info@nutus.com.ar"
smtppass = ""

[database]
uri = "mysqli://talkmee:talkmee@localhost/talkmee"


[location]
filesdir = "C:/data/nutus/desarrollo/clientes/talkmee"
webaddress = "http://talkmee.nutus.info"
adminaddress = "http://admin.talkmee.nutus.info"
cachedir = "C:/data/nutus/desarrollo/clientes/talkmee/archivos/cache"

[metadata]
description = ""
keywords = ""
author = "Nutus"
expires = "3600"

[user]
validation-method = "db"
new_validation = "yes"
block-method = "time"
block-time = "3600"
imap-server ="localhost:143"
can-self-register = "true"
new-group = "2"