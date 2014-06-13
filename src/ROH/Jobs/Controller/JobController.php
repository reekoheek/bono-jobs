<?php

namespace ROH\Jobs\Controller;

use \ROH\Jobs\Jobs;
use \Bono\Controller\RestController;

use \Norm\Schema\String;
use \Norm\Schema\DateTime;

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
                Jobs::create($this->request->post());
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
                // Jobs::update($this->request->post());
                h('notification.error', 'Job updated (not implemented yet).');
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
        return array(
            'expression' => String::create('expression'),
            'command' => String::create('command'),
            'next_run' => DateTime::create('next_run')->set('hidden', true),
        );
    }
}
