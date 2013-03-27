<%
/**
@Prefix('admin/<?php echo $bundle['model']['meta']['plural'] ?>')
*/
class <?php echo ucfirst($bundle['model']['meta']['name']) ?>AdminController extends \Coxis\Admin\Libs\Controller\ModelAdminController {
	static $_model = '<?php echo $bundle['model']['meta']['name'] ?>';
	static $_models = '<?php echo $bundle['model']['meta']['plural'] ?>';

	function __construct() {
		$this->_messages = array(
		<?php foreach($bundle['coxis_admin']['messages'] as $k=>$v): ?>
			'<?php echo $k ?>'			=>	__('<?php echo $v ?>'),
		<?php endforeach ?>
		);
		parent::__construct();
	}
	
	public function formConfigure($model) {
		$form = new \Coxis\Admin\Libs\Form\AdminModelForm($model, $this);
		
		return $form;
	}
}