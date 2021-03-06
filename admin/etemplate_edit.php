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


	if (isset($_POST['action']) && $_POST['action'] == "editetemplate")
	{
		$etemplate_id	= (int)getPostParameter('eid');
		$email_name		= mysql_real_escape_string($_POST['email_name']);
		$language		= mysql_real_escape_string($_POST['language']);
		$email_subject	= mysql_real_escape_string($_POST['esubject']);
		$email_message	= mysql_real_escape_string($_POST['emessage']);

		if ($_POST['update'] && $_POST['update'] != "")
		{
			unset($errs);
			$errs = array();

			if (!($email_name && $language && $email_subject && $email_message))
			{
				$errs[] = "Please fill in all required fields";
			}

			if (count($errs) == 0)
			{
				$sql = "UPDATE cashbackengine_email_templates SET language='$language', email_name='$email_name', email_subject='$email_subject', email_message='$email_message', modified=NOW() WHERE template_id='$etemplate_id' LIMIT 1";

				if (smart_mysql_query($sql))
				{
					header("Location: etemplates.php?msg=updated");
					exit();
				}
			}
			else
			{
				$allerrors = "";
				foreach ($errs as $errorname)
					$allerrors .= "&#155; ".$errorname."<br/>\n";
			}
		}
	}


	if (isset($_GET['id']) && is_numeric($_GET['id'])) { $eid = (int)$_GET['id']; } else { $eid = (int)$_POST['eid']; }
	
	$query = "SELECT * FROM cashbackengine_email_templates WHERE template_id='$eid' LIMIT 1";
	$result = smart_mysql_query($query);
	$total = mysql_num_rows($result);


	$title = "Edit Email Template";
	require_once ("inc/header.inc.php");

