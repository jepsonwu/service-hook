<?php
namespace Jepsonwu\hook;
/**
 * 通用场景是：函数返回值作为参数传入钩子调度器，如果为false则不执行钩子，亦可以不传参数
 *
 * Schedule::run($name,$result)
 *
 * 支持同一方法多个钩子连续执行，按ASCII码排序，你需要实现Collection接口getHooks方法
 * Schedule::exec($result)
 *
 * Created by PhpStorm.
 * User: jepsonwu
 * Date: 2017/2/14
 * Time: 10:03
 */
class Scheduler
{
    private static $instance;

    private $group;
    private $hooks = [];

    public static function instance($group = "")
    {
        is_null(self::$instance[$group]) &&
        self::$instance[$group] = new self($group);

        return self::$instance[$group];
    }

    private function __construct($group)
    {
        $this->group = $group;
        $this->hooks[$group] = [];
    }

    public function register($name, callable $function)
    {
        $this->hooks[$this->group][$name] = $function;
    }

    public function get($name)
    {
        $hooks = $this->hooks[$this->group];
        return isset($hooks[$name]) && is_callable($hooks[$name]) ? $hooks[$name] : false;
    }

    public function run($name, $result = null)
    {
        if ($result !== false) {
            $callback = $this->get($name);
            $callback && $result = call_user_func_array($callback, is_null($result) ? [] : [$result]);
        }

        return $result;
    }

    private function getHooks()
    {
        asort($this->hooks[$this->group]);
        return array_keys($this->hooks[$this->group]);
    }

    public function exec($result = null)
    {
        if ($result !== false) {
            foreach ($this->getHooks() as $name) {
                $result = $this->run($name, $result);
            }
        }
        return $result;
    }

}