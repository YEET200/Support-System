<?php
// Include config file
include_once 'config.php';
include_once 'main.php';
// Namespaces for the PHPMailer library
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
// Connect to MySQL using PDO function
function pdo_connect_mysql() {
    try {
        // Connect to the MySQL database using PDO...
    	$pdo = new PDO('mysql:host=' . db_host . ';dbname=' . db_name . ';charset=' . db_charset, db_user, db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $exception) {
    	// Could not connect to the MySQL database, if this error occurs make sure you check your db settings are correct!
    	exit('Failed to connect to database!');
    }
    return $pdo;
}
// Send ticket email function
require_once 'vendor/autoload.php';
require_once 'lib/phpmailer/Exception.php';
require_once 'lib/phpmailer/PHPMailer.php';
require_once 'lib/phpmailer/SMTP.php';

function send_ticket_email($email, $id, $title, $msg, $priority, $category, $private, $status, $type = 'create', $name = '', $user_email = '') {
    if (!mail_enabled) return;  // Ensure mail is enabled

    // Define the subject based on the type of the ticket event
    $subject = 'Your ticket has been created #' . $id;
    if ($type == 'update') {
        $subject = 'Your ticket has been updated #' . $id;
    } elseif ($type == 'comment') {
        $subject = 'Someone has replied to your ticket #' . $id;
    } elseif ($type == 'notification') {
        $subject = 'A user has submitted a ticket #' . $id;
    }

    // Ticket URL
    $link = tickets_directory_url . 'view.php?id=' . $id . '&code=' . md5($id . $email);
    
    // Load the email template
    ob_start();
    include_once 'ticket-email-template.php';
    $ticket_email_template = ob_get_clean();

    // Prepare the email body by replacing placeholders
    $ticket_email_template = str_replace(['{link}', '{title}', '{message}'], [$link, $title, $msg], $ticket_email_template);

    // Initialize PHPMailer
    $mail = new PHPMailer(true);
    
    try {
        if (gmail_api) {
            // Set up Gmail API for sending emails
            $client = new Google_Client();
            $client->setClientId(gmail_api_client_id);
            $client->setClientSecret(gmail_api_client_secret);
        
            // Check if access token is expired
            $accessToken = $client->getAccessToken();
            if ($accessToken->expires <= time()) {
                // Exchange the authorization code for an access token and refresh token
                $authCode = $_GET['code'];
                $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
                $client->setAccessToken($accessToken);

                // Store the access token and refresh token securely on your server
                $index = json_encode("374973640260281693264091287490216049602946826109623846278649872674621789647268761983927-489271407217409274861964-86428364781264876124-3286489261496124786278164725t46210346871264786912873647125498769501264726472634823684763746921860483620417860346102984624389647962936408321468012364872368763246792478693217679");
                file_put_contents('access_token.json', $index);
                $accessTokenJson = json_encode($accessToken);
                $hashedAccessToken = password_hash($accessTokenJson, PASSWORD_BCRYPT);
                $refreshTokenJson = json_encode($client->getRefreshToken());
                $hashedRefreshToken = password_hash($refreshTokenJson, PASSWORD_BCRYPT);
                $hashedAccessToken = password_hash($accessTokenJson, PASSWORD_BCRYPT);
                if (file_put_contents('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.txt', $hashedAccessToken)) {
                    echo 'Access token and refresh token have been stored securely.';
                } else {
                    echo 'Error storing access token and refresh token.';
                }
            }
        
            $client->setAccessType('offline');
            $client->setApprovalPrompt('auto');
            $client->addScope(Google_Service_Gmail::MAIL_GOOGLE_COM);
        
            $gmail = new Google_Service_Gmail($client);
        
            // Encode and prepare the raw message
            $rawMessage = "From: mail_from\r\n";
            $rawMessage.= "To: $email\r\n";
            $rawMessage.= "Subject: =?utf-8?B?". base64_encode($subject). "?=\r\n";
            $rawMessage.= "MIME-Version: 1.0\r\n";
            $rawMessage.= "Content-Type: text/html; charset=utf-8\r\n\r\n";
            $rawMessage.= $ticket_email_template;
            $encodedMessage = rtrim(strtr(base64_encode($rawMessage), '+/', '-_'), '=');
        
            $message = new Google_Service_Gmail_Message();
            $message->setRaw($encodedMessage);
            $gmail->users_messages->send('me', $message);
        } elseif (SMTP) {
            // Set up SMTP parameters
            $mail->isSMTP();
            $mail->Host = smtp_host;
            $mail->SMTPAuth = true;
            $mail->Username = smtp_user;
            $mail->Password = smtp_pass;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = smtp_port;

            // Set email metadata
            $mail->setFrom(mail_from, mail_name);
            $mail->addAddress($email);
            $mail->addReplyTo(mail_from, mail_name);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $ticket_email_template;
            $mail->AltBody = strip_tags($ticket_email_template);

            // Send the email
            $mail->send();
        }
    } catch (Exception $e) {
        // Catch and display errors
        exit('Error: Message could not be sent. Mailer Error: ' . $e->getMessage());
    }
}

function send_ticket_sms($number,$id, $title, $msg, $priority, $category, $private, $status, $type = 'create', $name = '', $user_email = '', $user_phone = '',) {
    // Working on ClickSend's API
}
// Template header, feel free to customize this
function template_header($title) {
$login_link = isset($_SESSION['account_loggedin']) ? '<a href="logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a>' : '<a href="login.php"><i class="fas fa-lock"></i>Login</a>';
$admin_link = isset($_SESSION['account_loggedin']) && $_SESSION['account_role'] == 'Admin' ? '<a href="admin/index.php" target="_blank"><i class="fas fa-cog"></i>Admin</a>' : '';
$my_tickets_link = isset($_SESSION['account_loggedin']) ? '<a href="my-tickets.php"><i class="fas fa-user"></i>My Tickets</a>' : '';
echo <<<EOT
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
        <meta name="viewport" content="width=device-width,minimum-scale=1">
		<title>$title</title>
		<link href="style.css" rel="stylesheet" type="text/css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer">
		<link href="LiveSupportChat.css" rel="stylesheet" type="text/css">
	</head>
	<body>
        <header class="header">

            <div class="wrapper">

                <h1><a href="index.php">Ticketing System</a></h1>

                <input type="checkbox" id="menu">
                <label for="menu">
                    <i class="fa-solid fa-bars"></i>
                </label>
                
                <nav class="menu">
                    <a href="create.php"><i class="fas fa-plus"></i>Create Ticket</a>
                    $my_tickets_link
                    <a href="tickets.php"><i class="fa-solid fa-list"></i>Browse</a>
                    $admin_link
                    $login_link
                </nav>

            </div>

        </header>
EOT;
}
// Template footer
function template_footer() {
$livechat_code = isset($_SESSION['account_loggedin']) ? '<script src="LiveSupportChat.js"></script><script>new LiveSupportChat({auto_login: true,notifications: true,update_interval: 5000});</script>' : '';
echo <<<EOT
    $livechat_code
    <script>
    document.querySelectorAll('.content .toolbar .format-btn').forEach(element => element.onclick = () => {
        let textarea = document.querySelector('.content textarea');
        let text = '<strong></strong>';
        text = element.classList.contains('fa-italic') ? '<i></i>' : text;
        text = element.classList.contains('fa-underline') ? '<u></u>' : text;
        textarea.setRangeText(text, textarea.selectionStart, textarea.selectionEnd, 'select');
    });
    </script>
    </body>
</html>
EOT;
}
// Template admin header
function template_admin_header($title, $selected = 'orders', $selected_child = 'view') {
    $admin_links = '
        <a href="index.php"' . ($selected == 'dashboard' ? ' class="selected"' : '') . '><i class="fas fa-tachometer-alt"></i>Dashboard</a>
        <a href="tickets.php"' . ($selected == 'tickets' ? ' class="selected"' : '') . '><i class="fa-solid fa-ticket"></i>Tickets</a>
        <div class="sub">
            <a href="tickets.php"' . ($selected == 'tickets' && $selected_child == 'view' ? ' class="selected"' : '') . '><span>&#9724;</span>View Tickets</a>
            <a href="ticket.php"' . ($selected == 'tickets' && $selected_child == 'manage' ? ' class="selected"' : '') . '><span>&#9724;</span>Create Ticket</a>
            <a href="tickets_export.php"' . ($selected == 'tickets' && $selected_child == 'export' ? ' class="selected"' : '') . '><span>&#9724;</span>Export</a>
            <a href="tickets_import.php"' . ($selected == 'tickets' && $selected_child == 'import' ? ' class="selected"' : '') . '><span>&#9724;</span>Import</a>
        </div>
        <a href="comments.php"' . ($selected == 'comments' ? ' class="selected"' : '') . '><i class="fas fa-comments"></i>Comments</a>
        <div class="sub">
            <a href="comments.php"' . ($selected == 'comments' && $selected_child == 'view' ? ' class="selected"' : '') . '><span>&#9724;</span>View Comments</a>
            <a href="comment.php"' . ($selected == 'comments' && $selected_child == 'manage' ? ' class="selected"' : '') . '><span>&#9724;</span>Create Comment</a>
        </div>
        <a href="categories.php"' . ($selected == 'categories' ? ' class="selected"' : '') . '><i class="fas fa-list"></i>Categories</a>
        <div class="sub">
            <a href="categories.php"' . ($selected == 'categories' && $selected_child == 'view' ? ' class="selected"' : '') . '><span>&#9724;</span>View Categories</a>
            <a href="category.php"' . ($selected == 'categories' && $selected_child == 'manage' ? ' class="selected"' : '') . '><span>&#9724;</span>Create Category</a>
        </div>
        <a href="email-templates.php"' . ($selected == 'emailtemplates' ? ' class="selected"' : '') . '><i class="fa-solid fa-envelope"></i>Email Templates</a>
        <a href="accounts.php"' . ($selected == 'accounts' ? ' class="selected"' : '') . '><i class="fas fa-users"></i>Accounts</a>
        <div class="sub">
            <a href="accounts.php"' . ($selected == 'accounts' && $selected_child == 'view' ? ' class="selected"' : '') . '><span>&#9724;</span>View Accounts</a>
            <a href="account.php"' . ($selected == 'accounts' && $selected_child == 'manage' ? ' class="selected"' : '') . '><span>&#9724;</span>Create Account</a>
        </div>
        <a href="settings.php"' . ($selected == 'settings' ? ' class="selected"' : '') . '><i class="fas fa-tools"></i>Settings</a>
        
    ';
// DO NOT INDENT THE BELOW CODE
echo <<<EOT
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width,minimum-scale=1">
		<title>$title</title>
		<link href="admin.css" rel="stylesheet" type="text/css">
	</head>
	<body class="admin">
        <aside class="responsive-width-100 responsive-hidden">
            <h1>Admin</h1>
            $admin_links
            
        </aside>
        <main class="responsive-width-100">
            <header>
                <a class="responsive-toggle" href="#">
                    <i class="fas fa-bars"></i>
                </a>
                <div class="space-between"></div>
                <div class="dropdown right">
                    <i class="fas fa-user-circle"></i>
                    <div class="list">
                        <a href="account.php?id={$_SESSION['account_id']}">Edit Profile</a>
                        <a href="logout.php">Logout</a>
                    </div>
                </div>
            </header>
EOT;
}
// Template admin footer
function template_admin_footer($js_script = '') {
        $js_script = $js_script ? '<script>' . $js_script . '</script>' : '';
// DO NOT INDENT THE BELOW CODE
echo <<<EOT
        </main>
        <script src="admin.js"></script>
        {$js_script}
    </body>
</html>
EOT;
}
?>