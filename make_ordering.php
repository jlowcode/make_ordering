<?php

// No direct access
defined('_JEXEC') or die('Restricted access');

// Require the abstract plugin class
require_once COM_FABRIK_FRONTEND . '/models/plugin-form.php';


class PlgFabrik_FormMake_Ordering extends PlgFabrik_Form
{
    /**
     * Run right at the end of the form processing
     * form needs to be set to record in database for this to hook to be called
     *
     * @return	bool
     */
    public function onAfterProcess()
    {
        return $this->_process();
    }

    public function _process() {
        $formModel   = $this->getModel();
        $params      = $this->getParams();
        $plugin      = FabrikWorker::getPluginManager();
        $mapElements = json_decode($params->get('map_elements'));
        $table       = $formModel->getListModel()->getTable()->db_table_name;
        $tablesJoin  = array();
        $keyElements = false;
        
        $elementOder = $params->get('list_order_elements');
        $elementOder = $plugin->getElementPlugin($elementOder)->element->name;
        $elementPath = $params->get('list_path_elements');
        
        for ($i=0; $i < count($mapElements->elemento_destino); $i++) { 
            if ($mapElements->elemento_destino[$i] == $elementPath) {
                $keyElements = $i;
                break;
            }
        }

        if ($keyElements === false) return false;
        
        $tablesJoin[] = $this->selectJoinRelationshipTable($mapElements->elemento_destino[$keyElements]);
        $tablesJoin[] = $this->selectJoinRelationshipTable($mapElements->elemento_origem[$keyElements]);

        $columns = array("a.`id` AS 'article'", "GROUP_CONCAT(s.`title` SEPARATOR '//..*..//') AS 'path'");
        $data    = $this->getData($columns, $table, $tablesJoin);

        $order = $this->makeOrdering($data);
        
        for ($i=0; $i < count($order); $i++) {
            $this->updateElement($order[$i], $i, $elementOder, $table);
        }
    }

    public function makeOrdering($data) {
        $newData = array();

        foreach ($data as $row) {
            $newData[$row->article] = $row->path;
        }
        
        // Inverts the order of the sections that are originally ordered from the lowest 
        // level to the highest level, so that they can be ordered alphabetically.
        foreach ($newData as $key => $value) :
            $section = explode('//..*..//', $value);
            $section = array_reverse($section);
            $section = implode('//..*..//', $section);
            $newData[$key] = $section;
        endforeach;

        asort($newData);

        return array_keys($newData);
    }

    private function selectJoinRelationshipTable($elementId)
	{
        $db     = JFactory::getDbo();
        $query  = $db->getQuery(true);
        
        $query
			->select($db->quoteName('rjoins.table_join'))
			->from($db->quoteName('#__fabrik_joins', 'rjoins'))
            ->where($db->quoteName('rjoins.element_id') . ' = ' . $elementId);

        $db->setQuery($query);
    
        return $db->loadResult();
    }

    public function getData($columns, $table, $tablesJoin) {
        $db = JFactory::getDbo();

        $query = $db->getQuery(true);
        $query
            ->select($columns)
            ->from($db->quoteName($table, 'a'))
            ->join('INNER', $db->quoteName($tablesJoin[0], 'p') . ' ON ' . $db->quoteName('a.id') . ' = ' . $db->quoteName('p.parent_id'))
            ->join('INNER', $db->quoteName($tablesJoin[1], 's') . ' ON ' . $db->quoteName('p.path') . ' = ' . $db->quoteName('s.id'))
            ->group($db->quoteName('a.id'))
            ->order($db->quoteName('path') . ', ' . $db->quoteName('a.title') . ', ' . $db->quoteName('a.introduction') . ', ' . $db->quoteName('a.id'));

        $db->setQuery($query);

        return $db->loadObjectList();
    }

    public function updateElement($rowId, $data, $field, $table) {
        if (!$field) {
            return false;
        }

        $row = new stdClass();
        $row->id = $rowId;
        $row->$field = $data;

        JFactory::getDbo()->updateObject($table, $row, 'id');
    }
}
