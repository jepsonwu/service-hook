<?php
namespace Jepsonwu\hook;

use PHPUnit\Framework\TestCase;

/**
 * Created by PhpStorm.
 * User: jepsonwu
 * Date: 2017/2/14
 * Time: 11:04
 */
class HookTest extends TestCase
{
    const SEX_HOOK = 1;
    const INFO_HOOK = 2;

    /**
     * @return Scheduler
     */
    public function testScheduler()
    {
        return Scheduler::instance("person");
    }

    /**
     * @depends testScheduler
     * @param Scheduler $scheduler
     */
    public function testRun(Scheduler $scheduler)
    {
        $this->registerPersonInfoHook($scheduler);

        $result = "man";
        $result = $scheduler->run(self::INFO_HOOK, $result);
        $this->assertTrue($result == "this is person infomation:name:jepson,age:26,sex:man", "hook run failed,result:man!");

        $result = false;
        $result = $scheduler->run(self::INFO_HOOK, $result);
        $this->assertTrue($result === false, "hook run failed,result:false");

        $result = null;
        $result = $scheduler->run(self::INFO_HOOK, $result);
        $this->assertTrue($result == "this is person infomation:name:jepson,age:26", "hook run failed,result:null");
    }

    /**
     * @depends testScheduler
     * @param Scheduler $scheduler
     */
    public function testExec(Scheduler $scheduler)
    {
        $this->registerPersonSexHook($scheduler);
        $this->registerPersonInfoHook($scheduler);

        $result = "man";
        $result = $scheduler->exec($result);
        $this->assertTrue($result == "this is person infomation:name:jepson,age:26,sex:1", "hook exec failed,result:man");
    }

    public function registerPersonSexHook(Scheduler $scheduler)
    {
        $hook = new Hook(function ($sex) {
            return $sex == "man" ? 1 : 0;
        });
        $scheduler->register(self::SEX_HOOK, $hook);
    }

    public function registerPersonInfoHook(Scheduler $scheduler)
    {
        $hook = new Hook(function ($name, $age, $sex = null) {
            return "this is person infomation:name:{$name},age:{$age}" . (is_null($sex) ? "" : ",sex:{$sex}");
        });
        $hook['name'] = "jepson";
        $hook["age"] = 26;

        $scheduler->register(self::INFO_HOOK, $hook);
    }
}