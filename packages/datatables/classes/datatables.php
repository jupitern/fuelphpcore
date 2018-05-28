<?php

namespace Datatables;

class Datatables
{
	
    public $instanceName;
	public $columns;
    public $actions;
	public $headerActions;
    public $dataSource;
	public $dataSourceUrl;
	public $debug;
    
	private $idLanguage;
    
    function __construct($instanceName)
	{
		$this->instanceName = $instanceName;
		$this->debug		= true;
		$this->columns		= array();
		$this->actions		= array();
		$this->idLanguage	= \Fuel\Core\Session::get('idLanguage', 0);
    }
	
	
	public static function forge($instanceName)
	{
		$response = new static($instanceName);
		return $response;
	}
	
	
	public function addColumn( $name, $header, $value = null, $filter = null, $htmlOptions = null )
	{
		$column = array(
			'name'	 => $name,
			'header' => $header
		);
		if( $value !== null ) $column['value'] = $value;
		if( $filter !== null ) $column['filter'] = $filter;
		if( is_array($htmlOptions) ) $column['htmlOptions'] = $htmlOptions;
		$this->columns[] = $column;
		return $this;
	}
	
	public function addAction( $url, $title, $htmlOptions = array(), $visible = null )
	{
		$this->actions[] = array(
			'url'	=> $url,
			'title' => $title,
			'htmlOptions' => $htmlOptions,
			'visible' => $visible
		);
		return $this;
	}
	
	public function setDataSource( $dataSource ){
		$this->dataSource = $dataSource;
		return $this;
	}
	
	public function setDataSourceUrl( $dataSourceUrl ){
		$this->dataSourceUrl = $dataSourceUrl;
		return $this;
	}
    
    public function getData( $params )
	{
        $columns	= $this->columns;
        $dataSource = $this->dataSource;
		$iTotalRecords = $dataSource->count();
		
        // search fields
        for( $i=0; $i < intval($params['iColumns']); ++$i ){
			if( is_numeric($params['sSearch_'.$i]) ){
				$dataSource->where($columns[$i]['name'], $params['sSearch_'.$i]);
			}
			elseif( trim($params['sSearch_'.$i]) != "" ){
				$dataSource->where($columns[$i]['name'], 'LIKE', '%'.$params['sSearch_'.$i].'%');
			}
        }
		$iTotalDisplayRecords = $dataSource->count();
		
        $dataSource->order_by( $columns[intval($params['iSortCol_0'])]['name'], $params['sSortDir_0'] );
        $dataSource->limit( intval($params['iDisplayLength']) );
        $dataSource->offset( intval($params['iDisplayStart']) );
		
		$q = $dataSource->get_query();
		
        $data_rows = array();
        foreach ($dataSource->get() as $row) {
            $data_row = array();
            foreach( $this->columns as $column ){
				
				$htmlOptions = "";
				if( isset($column['htmlOptions']) ){
                    foreach( $column['htmlOptions'] as $option => $value ) $htmlOptions .= $option.'="'.$value.'" ';
                }
				
				// eval code?
				if( isset($column['value']) ){
					
					if(strpos($column['value'], '$') !== false || strpos($column['value'], '\'') !== false || strpos($column['value'], ':') !== false){
						$data_row[] = "<span $htmlOptions>".$this->evaluateExpression($column['value'], array('row'=>$row))."</span>";
					}
					else{
						$parts = explode('.', $column['value']);
						$val_field = array_pop($parts);
						$relation  = '$row->'.implode('->', $parts);
						$data_row[] = "<span $htmlOptions>".$this->evaluateExpression($relation.' ? '.$relation.'->value(\''.$val_field.'\') : \'\'', array('row'=>$row))."</span>";
					}
				}
				else{
					$data_row[] = "<span $htmlOptions>". $row->value($column['name']) ."</span>";
				}
            }
            $data_row[]  = $this->renderActions($this->actions, $row);
            $data_rows[] = $data_row;
        }
        
        $data = array
        (
            'sEcho'                 => intval($params['sEcho']),
            'iTotalRecords'         => $iTotalRecords,
            'iTotalDisplayRecords'  => $iTotalDisplayRecords,
            'aaData'                => $data_rows,
			'debug'					=> $this->debug ? $q : "",
        );
        return json_encode($data);
    }

    
    public function render()
	{
        $view = \View::forge('datatables/index');
        $view->set_safe('instanceName',        $this->instanceName);
        $view->set_safe('dataSourceUrl',       $this->dataSourceUrl);
        $view->set_safe('columns',             $this->columns);
        $view->set_safe('hasActions',          (count($this->actions) ? true : false));
        return $view->render();
    }


    private function renderActions( $actions, $row )
	{
        $actions_html = '<span style="white-space:nowrap">';
        foreach( $this->actions as $action ){
            if (!isset($action['visible']) || $action['visible'] === true || $this->evaluateExpression($action['visible'],array('row'=>$row)) ){
                
                $url = isset($action['url']) ? $this->evaluateExpression($action['url'],array('row'=>$row)) : '#';
                $actions_html .= \Html::anchor( $url, $action['title'], $action['htmlOptions'] );
            }
        }
        return $actions_html.'</span>';
    }
    
    
    /**
     * Evaluates a PHP expression or callback under the context of this component.
     *
     * Valid PHP callback can be class method name in the fmodel of
     * array(ClassName/Object, MethodName), or anonymous function (only available in PHP 5.3.0 or above).
     *
     * If a PHP callback is used, the corresponding function/method signature should be
     * <pre>
     * function foo($param1, $param2, ..., $component) { ... }
     * </pre>
     * where the array elements in the second parameter to this method will be passed
     * to the callback as $param1, $param2, ...; and the last parameter will be the component itself.
     *
     * If a PHP expression is used, the second parameter will be "extracted" into PHP variables
     * that can be directly accessed in the expression. See {@link http://us.php.net/manual/en/function.extract.php PHP extract}
     * for more details. In the expression, the component object can be accessed using $this.
     *
     * @param mixed $_expression_ a PHP expression or PHP callback to be evaluated.
     * @param array $_data_ additional parameters to be passed to the above expression/callback.
     * @return mixed the expression result
     * @since 1.1.0
     */
    private function evaluateExpression($_expression_,$_data_=array())
    {
        if(is_string($_expression_))
        {
            extract($_data_);
            return eval('return '.$_expression_.';');
        }
        else
        {
            $_data_[]=$this;
            return call_user_func_array($_expression_, $_data_);
        }
    }
    
}