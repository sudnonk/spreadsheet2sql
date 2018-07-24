# spreadsheet2sql
スプレッドシートのデータをSQLにします

## 例
↓みたいなスプレッドシート(hoge.xlsx)に対して、

|ID|NAME|
----|----
|1|hoge|
|2|foo|
|3|bar|

`php main.php -e xlsx -t TABLE_NAME -i hoge.xlsx -o hoge.sql`
と打つと、

hoge.sqlは↓みたいになります
```
INSERT INTO TABLE_NAME(`ID`,`NAME`) values(`1`,`hoge`),(`2`,`foo`),(`3`,`bar`);
```

## 引数
|引数|意味|
----|----
|e|拡張子。xlsxとかcsvとか|
|t|テーブル名|
|i|入力ファイル名|
|o|出力ファイル名|