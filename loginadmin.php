<?php
   session_start();
   define('ADMIN_PASS', '1');
   $session_timeout = 600;
   $database = './usersdb.php';
   $admin_password = isset($_COOKIE['admin_password']) ? $_COOKIE['admin_password'] : '';
   if (empty($admin_password))
   {
      if (isset($_POST['admin_password']))
      {
         $admin_password = md5($_POST['admin_password']);
         if ($admin_password == md5(ADMIN_PASS))
         {
            setcookie('admin_password', $admin_password, time() + $session_timeout);
         }
      }
   }
   else
   if ($admin_password == md5(ADMIN_PASS))
   {
      setcookie('admin_password', $admin_password, time() + $session_timeout);
   }
   if (!file_exists($database))
   {
      echo 'User database not found!';
      exit;
   }
   $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
   $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
   $index = 0;
   $userindex = -1;
   $items = file($database, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
   foreach($items as $line)
   {
      list($username) = explode('|', trim($line));
      if ($id == $username)
      {
         $userindex = $index;
      }
      $index++;
   }
   if (!empty($action))
   {
      if ($action == 'delete')
      {
         if ($userindex == -1)
         {
            echo 'User not found!';
            exit;
         }
         $file = fopen($database, 'w');
         $index = 0;
         foreach($items as $line)
         {
            if ($index != $userindex)
            {
               fwrite($file, $line);
               fwrite($file, "\r\n");
            }
            $index++;
         }
         fclose($file);
         header('Location: '.basename(__FILE__));
         exit;
      }
      else
      if ($action == 'update')
      {
         $file = fopen($database, 'w');
         $index = 0;
         foreach($items as $line)
         {
            if ($index == $userindex)
            {
               $values = explode('|', trim($line));
               $values[0] = $_POST['username'];
               if (!empty($_POST['password']))
               {
                  $values[1] = md5($_POST['password']);
               }
               $values[2] = $_POST['email'];
               $values[3] = $_POST['fullname'];
               $values[4] = $_POST['active'];
               $line = '';
               for ($i=0; $i < count($values); $i++)
               {
                  if ($i != 0)
                     $line .= '|';
                  $line .= $values[$i];
               }
            }
            fwrite($file, $line);
            fwrite($file, "\r\n");
            $index++;
         }
         fclose($file);
         header('Location: '.basename(__FILE__));
         exit;
      }
      else
      if ($action == 'create')
      {
         for ($i=0; $i < $index; $i++)
         {
            if ($usernames[$i] == $_POST['username'])
            {
               echo 'User already exists!';
               exit;
            }
         }
         $file = fopen($database, 'a');
         fwrite($file, $_POST['username']);
         fwrite($file, '|');
         fwrite($file, md5($_POST['password']));
         fwrite($file, '|');
         fwrite($file, $_POST['email']);
         fwrite($file, '|');
         fwrite($file, $_POST['fullname']);
         fwrite($file, '|');
         fwrite($file, $_POST['active']);
         fwrite($file, '|NA');
         fwrite($file, "\r\n");
         fclose($file);
         header('Location: '.basename(__FILE__));
         exit;
      }
      else
      if ($action == 'logout')
      {
         session_unset();
         session_destroy();
         setcookie('admin_password', '', time() - 3600);
         header('Location: '.basename(__FILE__));
         exit;
      }
   }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<title>User administrator</title>
<style type="text/css">
p
{
   font-size: 13px;
   font-family: Arial;
   font-weight: normal;
   text-decoration: none;
   color: #000000;
}
th
{
   font-size: 13px;
   font-family: Arial;
   font-weight: normal;
   text-decoration: none;
   background-color: #878787;
   color: #FFFFFF;
   text-align: left;
}
td
{
   font-size: 13px;
   font-family: Arial;
   font-weight: normal;
   text-decoration: none;
   color: #000000;
}
input, select
{
   font-size: 13px;
   font-family: Arial;
   font-weight: normal;
   text-decoration: none;
   color: #000000;
   border:1px #000000 solid;
}
</style>
</head>
<body bgcolor="#FFFFFF" topmargin="0" leftmargin="0" marginwidth="0" marginheight="0">
<?php
   if ($admin_password != md5(ADMIN_PASS))
   {
      echo "<center>\n";
      echo "<p>User administrator login</p>\n";
      echo "<form method=\"post\" action=\"" .basename(__FILE__) . "\">\n";
      echo "<input type=\"password\" name=\"admin_password\" size=\"20\" />\n";
      echo "<input type=\"submit\" value=\"Login\" name=\"submit\" />\n";
      echo "</form>\n";
      echo "</center>\n";
   }
   else
   {
      if (!empty($action))
      {
         if (($action == 'edit') || ($action == 'new'))
         {
            if ($userindex != -1)
            {
               $values = explode('|', trim($items[$userindex]));
               $username_value = $values[0];
               $email_value = $values[2];
               $fullname_value = $values[3];
               $active_value = $values[4];
            }
            else
            {
               $username_value = "";
               $fullname_value = "";
               $email_value = "";
               $active_value = "0";
            }
            echo "<center>\n";
            echo "<form action=\"" . basename(__FILE__) . "\" method=\"post\">\n";
            echo "<table border=\"0\">\n";
            if ($action == 'new')
            {
               echo "<input type=\"hidden\" name=\"action\" value=\"create\">\n";
            }
            else
            {
               echo "<input type=\"hidden\" name=\"action\" value=\"update\">\n";
            }
            echo "<input type=\"hidden\" name=\"id\" value=\"". $id . "\">\n";
            echo "<tr><td>Username:</td>\n";
            echo "<td><input type=\"text\" size=\"50\" name=\"username\" value=\"" . $username_value . "\"></td></tr>\n";
            echo "<tr><td>Password:</td>\n";
            echo "<td><input type=\"password\" size=\"50\" name=\"password\" value=\"\"></td></tr>\n";
            echo "<tr><td>Fullname:</td>\n";
            echo "<td><input type=\"text\" size=\"50\" name=\"fullname\" value=\"" . $fullname_value . "\"></td></tr>\n";
            echo "<tr><td>Email:</td>\n";
            echo "<td><input type=\"text\" size=\"50\" name=\"email\" value=\"" . $email_value . "\"></td></tr>\n";
            echo "<tr><td>Active:</td>\n";
            echo "<td style=\"text-align:left\"><select name=\"active\" size=\"1\"><option " . ($active_value == "0" ? "selected " : "") . "value=\"0\">Not active</option><option " . ($active_value != "0" ? "selected " : "") . "value=\"1\">Active</option></select></td></tr>\n";
            echo "<tr><td>&nbsp;</td><td style=\"text-align:left\"><input type=\"submit\" name=\"cmdSubmit\" value=\"Save\">";
            echo "&nbsp;&nbsp;";
            echo "<input type=\"reset\" name=\"cmdReset\" value=\"Reset\">&nbsp;&nbsp;";
            echo "<input type=\"button\" name=\"cmdBack\" value=\"Back\" onclick=\"location.href='" . basename(__FILE__) . "'\"></td></tr>\n";
            echo "</table>\n";
            echo "</form>\n";
            echo "</center>\n";
         }
      }
      else
      {
         echo "<table width=\"100%\" cellspacing=\"0\" cellpadding=\"2\">\n";
         echo "<tr><th>Username</th>\n";
         echo "<th>Fullname</th>\n";
         echo "<th>Email</th>\n";
         echo "<th>Active</th>\n";
         echo "<th>Action</th></tr>\n";
         foreach($items as $line)
         {
            list($username, $password, $email, $fullname, $active) = explode('|', trim($line));
            echo "<tr>\n";
            echo "<td>" . $username . "</td>\n";
            echo "<td>" . $fullname . "</td>\n";
            echo "<td>" . $email . "</td>\n";
            echo "<td>" . ($active == "0" ? "not active" : "active") . "</td>\n";
            echo "<td>\n";
            echo "   <a href=\"" . basename(__FILE__) . "?action=edit&id=" . $username . "\">Edit</a> | \n";
            echo "   <a href=\"" . basename(__FILE__) . "?action=delete&id=" . $username . "\">Delete</a>\n";
            echo "</td>\n";
            echo "</tr>\n";
         }
         echo "</table>\n";
         echo "<p><a href=\"" . basename(__FILE__) . "?action=new\">Create new user</a>&nbsp;&nbsp;<a href=\"" . basename(__FILE__) . "?action=logout\">Logout</a></p>\n";
      }
   }
?>
</body>
</html>
