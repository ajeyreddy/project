<?php
/*******************************************************************\
 * CashbackEngine v2.1
 * http://www.CashbackEngine.net
 *
 * Copyright (c) 2010-2014 CashbackEngine Software. All rights reserved.
 * ------------ CashbackEngine IS NOT FREE SOFTWARE --------------
\*******************************************************************/

	session_start();
	require_once("inc/auth.inc.php");
	require_once("inc/config.inc.php");

	$cc = 0;

	function getRepliesNum($message_id)
	{
		global $userid;
		
		$message_id = (int)$message_id;
		
		$query = "SELECT COUNT(answer_id) as total_replies FROM cashbackengine_messages_answers WHERE message_id='$message_id' AND user_id='$userid' AND is_admin='1'";
		$result = smart_mysql_query($query);
		$row = mysql_fetch_array($result);
		
		$total_replies = $row['total_replies'];

		if ($total_replies > 0) 
			return "<span class='all_replies'>".$total_replies."</span>";
		else
			return "<span class='no_replies'>".$total_replies."</span>";
	}


	if (isset($_POST['action']) && $_POST['action'] == "mysupport")
	{
		unset($errs);
		$errs = array();

		$subject = mysql_real_escape_string(getPostParameter('subject'));
		$message = mysql_real_escape_string(nl2br(getPostParameter('message')));

		if(!($subject && $message))
		{
			$errs[] = CBE1_MYMESSAGES_ERR2;
		}

		if (count($errs) == 0)
		{
			$ins_query = "INSERT INTO cashbackengine_messages SET user_id='$userid', subject='$subject', message='$message', status='new', created=NOW()";
			if (smart_mysql_query($ins_query))
			{
				// send email notification //
				if (NEW_TICKET_ALERT == 1)
				{
					SendEmail(SITE_ALERTS_MAIL, CBE1_EMAIL_ALERT3, CBE1_EMAIL_ALERT3_MSG);
				}
				/////////////////////////////

				header("Location: mysupport.php?msg=1");
				exit();
			}
		}
		else
		{
			$allerrors = "";
			foreach ($errs as $errorname)
				$allerrors .= $errorname."<br/>\n";
		}
	}


	if (isset($_POST['action']) && $_POST['action'] == "reply")
	{
		unset($errs2);
		$errs2 = array();

		$message_id = mysql_real_escape_string(getPostParameter('mid'));
		$answer = mysql_real_escape_string(nl2br(getPostParameter('answer')));

		if(!($message_id && $answer))
		{
			$errs2[] = CBE1_MYMESSAGES_ERR1;
		}

		if (count($errs2) == 0)
		{
			$ins_query = "INSERT INTO cashbackengine_messages_answers SET message_id='$message_id', user_id='$userid', answer='$answer', answer_date=NOW()";
			if (smart_mysql_query($ins_query))
			{
				// send email notification //
				if (NEW_TICKET_REPLY_ALERT == 1)
				{
					SendEmail(SITE_ALERTS_MAIL, CBE1_EMAIL_ALERT4, CBE1_EMAIL_ALERT4_MSG);
				}
				/////////////////////////////

				header("Location: mysupport.php?msg=1");
				exit();
			}
		}
		else
		{
			$allerrors2 = "";
			foreach ($errs2 as $errorname2)
				$allerrors2 .= $errorname2."<br/>\n";
		}
	}


	///////////////  Page config  ///////////////
	$PAGE_TITLE = CBE1_MYMESSAGES_TITLE;

	require_once ("inc/header.inc.php");

