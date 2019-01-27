<?php
/**
 * Created by 小志.
 * User: 小志
 * Date: 2018/12/15
 * Time: 20:24
 * action: 一致性哈希的PHP实现
 */

//需要一个把字符串转成整数的功能
interface Hash{
    public function _hash($str);
}

//给一个key放到某个虚拟节点上 分布式
interface Distribution{
    public function lookUp($key); //找节点
}

class ConsistentHash implements Hash, Distribution {
    protected $_nodes = array();
    protected $_position = array();
    protected $_mul = 64; //每个真实节点虚拟出来的虚拟节点个数

    public function _hash($str)
    {
        // TODO: Implement _hash() method.
        return sprintf('%u', crc32($str));
    }


    public function lookUp($key)
    {
        // TODO: Implement lookup() method.
        $point = $this->_hash($key);
        $node = current($this->_position); //先取圆环上的最小的那一个节点，当成结果

        foreach ($this->_position as $k => $v){
            if($point <= $k){
                $node = $v;
                break;
            }
        }
        return $node;
    }


    public function addNode($node)
    {
        //每添加一个真实节点虚拟出来 $_mul 个虚拟节点
        for($i = 0; $i < $this->_mul; $i++){
            $virtual_node = $node.'-'.$i; //虚拟节点
            $this->_position[$this->_hash($virtual_node)] = $node;
            $this->_nodes[$node][] = $this->_hash($virtual_node);
        }
        //$this->_nodes[$this->_hash($node)] = $node; //如array('13亿' => true)
        ksort($this->_position, SORT_REGULAR); //将节点hash后按键的大小排序
    }

    //循环所有虚拟节点，谁的值等于真实节点就把他删掉
    public function delNode($node){
        foreach ($this->_nodes[$node] as $k){ //$this->_nodes数组的第二维是节点hash后的值
            unset($this->_position[$k]);
        }
    }

    /**
     * @return array
     */
    public function getPosition()
    {
        print_r($this->_position);
        return $this->_position;
    }

    /**
     * @return array
     * @action 调试用
     */
    /*public function getNodes()
    {
        print_r($this->_nodes);
        return $this->_nodes;
    }*/


}

$con = new ConsistentHash();
$con->addNode('a');
$con->addNode('b');
$con->addNode('c');
$con->getPosition();
//echo $con->_hash('a').'<br>';
//echo $con->_hash('b');
echo $con->_hash('name');
echo $con->lookUp('title');