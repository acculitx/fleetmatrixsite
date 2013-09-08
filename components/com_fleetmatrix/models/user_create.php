<?php
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.user.helper' );

$db =& JFactory::getDBO();

function get_parent_id($id)
{
    $db =& JFactory::getDBO();
    $query = "SELECT parent_id FROM #__usergroups WHERE id = '$id' order by parent_id DESC";
    $db->setQuery($query); // Set query
    $result = $db->loadResult(); // Load result
    return $result;
}

function MakeAddition($fusername) {
    $db =& JFactory::getDBO();
    $finished = false;  // We're not finished loop yet (we just started the loop)
    $i = 1; // Counting
    while(!$finished) {                          // While not finished
        $sql = "SELECT COUNT(*) ".$db->nameQuote('username')." FROM ".$db->nameQuote('#__users')." WHERE ".$db->nameQuote('username')." = ".$db->quote($fusername.$i).""; // Check in DB if the alternative username doesnt exist
        $db->setQuery($sql);
        $num_rows_add = $db->loadResult();
        if ($num_rows_add == "0") {        // If username DOES NOT exist...
            $finished = true;                    // We are finished stop loop
        }
        $i++;
    }
    return $i-1;
}

function clean_now($text)
{
    $text=strtolower($text);
    $code_entities_match = array(' ','--','&quot;','!','@','#','$','%','^','&','*','(',')','_','+','{','}','|',':','"','<','>','?','[',']','\\',';',"'",',','/','*','+','~','`','=');
    $code_entities_replace = array('-','-','','','','','','','','','','','','','','','','','','','','','','','');
    $text = str_replace($code_entities_match, $code_entities_replace, $text);
    return $text;
}

function createRandomPassword() {
    $chars = "abcdefghijkmnopqrstuvwxyz0123456789";
    srand((double)microtime()*1000000);
    $i = 0;
    $pass = '' ;
    while ($i < 10) {
        $num = rand() % 33;
        $tmp = substr($chars, $num, 1);
        $pass = $pass . $tmp;
        $i++;
    }
    return $pass;
}

function getCryptedPassword($plaintext, $salt = '', $encryption = 'md5-hex', $show_encrypt = false)
{
    $salt = JUserHelper::getSalt($encryption, $salt, $plaintext);
    $encrypted = ($salt) ? md5($plaintext.$salt) : md5($plaintext);
    return ($show_encrypt) ? '{MD5}'.$encrypted : $encrypted;
}