?>
	
	<?php if (isset($_GET['msg']) and $_GET['msg'] == 1) { ?>
		<div class="success_msg"><?php echo CBE1_MYMESSAGES_SENT; ?></div>
	<?php }else if (!(isset($_REQUEST['mid']) && is_numeric($_REQUEST['mid']))) { ?>

		<h1><?php echo CBE1_MYMESSAGES_TITLE; ?></h1>

		<p align="center"><img src="<?php echo SITE_URL; ?>images/icon_support.png" border="0" /></p>
		<p align="center"><?php echo CBE1_MYMESSAGES_TEXT; ?></p>
 
		<?php if (isset($allerrors) and $allerrors != "") { ?>
			<div class="error_msg" style="width: 65%"><?php echo $allerrors; ?></div>
		<?php } ?>

 		<form action="" method="post">
		<table align="center" width="75%" border="0" cellpadding="3" cellspacing="0">
          <tr>
            <td align="right" valign="middle"><?php echo CBE1_MYMESSAGES_SUBJECT; ?>:</td>
            <td align="left" valign="top"><input type="text" class="textbox" name="subject" value="<?php echo getPostParameter('subject'); ?>" size="53" /></td>
          </tr>
          <tr>
            <td align="right" valign="top"><?php echo CBE1_MYMESSAGES_MESSAGE; ?>:</td>
            <td align="left" valign="top"><textarea cols="50" rows="8" class="textbox2" name="message"><?php echo getPostParameter('message'); ?></textarea></td>
          </tr>
          <tr>
            <td align="right" valign="middle">&nbsp;</td>
			<td align="left" valign="middle">
				<input type="hidden" name="action" id="action" value="mysupport" />
				<input type="submit" class="submit" name="Send" id="Sent" value="<?php echo CBE1_MYMESSAGES_BUTTON; ?>" />
		  </td>
          </tr>
		  </table>
		</form>

	<?php } ?>


	<?php

		$mquery = "SELECT m.*, DATE_FORMAT(m.created, '%e %b %Y %h:%i %p') AS message_date, u.fname, u.lname FROM cashbackengine_messages m, cashbackengine_users u WHERE u.user_id='$userid' AND m.is_admin='0' AND m.user_id=u.user_id ORDER BY created DESC";
		$mresult = smart_mysql_query($mquery);
		$mtotal = mysql_num_rows($mresult);

	?>

	<h1><?php echo CBE1_MYMESSAGES_TITLE2; ?></h1>

	<?php

	if (isset($_REQUEST['mid']) && is_numeric($_REQUEST['mid']))
	{
			$message_id = (int)$_REQUEST['mid'];
			$ms_query = "SELECT *, DATE_FORMAT(created, '%e %b %Y %h:%i %p') AS sent_date FROM cashbackengine_messages WHERE user_id='$userid' AND message_id='$message_id' LIMIT 1";
			$ms_result = smart_mysql_query($ms_query);
			
			if (mysql_num_rows($ms_result) > 0)
			{
				$ms_row = mysql_fetch_array($ms_result);
		?>
			<div class="message_date"><?php echo $ms_row['sent_date']; ?></div>
			<div class="message_subject"><?php echo $ms_row['subject']; ?></div>
			<div class="message_text"><?php echo $ms_row['message']; ?></div>
		<?php
			}
		?>

		<?php

			$aquery = "SELECT *, DATE_FORMAT(answer_date, '%e %b %Y %h:%i %p') AS a_date FROM cashbackengine_messages_answers WHERE user_id='$userid' AND message_id='$message_id' ORDER BY answer_date ASC";
			$aresult = smart_mysql_query($aquery);
		
			if (mysql_num_rows($aresult) > 0)
			{
				// mark message as viewed //
				smart_mysql_query("UPDATE cashbackengine_messages_answers SET viewed='1' WHERE message_id='$message_id' AND user_id='$userid'");
				while ($arow = mysql_fetch_array($aresult)) {
		?>
				<div class="answer_date"><?php echo $arow['a_date']; ?></div>
				<div class="<?php echo ($arow['is_admin'] == 1) ? "answer_support" : "answer_sender"; ?>"><?php echo ($arow['is_admin'] == 1) ? CBE1_MYMESSAGES_ADMIN : $_SESSION['FirstName']; ?></div>
				<div class="answer_text"><?php echo $arow['answer']; ?></div>
			<?php 
			}
		}
		?>


		<?php if (isset($allerrors2) and $allerrors2 != "") { ?>
			<div class="error_msg"><?php echo $allerrors2; ?></div>
		<?php } ?>

 		<form action="" method="post">
		<table align="center" border="0" cellpadding="3" cellspacing="0">
          <tr>
            <td align="left" valign="top">
				<a name="reply"></a>
				<h3><?php echo CBE1_MYMESSAGES_REPLY; ?></h3>
				<textarea cols="50" rows="5" class="textbox2" name="answer"><?php echo getPostParameter('answer'); ?></textarea>
			</td>
          </tr>
          <tr>
			<td align="left" valign="middle">
				<input type="hidden" name="mid" id="mid" value="<?php echo $message_id; ?>" />
				<input type="hidden" name="action" id="action" value="reply" />
				<input type="submit" class="submit" name="Send" id="Sent" value="<?php echo CBE1_MYMESSAGES_REPLY_BUTTON; ?>" />
			</td>
          </tr>
		</table>
		</form>

		<p><div class="sline"></div></p>
		<p align="center"><a class="goback" href="<?php echo SITE_URL; ?>mysupport.php"><?php echo CBE1_GO_BACK; ?></a></p>

	<?php

	 }
	 else
	 {
		 
	?>

	<?php if ($mtotal > 0) { ?>

			<table align="center" class="btb" border="0" cellpadding="3" cellspacing="0">
			<tr>
				<th width="3%">&nbsp;</th>
				<th width="55%"><?php echo CBE1_MYMESSAGES_SUBJECT; ?></th>
				<th width="22%"><?php echo CBE1_MYMESSAGES_DATE; ?></th>
				<th width="10%"><?php echo CBE1_MYMESSAGES_REPLIES; ?></th>
				<th width="10%"><?php echo CBE1_MYMESSAGES_ACTIONS; ?></th>
			</tr>
			<?php while ($mrow = mysql_fetch_array($mresult)) { $cc++; ?>
				  <tr class="<?php if (($cc%2) == 0) echo "row_even"; else echo "row_odd"; ?>">
					<td align="center" valign="middle"><img src="<?php echo SITE_URL; ?>images/icon_message.png" /></td>
					<td align="left" valign="middle">
						<a href="<?php echo SITE_URL; ?>mysupport.php?mid=<?php echo $mrow['message_id']; ?>">
						<?php
							if (strlen($mrow["subject"]) > 100) $msubject = substr($mrow["subject"], 0, 100)."..."; else $msubject = $mrow["subject"];
							echo $msubject;
						?>
						</a>
					</td>
					<td align="center" valign="middle"><?php echo $mrow["message_date"]; ?></td>
					<td align="center" valign="middle"><a href="<?php echo SITE_URL; ?>mysupport.php?mid=<?php echo $mrow['message_id']; ?>" title="<?php echo CBE1_MYMESSAGES_VIEW; ?>"><?php echo getRepliesNum($mrow['message_id']); ?></a></td>
					<td nowrap="nowrap" align="center" valign="middle">
						<a href="<?php echo SITE_URL; ?>mysupport.php?mid=<?php echo $mrow['message_id']; ?>" title="<?php echo CBE1_MYMESSAGES_VIEW; ?>"><img src="<?php echo SITE_URL; ?>images/icon_view.png" alt="<?php echo CBE1_MYMESSAGES_VIEW; ?>" border="0" /></a>
						<a href="<?php echo SITE_URL; ?>mysupport.php?mid=<?php echo $mrow['message_id']; ?>#reply" title="<?php echo CBE1_MYMESSAGES_REPLY; ?>"><img src="<?php echo SITE_URL; ?>images/icon_reply.png" alt="<?php echo CBE1_MYMESSAGES_REPLY; ?>" border="0" /></a>
					</td>
				  </tr>
			<?php } ?>
			</table>

        <?php }else{ ?>
				<p align="center"><?php echo CBE1_MYMESSAGES_NO; ?></p>
        <?php } ?>
	
	<?php } ?>

<?php require_once ("inc/footer.inc.php"); ?>