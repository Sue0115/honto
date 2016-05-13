<?php $this->load->view('admin/common/header')?>
<?php $this->load->view('admin/common/top')?>
<?php $this->load->view('admin/common/left')?>

<?php $this->load->view(isset($tpl) && $tpl ? $tpl : 'admin/index'); ?>

<?php $this->load->view('admin/common/footer')?>
