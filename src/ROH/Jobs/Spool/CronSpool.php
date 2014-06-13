<?php

namespace ROH\Jobs\Spool;

use ROH\Jobs\Spool;

use Cron\CronExpression;

class CronSpool extends Spool
{
    protected $entries = null;

    public function __construct($options)
    {
        parent::__construct($options);

    }

    public function find()
    {
        if (is_null($this->entries)) {

            $entries = array();

            exec('crontab -l', $result, $code);

            foreach ($result as $line) {
                // # m h  dom mon dow   command
                $line = trim($line);
                if (!$line || $line[0] == '#') {
                    continue;
                }

                $lineSplitted = preg_split('/\s/', $line);
                $lineSplitted = array_slice($lineSplitted, 0, 5);

                $cron = CronExpression::factory(implode(' ', $lineSplitted));

                $expression = $cron->__toString();
                $lineSplitted = explode($expression, $line);

                $entries[] = array(
                    '$id' => base64_encode($line),
                    'line' => $line,
                    'expression' => $expression,
                    'command' => trim($lineSplitted[1]),
                    'next_run' => $cron->getNextRunDate(),
                    // 'last_run' => $cron->getPreviousRunDate(),
                );
            }

            $this->entries = $entries;
        }

        return $this->entries;
    }

    public function findOne($id)
    {
        $id = str_replace(' ', '+', $id);

        foreach ($this->find() as $key => $value) {
            if ($value['$id'] === $id) {
                return $value;
            }
        }
    }

    public function getContent()
    {
        $this->find();

        $content = array('# m h  dom mon dow   command');
        foreach ($this->entries as $entry) {
            $content[] = $entry['expression'].' '.$entry['command'];
        }

        return implode("\n", $content)."\n";

    }

    public function persist()
    {
        // var_dump($this->getContent());

        $fn = tempnam('/tmp', 'cron-spool-');
        if ($fn) {
            $f = fopen($fn, 'w+');
            if ($f) {
                fwrite($f, $this->getContent());
                exec('crontab "'.$fn.'"', $result, $code);
            }
            @unlink($fn);
        }
    }

    public function create($entry)
    {
        $cron = CronExpression::factory($entry['expression']);
        $entry['expression'] = $cron->__toString();
        $entry['line'] = $entry['expression'].' '.$entry['command'];
        $entry['$id'] = base64_encode($entry['line']);

        $this->find();

        $this->entries[] = $entry;

        $this->persist();
    }

    public function remove($id)
    {
        $id = str_replace(' ', '+', $id);

        $this->find();

        $entries = array();

        foreach ($this->entries as $key => $value) {
            if ($value['$id'] !== $id) {
                $entries[] = $value;
            }
        }

        $this->entries = $entries;

        $this->persist();
    }
}