function createJoomlaUser($data) {
    $user   = &JFactory::getUser();
    $uid    = $user->get('id');
    $custumgroupparentids = ""; // Initialize variable
    $normalgroupids = ""; // Initialize variable
    $normalgroupidsstring = ""; // Initialize variable
    $custumgroupparentidsstring = ""; // Initialize variable

    $usergroups = $user->getAuthorisedGroups(); // get all usergroups for this user

    foreach ($usergroups as $usergroup) { // For each usergroup do something
        if($usergroup > "8") { // If the usergroup ID is higher then 8 and therefore is a custum usergroup
            $result = get_parent_id($usergroup);
            // If the resulting parent_id is also a custom usergroup
            while($result > 8) { // Loop to get parent_id untill we find a parent id of the standard joomla usergroups
                $result = get_parent_id($result);
            }
            $custumgroupparentidsstring .= $result.","; // Make comma seperated string out of results
        } else { // Else of: if($usergroup > "8") { - (usergroup is not higher then 8 and therefore is a 'normal' usergroup)
            $normalgroupidsstring .= $usergroup.","; // Make comma seperated string out of results
        }
    } // END - (foreach ($usergroups as $usergroup) { - For each usergroup do something)

    $custumgroupparentidsstring = substr($custumgroupparentidsstring, 0, -1); // Delete not needed comma's
    $custumgroupparentids = explode(",", $custumgroupparentidsstring); // Explode comma seperated string to array
    $normalgroupidsstring = substr($normalgroupidsstring, 0, -1); // Delete not needed comma's
    $normalgroupids = explode(",", $normalgroupidsstring); // Explode comma seperated string to array
    $allgroupids = array_merge($custumgroupparentids,$normalgroupids); // Merge the 2 arrays
    $allgroupids = array_unique($allgroupids); // Remove duplicate value's from array
    sort($allgroupids, SORT_NUMERIC); // Sort all groups numeric
    $highestgroup = max($allgroupids); // Get highest groupid or parent groupid
    $groupid = $highestgroup;

    $showpass = $data['password'];
    $password = getCryptedPassword($showpass, $salt= '', $encryption= 'md5-hex', $show_encrypt=false);

    $name = $data['name'];

    # |  3 |         2 |   3 |   8 | Reseller      |
    # |  4 |         3 |   4 |   7 | Company       |
    # | 16 |         4 |   5 |   6 | Group         |

    # (1, 'Reseller'),
    # (2, 'Group'),
    # (3, 'Company'),


    switch ($data['entity_type']) {
        case 1:
            # Reseller
            $usertype = 3;
            $usertypename = 'Reseller';
            break;
        case 2:
            # Company
            $usertype = 4;
            $usertypename = 'Company';
            break;
        case 3:
            # Group
            $usertype = 16;
            $usertypename = 'Group';
            break;
    }
    $group = $usertype;

    if($group > 8) {
        $parentgrouporgroup = get_parent_id($group);
        while($parentgrouporgroup > 8) { // Loop to get parent_id untill we find a parent_id of the standard joomla usergroups
            $parentgrouporgroup = get_parent_id($parentgrouporgroup);
        }
    } else {
        $parentgrouporgroup = $group;
    }

    $username = clean_now($data['username']);

    $db =& JFactory::getDBO();

    $query = "SELECT ".$db->nameQuote('title')." FROM ".$db->nameQuote('#__usergroups')." WHERE id = ".$db->quote($usertype)."";
    $db->setQuery($query);
    $usertypename = $db->loadResult();
    if($usertypename == "") {
        die("user group id not found in db");
    }

    $sql = "SELECT COUNT(*) ".$db->nameQuote('username')." FROM ".$db->nameQuote('#__users')." WHERE ".$db->nameQuote('username')." = ".$db->quote($username)."";
    $db->setQuery($sql);
    $num_rows = $db->loadResult();
    if($num_rows != 0) {
        die("user name taken");
    }
    $email = trim($data['email']);

    $sql = "SELECT COUNT(*) ".$db->nameQuote('email')." FROM ".$db->nameQuote('#__users')." WHERE ".$db->nameQuote('email')." = ".$db->quote($email)."";
    $db->setQuery($sql);
    $num_rows = $db->loadResult();
    if($num_rows == 0){
        //die("email already in use");
    }

    $block = '0';
    $sendmail = '0';

    $sql1 = "INSERT INTO ".$db->nameQuote('#__users')." SET
    ".$db->nameQuote('name')."            = ".$db->quote($name).",
    ".$db->nameQuote('username')."        = ".$db->quote($username).",
    ".$db->nameQuote('email')."           = ".$db->quote($email).",
    ".$db->nameQuote('password')."        = ".$db->quote($password).",
    ".$db->nameQuote('usertype')."        = ".$db->quote($usertypename).",
    ".$db->nameQuote('block')."           = ".$db->quote($block).",
    ".$db->nameQuote('sendEmail')."       = ".$db->quote($sendmail).",
    ".$db->nameQuote('registerDate')."    = NOW(),
    ".$db->nameQuote('lastvisitDate')."   = ".$db->quote('0000-00-00 00:00:00').",
    ".$db->nameQuote('activation')."      = '',
    ".$db->nameQuote('params')."          = ''
    ";
    $db->setQuery($sql1);
    $db->query();

    $user_id = $db->insertid();

    $sql2 = "INSERT INTO ".$db->nameQuote('#__user_usergroup_map')." SET
    ".$db->nameQuote('group_id')."        = ".$db->quote($usertype).",
    ".$db->nameQuote('user_id')."         = ".$db->quote($user_id)."
    ";
    $db->setQuery($sql2);
    $db->query();

    $config =& JFactory::getConfig();
    $fromname = $config->getValue( 'config.fromname' );
    $from = $config->getValue( 'config.mailfrom' );
    $recipient = $email;
    $subject = "Your user details for ".$_SERVER['HTTP_HOST'];
    $body = <<<BODY
You have been added as a User to __HTTP_HOST__.
<br><br>
This e-mail contains your username and password to log in to __HTTP_HOST__.
<br><br>
Username: __USERNAME__
<br>
Password: __PASSWORD__
<br><br>
Please do not respond to this message as it is automatically generated
and is for information purposes only.
BODY;
    $body = str_replace('__HTTP_HOST__', $_SERVER['HTTP_HOST'], $body);
    $body = str_replace('__USERNAME__', $username, $body);
    $body = str_replace('__PASSWORD__', $showpass, $body);

    JUtility::sendMail($from, $fromname, $recipient, $subject, $body, $mode=1, $cc=null, $bcc=null, $attachment=null, $replyto=null, $replytoname=null);

    $recipient = $from;
    $subject = "A new user has been added to ".$_SERVER['HTTP_HOST']."";
    $body   = "A new user has been added to ".$_SERVER['HTTP_HOST'].". This is a copy off the email notification that this user received:<br>".$body;

    JUtility::sendMail($from, $fromname, $recipient, $subject, $body, $mode=1, $cc=null, $bcc=null, $attachment, $replyto=null, $replytoname=null);

    return $user_id;
}

?>