<?php
/**
 * Created by PhpStorm.
 * User: cronfy
 * Date: 18.09.17
 * Time: 12:33
 */

namespace cronfy\import\readers;

use Yii;

class Csv extends Generic
{
    public $source;
    public $encoding = 'utf-8';
    public $separator = ',';

    public $columns;
    public $startLine = 1;
    public $headersInFirstLine = true;

    public $skipper;

    public function iterateItems()
    {
        $data = $this->iterateCsv();

        foreach ($data as $lineNo => $raw_line) {
            if ($this->skipper) {
                if ($this->skipper->skip()) {
                    continue;
                }

                if (!$this->skipper->go()) {
                    return;
                }
            }

            $current = array_combine($this->columns, $raw_line);

            if (false === $current) {
                throw new \Exception(
                    "Both parameters should have an equal number of elements "
                    . print_r($this->columns, 1)
                    . print_r($raw_line, 1)
                );
            }

            yield $lineNo => $current;
        }
    }

    protected function iterateCsv()
    {
        $file = Yii::getAlias($this->source);
        $separator = $this->separator;

        if ($this->encoding !== 'utf-8') {
            $handle = fopen('php://memory', 'w+');
            fwrite($handle, iconv($this->encoding, 'utf-8', file_get_contents($file)));
            rewind($handle);
        } else {
            if (($handle = fopen($file, "r")) === false) {
                throw new \Exception('Failed to read csv file');
            }
        }

        $line = 0;
        while (($data = fgetcsv($handle, 0, $separator)) !== false) {
            $line++;

            if ($this->startLine && $line < $this->startLine) {
                continue;
            }

            if ($this->headersInFirstLine && $line == $this->startLine) {
                if (!$this->columns) {
                    $this->initColumns($data);
                    continue;
                }
            }

            yield $line => $data;
        }

        fclose($handle);
    }

    public function initColumns($lineData)
    {
        $columns = array_map(function ($item) {
            static $counter = 0;
            $counter++;
            return $item ?: 'noname_column_' . chr($counter + 64);
        }, $lineData);

        $this->columns = $columns;
    }
}
