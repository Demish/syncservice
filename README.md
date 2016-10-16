# SyncService

Servce for sync anythong (not permanently)

## Install

Copy all files to any directory at your hosting. Databases aren't required, only Flinstone is used (you can get ./vendor dir or install it via composer).

## Problems

Current version doesn't suppory any auth features. Thus it can be used only for closed networks (where you have to do sync operations from time to time)

## Examples

Url - it is url of SyncService deployed. All examples should work for http://url/-example is here-
Project - project (unique string), for example "users"
Regname - the part of data you need to be synchronized

Show all keys: 
/syncserv/index.php?mode=getkeys&project=users

Setup metadata: 
/syncserv/index.php?mode=setmeta&project=users&regname=товарыпоскладам&metakeys[keys][0]=номенклатура&metakeys[keys][1]=склад&metakeys[values]=1

Get metadata: 
/syncserv/index.php?mode=getmeta&project=users&regname=товарыпоскладам

Set value for key: 
/syncserv/index.php?mode=set&project=users&regname=товарыпоскладам&vals[keys][0]=товар1&vals[keys][1]=склад1&vals[values][0]=1000
/syncserv/index.php?mode=set&project=users&regname=товарыпоскладам&vals[keys][0]=товар2&vals[keys][1]=склад1&vals[values][0]=10

Get values by keys:
/syncserv/index.php?mode=get&project=users&regname=товарыпоскладам&keys[0]=товар1&keys[1]=склад1

Get all data by Regname:

/syncserv/index.php?mode=getbyregname&project=users&regname=товарыпоскладам
/syncserv/index.php?mode=getbyregname&project=utkrasnodar&regname=deleting

Drop the key:

/syncserv/index.php?mode=dropkey&project=users&key=data