?>
 
      <?php if ($total > 0) {

		  $row = mysql_fetch_array($result);
		  
      ?>

        <h2>Edit Email Template</h2>

		<?php if (isset($allerrors) && $allerrors != "") { ?>
			<div class="error_box"><?php echo $allerrors; ?></div>
		<?php } ?>

        <form action="" name="form1" method="post">
          <table width="98%" align="center" cellpadding="2" cellspacing="5" border="0">
          <tr>
            <td nowrap="nowrap" width="35" valign="middle" align="right" class="tb1"><span class="req">* </span>Template Name:</td>
            <td valign="top">
				<select name="email_name" onChange="document.form1.submit()">
					<option value="">-- select template name --</option>
					<option value="signup" <?php if ($row['email_name'] == "signup") echo "selected='selected'"; ?>>Sign Up email</option>
					<option value="activate" <?php if ($row['email_name'] == "activate") echo "selected='selected'"; ?>>Registration Confirmation email</option>
					<option value="activate2" <?php if ($row['email_name'] == "activate2") echo "selected='selected'"; ?>>Account activation email</option>
					<option value="forgot_password" <?php if ($row['email_name'] == "forgot_password") echo "selected='selected'"; ?>>Forgot Password email</option>
					<option value="invite_friend" <?php if ($row['email_name'] == "invite_friend") echo "selected='selected'"; ?>>Invite a Friend email</option>
				</select>			
			</td>
          </tr>
          <tr>
            <td nowrap="nowrap" width="35" valign="middle" align="right" class="tb1"><span class="req">* </span>Language:</td>
            <td valign="top">
				<select name="language">
				<option value="">-- select --</option>
				<?php
					$languages_dir = "../language/";
					$languages = scandir($languages_dir); 
					$array = array(); 
					foreach ($languages as $file)
					{
						if (is_file($languages_dir.$file) && strstr($file, ".inc.php")) { $language= str_replace(".inc.php","",$file);
				?>
					<option value="<?php echo $language; ?>" <?php if ($row['language'] == $language) echo 'selected="selected"'; ?>><?php echo $language; ?></option>
					<?php } ?>
				<?php } ?>
				</select>			
			</td>
          </tr>
          <tr>
            <td nowrap="nowrap" width="35" valign="middle" align="right" class="tb1"><span class="req">* </span>Email Subject:</td>
            <td valign="top"><input type="text" name="esubject" id="esubject" value="<?php echo $row['email_subject']; ?>" size="70" class="textbox" /></td>
          </tr>
           <tr>
            <td>&nbsp;</td>
            <td height="50" style="border: 1px solid #EEEEEE;" bgcolor="#F7F7F7" align="center" valign="middle">
				<p>Please use following variables for this email template:</p>
				<table width="95%" align="center" cellpadding="2" cellspacing="2" border="0">
					<?php if ($row['email_name'] == "signup") { ?>
						<tr><td nowrap="nowrap" align="right"><b>{first_name}</b></td><td nowrap="nowrap" align="left"> - Member First Name</td></tr>
						<tr><td nowrap="nowrap" align="right"><b>{username}</b></td><td nowrap="nowrap" align="left"> - Member Username</td></tr>
						<tr><td nowrap="nowrap" align="right"><b>{password}</b></td><td nowrap="nowrap" align="left"> - Member Password</td></tr>
						<tr><td nowrap="nowrap" align="right"><b>{login_url}</b></td><td nowrap="nowrap" align="left"> - Login Link</td></tr>
					<?php }elseif($row['email_name'] == "activate") { ?>
						<tr><td nowrap="nowrap" align="right"><b>{first_name}</b></td><td nowrap="nowrap" align="left"> - Member First Name</td></tr>
						<tr><td nowrap="nowrap" align="right"><b>{username}</b></td><td nowrap="nowrap" align="left"> - Member Username</td></tr>
						<tr><td nowrap="nowrap" align="right"><b>{password}</b></td><td nowrap="nowrap" align="left"> - Member Password</td></tr>
						<tr><td nowrap="nowrap" align="right"><b>{activate_link}</b></td><td nowrap="nowrap" align="left"> - Activation Link</td></tr>
					<?php }elseif($row['email_name'] == "activate2") { ?>
						<tr><td nowrap="nowrap" align="right"><b>{first_name}</b></td><td nowrap="nowrap" align="left"> - Member First Name</td></tr>
						<tr><td nowrap="nowrap" align="right"><b>{username}</b></td><td nowrap="nowrap" align="left"> - Member Username</td></tr>
						<tr><td nowrap="nowrap" align="right"><b>{password}</b></td><td nowrap="nowrap" align="left"> - Member Password</td></tr>
						<tr><td nowrap="nowrap" align="right"><b>{activate_link}</b></td><td nowrap="nowrap" align="left"> - Activation Link</td></tr>
					<?php }elseif($row['email_name'] == "forgot_password") { ?>
						<tr><td nowrap="nowrap" align="right"><b>{first_name}</b></td><td nowrap="nowrap" align="left"> - Member First Name</td></tr>
						<tr><td nowrap="nowrap" align="right"><b>{username}</b></td><td nowrap="nowrap" align="left"> - Member Username</td></tr>
						<tr><td nowrap="nowrap" align="right"><b>{password}</b></td><td nowrap="nowrap" align="left"> - Member Password</td></tr>
						<tr><td nowrap="nowrap" align="right"><b>{login_url}</b></td><td nowrap="nowrap" align="left"> - Login Link</td></tr>
					<?php }elseif($row['email_name'] == "invite_friend") { ?>
						<tr><td nowrap="nowrap" align="right"><b>{friend_name}</b></td><td nowrap="nowrap" align="left"> - Friend First Name</td></tr>
						<tr><td nowrap="nowrap" align="right"><b>{first_name}</b></td><td nowrap="nowrap" align="left"> - Member First Name</td></tr>
						<tr><td nowrap="nowrap" align="right"><b>{referral_link}</b></td><td nowrap="nowrap" align="left"> - Referral Link</td></tr>
					<?php } ?>
				</table>
			</td>
          </tr>
          <tr>
            <td nowrap="nowrap" valign="middle" align="right" class="tb1"><span class="req">* </span>Email Message:</td>
            <td valign="top">
				<textarea cols="80" id="editor" name="emessage" rows="10"><?php echo stripslashes($row['email_message']); ?></textarea>
				<script type="text/javascript" src="./js/ckeditor/ckeditor.js"></script>
				<script>
					CKEDITOR.replace( 'editor' );
				</script>		
			</td>
          </tr>
          <tr>
            <td colspan="2" align="center" valign="bottom">
			<input type="hidden" name="eid" id="eid" value="<?php echo (int)$row['template_id']; ?>" />
			<input type="hidden" name="action" id="action" value="editetemplate" />
			<input type="submit" name="update" id="update" class="submit" value="Update" />
			<input type="button" class="cancel" name="cancel" value="Cancel" onClick="javascript:document.location.href='etemplates.php'" />
		  </td>
          </tr>
        </table>
      </form>

      <?php }else{ ?>
				<p align="center">Sorry, no record found.<br/><br/><a class="goback" href="#" onclick="history.go(-1);return false;">Go Back</a></p>
      <?php } ?>

<?php require_once ("inc/footer.inc.php"); ?>