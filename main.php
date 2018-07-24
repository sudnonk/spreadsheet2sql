<?php

    require_once "vendor/autoload.php";

    /* 入力受付 */
    //引数
    $options = getopt("e:p:t:i::o:");
    foreach (["e", "p", "t", "o"] as $option) {
        if (!isset($options[$option])) {
            echo_help("param $option does not set.");
            exit();
        }
    }
    if(!isset($options["i"])){
        $option["i"] = 0;
    }

    //入力ファイルの拡張子。extensionのe。xlsxとかcsvとか
    $input_type = $options["e"];
    //入力ファイルのパス。pathのp。
    $input_path = $options["p"];
    //insert先のテーブル名。tableのt
    $table_name = $options["t"];
    //sheetのindex。indexのi
    $sheet_index = $options["i"];
    //SQLの書き出し先。outputのo
    $output = $options["o"];

    /* バリデーション */
    if (!file_exists($input_path)) {
        echo_help("file not exists.");
        exit();
    }

    try {
        /* ファイル読み込み */
        switch ($input_type) {
            case "csv":
                $reader = new PhpOffice\PhpSpreadsheet\Reader\Csv();
                break;
            case "xlsx":
                $reader = new PhpOffice\PhpSpreadsheet\Reader\Xlsx();
                break;
            case "xls":
                $reader = new PhpOffice\PhpSpreadsheet\Reader\Xls();
                break;
            default:
                echo_help("Extension must be csv, xlsx, xls.");
                exit();
        }

        $spreadsheet = $reader->load($input_path);

        $sheet = $spreadsheet->getSheet($sheet_index);

        $keys = [];
        $data = [];
        foreach ($sheet->getRowIterator() as $row) {
            foreach ($row->getCellIterator() as $cell) {
                $rowIndex = $cell->getRow();
                if ($rowIndex === 1) {
                    $keys[] = $cell->getValue();
                } else {
                    $data[$rowIndex][] = $cell->getValue();
                }
            }
        }


        $table_columns = concat($keys);
        $values = [];
        foreach ($data as $datum) {
            $values[] = concat($datum);
        }
        $value = concat($values, "),\n(", "(", ");");

        $sql = "insert into `$table_name`($table_columns) values $value";
        if (file_put_contents($output, $sql) === false) {
            echo_help("failed to output.");
            exit();
        }
    } catch (Exception $e) {

    }


    function echo_help(string $err) {
        echo <<<HELP

{$err}
usage:
required
  e - Extension
  p - input file path
  t - table name to insert
  o - output file path

not required
  i - sheet index. default 0
HELP;

    }

    function concat(array $arr, string $glue = "`,`", string $head = "`", string $tail = "`") {
        $str = $head;

        $arr = array_values($arr);
        $num = count($arr);
        foreach ($arr as $i => $value) {
            if (!is_string($value)) {
                $value = "";
            }
            if ($i === $num) {
                $str .= $value;
            } else {
                $str .= $value . $glue;
            }
        }

        return $str . $tail;
    }

