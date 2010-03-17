<?php
return new ExampleAsyncProcess;
class ExampleAsyncProcess extends AppInstance
{
 public function beginRequest($req,$upstream) {return new ExampleAsyncProcessRequest($this,$upstream,$req);}
}
class ExampleAsyncProcessRequest extends Request
{
 public $proc;
 public function init()
 {
  $this->header('Content-Type: text/plain');
  $this->proc = new AsyncProcess;
  $this->proc->onReadData(function($stream,$data)
   {
    $stream->request->out($data);
   });
  $this->proc->onEOF(function($stream)
  {
   $stream->request->wakeup();
  });
  $this->proc->setRequest($this);
  $this->proc->nice(256);
  //$this->proc->execute('cat /dev/urandom');
  $this->proc->execute('ls -lia');
  $this->proc->closeWrite();
 }
 public function onAbort()
 {
  if ($this->proc) {$this->proc->close();}
 }
 public function run()
 {
  if (!$this->proc->eof()) {$this->sleep(1);}
  return 1;
 }
}
