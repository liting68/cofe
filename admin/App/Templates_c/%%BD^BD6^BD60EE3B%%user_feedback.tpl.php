<?php /* Smarty version 2.6.18, created on 2014-11-05 17:04:51
         compiled from user/user_feedback.tpl */ ?>
<td valign="top" align="center">
 	<div class="main_ta_box">
         <div class="hd_t">用户反馈</div>
         <form action="" method="get">
         <input type="hidden" name="controller" value="User" />
         <input type="hidden" name="action" value="Feedback" />
         <div class="hd_t1">查找<input class="cz_input" type="text" name="keyword" value="<?php echo $this->_tpl_vars['keyword']; ?>
"><input class="cz_btn" type="submit" value="查找"></div>
         </form>
         <table class="hd_ta" border="0" cellpadding="0" cellspacing="1" width="97%" align="center">
			<colgroup>
				<col width="10%">
				<col width="10%">
				<col width="5%">
				<col width="10%">
				<col width="10%">
				<col width="">
				<col width="20%">
			</colgroup>
             <tr>
                 <th>账号</th>
                 <th>昵称</th>
                 <th>性别</th>
                 <th>电话</th>
                 <th>邮箱</th>
                 <th>反馈内容</th>
                 <th>反馈时间</th>
             </tr>
             <?php unset($this->_sections['sec']);
$this->_sections['sec']['name'] = 'sec';
$this->_sections['sec']['loop'] = is_array($_loop=$this->_tpl_vars['list']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['sec']['show'] = true;
$this->_sections['sec']['max'] = $this->_sections['sec']['loop'];
$this->_sections['sec']['step'] = 1;
$this->_sections['sec']['start'] = $this->_sections['sec']['step'] > 0 ? 0 : $this->_sections['sec']['loop']-1;
if ($this->_sections['sec']['show']) {
    $this->_sections['sec']['total'] = $this->_sections['sec']['loop'];
    if ($this->_sections['sec']['total'] == 0)
        $this->_sections['sec']['show'] = false;
} else
    $this->_sections['sec']['total'] = 0;
if ($this->_sections['sec']['show']):

            for ($this->_sections['sec']['index'] = $this->_sections['sec']['start'], $this->_sections['sec']['iteration'] = 1;
                 $this->_sections['sec']['iteration'] <= $this->_sections['sec']['total'];
                 $this->_sections['sec']['index'] += $this->_sections['sec']['step'], $this->_sections['sec']['iteration']++):
$this->_sections['sec']['rownum'] = $this->_sections['sec']['iteration'];
$this->_sections['sec']['index_prev'] = $this->_sections['sec']['index'] - $this->_sections['sec']['step'];
$this->_sections['sec']['index_next'] = $this->_sections['sec']['index'] + $this->_sections['sec']['step'];
$this->_sections['sec']['first']      = ($this->_sections['sec']['iteration'] == 1);
$this->_sections['sec']['last']       = ($this->_sections['sec']['iteration'] == $this->_sections['sec']['total']);
?>
             <tr>
                 <td><?php echo $this->_tpl_vars['list'][$this->_sections['sec']['index']]['user_name']; ?>
</td>
                 <td><?php echo $this->_tpl_vars['list'][$this->_sections['sec']['index']]['nick_name']; ?>
</td>
                 <td><?php if ($this->_tpl_vars['list'][$this->_sections['sec']['index']]['sex'] == 1): ?>男<?php elseif ($this->_tpl_vars['list'][$this->_sections['sec']['index']]['sex'] == 2): ?>女<?php endif; ?></td>
                 <td><?php echo $this->_tpl_vars['list'][$this->_sections['sec']['index']]['mobile']; ?>
</td>
                 <td><?php echo $this->_tpl_vars['list'][$this->_sections['sec']['index']]['email']; ?>
</td>
                 <td><?php echo $this->_tpl_vars['list'][$this->_sections['sec']['index']]['content']; ?>
</td>
                 <td><?php echo $this->_tpl_vars['list'][$this->_sections['sec']['index']]['created']; ?>
</td>
             </tr>
             <?php endfor; endif; ?>
         </table>
         <?php echo $this->_tpl_vars['page']; ?>

     </div>
 </td>