<?php

namespace ROH\Jobs\Controller;

use \ROH\Jobs\Jobs;
use \Bono\Controller\RestController;

use \Norm\Schema\String;
use \Norm\Schema\DateTime;
use \Norm\Filter\Filter;

class JobController extends RestController
{
    public function search()
    {
        $this->data['entries'] = Jobs::find();
    }

    public function create()
    {
        if ($this->request->isPost()) {
            try {
                $post = $this->request->post();
                $post = Filter::fromSchema($this->schema())->run($post);

                Jobs::create($post);

                h('notification.info', 'Job created.');
            } catch (\Exception $e) {
                h('notification.error', $e);
            }
        }
    }

    public function read($id)
    {
        $this->data['entry'] = Jobs::findOne($id);
    }

    public function update($id)
    {
        $this->data['entry'] = Jobs::findOne($id);
        if ($this->request->isPost() || $this->request->isPut()) {

            try {
                $post = $this->request->post();
                $post = Filter::fromSchema($this->schema())->run($post);

                Jobs::update($id, $post);

                h('notification.info', 'Job updated.');
            } catch (\Exception $e) {
                h('notification.error', $e);
            }
        }
    }

    public function delete($id)
    {
        $this->data['entry'] = Jobs::findOne($id);
        if ($this->request->isPost() || $this->request->isDelete()) {
            try {
                Jobs::remove($id);
                h('notification.info', 'Job deleted.');
            } catch (\Exception $e) {
                h('notification.error', $e);
            }
        }
    }

    public function schema()
    {
        Filter::register('can_access', function ($value, $entry, $args) {

            if (empty($value)) {
                return $value;
            }

            if ($args[0] === 'dir') {
                if (!is_dir($value) && ! @mkdir($value, 0755, true)) {
                    throw new \Exception('Directory is not exists');
                }

                if (!is_readable($value)) {
                    throw new \Exception('Directory is not readable');
                }

                if (!is_writable($value)) {
                    throw new \Exception('Directory is not writable');
                }
            } else {
                @mkdir(dirname($value), 0755, true);
                if (!@touch($value)) {
                    throw new \Exception('File is not exists');
                }

                if (!is_readable($value)) {
                    throw new \Exception('File is not readable');
                }

                if (!is_writable($value)) {
                    throw new \Exception('File is not writable');
                }
            }

            return $value;
        });

        return array(
            'name' => String::create('name')->filter('required'),
            'expression' => String::create('expression')->filter('required'),
            'command' => String::create('command')->filter('required'),
            'working_dir' => String::create('working_dir')->filter('can_access:dir'),
            'stdout' => String::create('stdout')->filter('can_access:file'),
            'stderr' => String::create('stderr')->filter('can_access:file'),
            'next_run' => DateTime::create('next_run'),
        );
    }
}
