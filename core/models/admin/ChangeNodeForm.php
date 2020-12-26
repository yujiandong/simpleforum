<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

namespace app\models\admin;

use Yii;
use yii\base\Model;
use app\models\Node;

class ChangeNodeForm extends Model
{
    public $id;
    public $name;
    public $ename;
    public $type;
	private $_node;

    public function attributeLabels()
    {
        return [
            'name' => Yii::t('app', 'Node name'),
        ];
    }

    public function find($name)
	{
			$this->_node = Node::findByName($name);
			if($this->_node !== null) {
				$this->attributes = $this->_node->attributes;
			}
			return ($this->_node !== null);
	}

    public function change()
    {
        	$node = $this->_node;
            $node->node_id = $this->node_id;
            return $node->save(false);
    }
}
