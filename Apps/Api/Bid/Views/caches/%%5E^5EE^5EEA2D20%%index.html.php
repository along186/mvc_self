<?php /* Smarty version 2.6.25-dev, created on 2016-07-02 16:31:26
         compiled from F:%5Cwamp%5Cwww%5Cmvc_self/Apps/Api/Bid/Views/index.html */ ?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Insert title here</title>
</head>
<body>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'header.html', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php echo $this->_tpl_vars['sessionname']; ?>

<table border="1">
<tr>
<td>用户ID</td>
<td>用户姓名</td>
<td>用户邮箱</td>
<td>用户年龄</td>
</tr>
<?php $_from = $this->_tpl_vars['info']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['vi']):
?>
<?php if (( $this->_tpl_vars['vi']['u_age'] == 28 )): ?>
<tr>
<td><?php echo $this->_tpl_vars['vi']['u_id']; ?>
</td>
<td><?php echo $this->_tpl_vars['vi']['u_name']; ?>
</td>
<td><?php echo $this->_tpl_vars['vi']['u_email']; ?>
</td>
<td><?php echo $this->_tpl_vars['vi']['u_age']; ?>
</td>
</tr>
<?php elseif (( $this->_tpl_vars['vi']['u_age'] == 27 )): ?>
<tr>
<td><?php echo $this->_tpl_vars['k']; ?>
<?php echo $this->_tpl_vars['vi']['u_id']; ?>
老婆</td>
<td><?php echo $this->_tpl_vars['vi']['u_name']; ?>
老婆</td>
<td><?php echo $this->_tpl_vars['vi']['u_email']; ?>
老婆</td>
<td><?php echo $this->_tpl_vars['vi']['u_age']; ?>
老婆</td>
</tr>
<?php else: ?>
<tr>
<td><?php echo $this->_tpl_vars['k']; ?>
<?php echo $this->_tpl_vars['vi']['u_id']; ?>
宝宝</td>
<td><?php echo $this->_tpl_vars['vi']['u_name']; ?>
宝宝</td>
<td><?php echo $this->_tpl_vars['vi']['u_email']; ?>
宝宝</td>
<td><?php echo $this->_tpl_vars['vi']['u_age']; ?>
宝宝</td>
</tr>
<?php endif; ?>
<?php endforeach; endif; unset($_from); ?>
</table>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'footer.html', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
</body>
</html>