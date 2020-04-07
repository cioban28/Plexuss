<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>Please verify your email address</title>
	</head> 
	<body style="margin:0px; padding:0px; height:100%;">
		<!-- Main Wrapper-->
		<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#E8E8E8">
			<tr>
				<td  valign='top'>
					<table width="600" cellspacing="0" cellpadding="0" border="0" align="center" bgcolor='#ffffff'>
						<tr>
							<td height='20'></td>
						</tr>
						<tr>
							<td>
								<table cellpadding="0" border="0">
									<tr>
										<td width='35'></td>
										<td width='530'>
											<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/images/email_images/emailClearLogo.png" alt='Plexuss.com'/>
										</td>
									</tr>
									<tr>
										<td></td>
										<td height='30'></td>
									</tr>
									
									<tr>
										<td></td>
										<td>
											<h1 style='font-size: 28px; margin: 0px; padding: 0px; color: rgb(38, 178, 75); font-family: "Century Gothic",CenturyGothic,AppleGothic,sans-serif; font-weight: normal;'>Verify your email address</h1>
										</td>
									</tr>
									<tr>
										<td></td>
										<td height='10'></td>
									</tr>
									<tr>
										<td></td>
										<td style='color:#79796A;font-family: "Century Gothic",CenturyGothic,AppleGothic,sans-serif;font-weight: normal;font-size: 12px;'>We care about your safety so we need to make sure this email address is yours.</td>
									</tr>
									<tr>
										<td></td>
										<td height='20'></td>
									</tr>
									<tr>
										<td></td>
										<td height='30'>
											<a style='text-decoration: none;height: 31px; text-align: center; background-color: rgb(255, 128, 0); color: rgb(255, 255, 255); font-size: 20px; font-family: "Century Gothic",CenturyGothic,AppleGothic,sans-serif; margin: 0px; padding: 0px; border-width: 10px 30px; border-style: solid; border-color: rgb(255, 128, 0);' href="{{ action('AuthController@confirmEmail', $parameters = array('cofirmation'=> $confirmation)) }}">Verify {{ $email }}</a>
										</td>
									</tr>
									<tr>
										<td></td>
										<td height='25'></td>
									</tr>
									

									<!--
									<tr>
										<td></td>
										<td height='47' style='color:#000;font-family: "Century Gothic",CenturyGothic,AppleGothic,sans-serif;font-weight: normal;font-size: 20px;'>Also, enjoy this gift from Snapfish.</td>
									</tr>


									<tr>
										<td></td>
										<td style='color:#797979;font-family: "Century Gothic",CenturyGothic,AppleGothic,sans-serif;font-weight: normal;font-size: 14px;'>We’re delighted to announce that Snapfish by HP is partnering with Plexuss for the holidays.</td>
									</tr>
									<tr>
										<td></td>
										<td height='10'></td>
									</tr>
									
									<tr>
										<td></td>
										<td style='color:#797979;font-family: "Century Gothic",CenturyGothic,AppleGothic,sans-serif;font-weight: normal;font-size: 14px;'>We invite you to enjoy $10 + FREE shipping to make one-of-a-kind photo gifts.</td>
									</tr>

									<tr>
										<td></td>
										<td height='20'></td>
									</tr>
									<tr>
										<td></td>
										<td style='background:#F5F5F5;color:#79796A;border:solid 2px #B4B4B4; padding:20px; font-family: "Century Gothic",CenturyGothic,AppleGothic,sans-serif;font-weight: normal;font-size: 16px;'>Enter the promo code <span style='font-size: 16px; color:#26B24B; font-weight: bold; '>PLEXUSS</span> before checking out</td>
									</tr>

									<tr>
										<td></td>
										<td height='20'></td>
									</tr>

									<tr>
										<td></td>
										<td><a href="{{ action('AuthController@confirmEmail', $parameters = array('cofirmation'=> $confirmation))  }}" style='color:#FF5C26;font-family: "Century Gothic",CenturyGothic,AppleGothic,sans-serif;font-weight: normal;font-size: 14px;'>
											Click here to get started.
											</a>
										</td>
									</tr>
									-->



									<tr>
										<td></td>
										<td height='50'></td>
									</tr>
									
									


									
									<!--
									<tr>
										<td></td>
										<td style='color:#79796A;font-family: "Century Gothic",CenturyGothic,AppleGothic,sans-serif;font-weight: normal;font-size: 11px;'>If you’d like to stop receiving these emails, or have received this by accident, please click here.</td>
									</tr>
									-->
									<tr>
										<td></td>
										<td height='15'></td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td height='29' valign='top'>
								<img style='display: block;' alt='Plexuss Brick footer' src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/images/email_images/redbrickpattern.png"/>
							</td>
						</tr>
						<tr>
							<td height='40' bgcolor='#202020'><span style='color: rgb(255, 255, 255); font-family: "Century Gothic",CenturyGothic,AppleGothic,sans-serif; font-size: 12px; margin-left: 30px;'>&copy; 2014 Plexuss, All rights reserved.</span></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</body>
</html>
