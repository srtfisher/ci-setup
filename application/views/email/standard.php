<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>{title}</title>
</head>

<body>
<style type="text/css">
body { background: url('http://truanttoday.com/assets/images/wood_header_footer.jpg') repeat #602d1c; color: #333; width:100%; margin:0; padding:10px 0;}
p { margin: 0 0 10px 0; font-family: Arial, Helvetica, sans-serif; font-size: 14px; }
</style>
<!-- Parent Table to give the email's background -->
<table width="100%" bgcolor="#602d1c" style="background-image:url('http://truanttoday.com/assets/images/wood_header_footer.jpg'); background-color:#602d1c; background-repeat: repeat; padding:10px 0;">
<tr>
     <td>
     <!-- Inside Parent -->
     <table width="95%" border="0" bgcolor="#ffffff" style="color: #DDE8EE; -webkit-border-radius: 5px; -moz-border-radius: 5px; border-radius: 5px; margin:0 auto;">
	<tr>
		<td>
		<table width="100%" border="0" align="center" cellpadding="10" bgcolor="#f5f5f5" style="border:1px solid #dddddd; -webkit-border-radius: 5px; -moz-border-radius: 5px; border-radius: 5px;">
				<tr>
					<td>
					<!-- Logo -->
					<a href="http://truanttoday.com/"><img src="<?=site_url('assets/images/email-logo.jpg')?>" /></a>
					
					<!-- Title -->
					<h3 style="margin:0;padding:0;color:#21759b;font-size:20px;font-weight:bold;font-family:Arial;line-height:28px; padding:5px 0;">{title}</h3>
					
					<table width="100%" border="0" bordercolor="#CBDDE3" align="center" cellpadding="10" bgcolor="#ffffff" style="border: 1px solid #ececec; -webkit-border-radius: 5px; -moz-border-radius: 5px; border-radius: 5px;">
							<tr>
								<td><p><font color="#000" face="Arial, Helvetica, sans-serif" style="font-size:14px;"><strong>Hello there!</strong></font></p>
									<p><font color="#000" face="Arial, Helvetica, sans-serif"  style="font-size:14px;">{content}</p>
									
					                    <p><font color="#000" face="Arial, Helvetica, sans-serif"  style="font-size:14px;">Thanks!<br />
										The TruantToday Team  <br />
										</font>
									</p></td>
							</tr>
						</table></td>
				</tr>
			</table>
			</td>
	</tr>
</table>

<center>
     <p style="color:#808080; font-family: Arial, Helvetica, sans-serif; font-size:14px; padding:8px 0;text-shadow:0px 1px 0px #000000;">If you experienced any problems, shoot us an email at <a href="mailto:support@truanttoday.com"><font color="#808080">support@truanttoday.com</font></a>.</p>
</center>
     <!-- End of parent -->
     </td>
</tr>
</table>
			
</body>
</html>