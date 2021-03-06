<?php 
/*******************************************************************\
 * CashbackEngine v2.1
 * http://www.CashbackEngine.net
 *
 * Copyright (c) 2010-2014 CashbackEngine Software. All rights reserved.
 * ------------ CashbackEngine IS NOT FREE SOFTWARE --------------
\*******************************************************************/

	session_start();
	require_once("../inc/adm_auth.inc.php");
	require_once("../inc/config.inc.php");
	require_once("./inc/admin_funcs.inc.php");


	$query = "SELECT sale.created,sale.title, sale.description, sale.trending_sale_id, retailer.title as retailer_title, retailer.retailer_id FROM cashbackengine_trending_sales as sale 
	join cashbackengine_retailers as retailer 
	on retailer.retailer_id = sale.retailer_id 
	ORDER BY sale.trending_sale_id desc";
	
	$result = smart_mysql_query($query);
	$total = mysql_num_rows($result);
	$cc = 0;
	$title = "Trending Sales";
	require_once ("inc/header.inc.php");

?>

		<div id="addnew"><a class="addnew" href="trending_sale_add_edit.php">Add Trending Sale</a></div>

		<h2>Trending Sales</h2>

        <?php if ($total > 0) { ?>
			<?php         	
        	$msg_type = isset($_GET['msg_type']) ? $_GET['msg_type'] : 'success';
			if (isset($_GET['msg']) && $_GET['msg'] && $msg_type=='success') { 
			?>
			<div style="width:60%;" class="success_box">
				<?php
				switch ($_GET['msg'])
				{
					case "added":	echo "Trending Sale was successfully added"; break;
					case "updated": echo "Trending Sale has been successfully edited"; break;
					case "deleted": echo "Trending Sale has been successfully deleted"; break;
				}
				?>
			</div>
			<?php } ?>
			
			<?php if(isset($_GET['msg']) && $_GET['msg'] && $msg_type=='error') { ?>
			<div style="width:60%;" class="error_box">
				No such trending sale exists.
			</div>
			<?php } ?>

			<table align="center" class="tbl" width="100%" border="0" cellpadding="3" cellspacing="0">
			<tr>
				<th width="30%">Title</th>
				<th width="45%">Description</th>
				<th width="20%">Retailer</th>
				<th width="15%">Actions</th>
			</tr>
             <?php while($sale = mysql_fetch_array($result, MYSQL_ASSOC)){ $cc++; ?>
				  <tr class="<?php if (($cc%2) == 0) echo "even"; else echo "odd"; ?>">
					<td align="left" valign="middle" class="row_title"><a href="trending_sale_add_edit.php?id=<?php echo $sale['trending_sale_id']; ?>"><?php echo $sale['title']; ?></a></td>
					<td align="left" valign="middle"><?php echo $sale['description']; ?></td>
					<td align="left"><?php echo $sale['retailer_title']?></td>
					<td nowrap="nowrap" align="center" valign="middle">
						<a href="trending_sale_add_edit.php?id=<?php echo $sale['trending_sale_id']; ?>" title="Edit"><img border="0" alt="Edit" src="images/edit.png" /></a>
						<a href="#" onclick="if (confirm('Are you sure you really want to delete this Trending Sale') )location.href='trending_sale_delete.php?id=<?php echo $sale['trending_sale_id']; ?>'" title="Delete"><img border="0" alt="Delete" src="images/delete.png" /></a>
					</td>
				  </tr>
			<?php } ?>
            </table>
          
		  <?php }else{ ?>
				<div class="info_box">There is no Trending Sale at this time.</div>
          <?php } ?>

<?php require_once ("inc/footer.inc.php"); ?>