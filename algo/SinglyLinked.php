<?php
/**
 * 单项列表
 */


/**
 * 节点
 * Class Node
 */
class Node {

    public $data;
    public $next;

    public function __construct( $data = null ) {
        $this->data = $data;
        $this->next = null;
    }

}

class SinglyLinked {

    public $head;
    private $tail;
    private $length;
    private $current;

    /**
     * 初始化
     * SinglyLinked constructor.
     *
     * @param Node $node
     */
    public function __construct( Node $node ) {

        $this->head    = $node;
        $this->tail    = $node;
        $this->current = $node;
        $this->length  = 0;
    }

    public function append( $data ) {
        $node             = new Node($data);
        $this->tail->next = $node;
        $this->tail       = $node;
        $this->current    = $node;
        $this->length++;
    }

    public function insert( Node $node,$data ) {

        $newNode       = new Node($data);
        $newNode->next = $node->next;
        $node->next    = $newNode;

        $this->current = $newNode;

        if ( $this->tail == $node ){
            $this->tail = $newNode;
        }

        $this->length++;
    }

    public function prev() {
        $p = $this->head->next;

        while ( $p && $p->next != $this->current ) {
            $p = $p->next;
        }
        return $this->current = $p;
    }

    public function next() {
        return $this->current = $this->current->next;
    }
}

$linked = new SinglyLinked(new Node('a'));

$linked->insert($linked->head,'b');
$linked->append('b');
$linked->append('c');
$linked->append('d');
$linked->prev();
$linked->prev();

print_r($linked);
print_r($linked->next());