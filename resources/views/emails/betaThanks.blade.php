<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>Please Confirm your email</title>
	</head>
	<body style="margin:0px; padding:0px;">
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td>
					<!-- Start email content -->
					<table align="center" bgcolor="#ffffff" border="0" cellspacing="0" cellpadding="0" style="width:727px; margin:0 auto;">
						<tr>
							<td colspan="4" bgcolor="#000000" height="53" style="padding:14px 0 0 13px;"><img src="http://plexuss.com/images/email/emaillogo.png" alt="" /></td>
						</tr>
						<tr>
							<td bgcolor="#145C28" colspan="4" height="28"></td>
						</tr>
						
						<tr>
							<td>
								<table border="0" cellspacing="0" cellpadding="0">
									<tr>
										<td width="245" height="445" background="http://plexuss.com/images/email/clocktower1.png"></td>
										<td valign="top" width="482" height="445" background="http://plexuss.com/images/email/clocktower2.png">
											<table align="center" border="0" cellspacing="0" cellpadding="0">
												<tr><td height="140px"></td></tr>
												<tr><td valign="top" style="font-size: 38px; font-family: tahoma,arial;">Thank You</td></tr>
												<tr>
													<td><p style="font-size: 17px; font-family: tahoma,arial; margin-bottom: 16px;">Thank you for signing up for Plexuss beta, {{$name}}!<br/>
Just click below to confirm your email address.</p>

<a style="font-size: 20px; font-weight: bold; font-family: ta,arial;" href="{{url('confirm-beta-email', $parameters = array('cofirmation'=> $confirmation))}}">Click Here to confirm your email.</a>
</td>
												</tr>
											</table>


										</td>
									</tr>

								</table>	








							</td>
						</tr>
						<tr>
							<td bgcolor="#000000" colspan="4" style="padding: 26px 0px 20px; text-align: center; font-family: verdana,Arial,Helvetica,sans-serif; font-size: 12px; color: rgb(255, 255, 255);">
								<!-- Footer -->
								<table width="400" border="0" align="center" cellpadding="0" cellspacing="0" style="font-size:12px; color:#fff; line-height:18px;">
									<tr>
										<td align="center"><table border="0" cellspacing="5" cellpadding="0" style="border-collapse: separate;">
											<tr>
												<td>Please email support@plexuss.com to unsubscribe.</td>
											</tr>
										</table></td>
									</tr>
									<tr>
										<td height="5"></td>
									</tr>

									<tr>
										<td height="10"></td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</body>
</html>