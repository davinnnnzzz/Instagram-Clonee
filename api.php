<?php
// Simple API endpoints for login/register/edit/delete profile
session_start();
include 'config/koneksi.php';
header('Content-Type: application/json; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

function json($data){ echo json_encode($data); exit; }

if ($action === 'register' && $method === 'POST'){
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? null;
    $password = $_POST['password'] ?? null;
    if (!$username || !$password) json(['ok'=>false,'msg'=>'username/password required']);

    $passHash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = mysqli_prepare($koneksi, "INSERT INTO users (username,email,password,avatar) VALUES (?,?,?,?)");
    $defaultAvatar = 'https://picsum.photos/id/1005/150/150';
    mysqli_stmt_bind_param($stmt,'ssss',$username,$email,$passHash,$defaultAvatar);
    if (mysqli_stmt_execute($stmt)){
        $newId = mysqli_insert_id($koneksi);
        // set session
        $_SESSION['user_id'] = $newId;
        json(['ok'=>true,'msg'=>'registered','user'=>['id'=>$newId,'username'=>$username,'avatar'=>$defaultAvatar]]);
    } else {
        json(['ok'=>false,'msg'=>mysqli_error($koneksi)]);
    }
}

if ($action === 'login' && $method === 'POST'){
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    if (!$username || !$password) json(['ok'=>false,'msg'=>'username/password required']);

    $stmt = mysqli_prepare($koneksi, "SELECT id,username,password,avatar,bio,link FROM users WHERE username=? LIMIT 1");
    mysqli_stmt_bind_param($stmt,'s',$username);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($res);
    if (!$user) json(['ok'=>false,'msg'=>'user not found']);
    if (!password_verify($password, $user['password'])) json(['ok'=>false,'msg'=>'invalid password']);

    // return user data
    unset($user['password']);
    // set session
    $_SESSION['user_id'] = $user['id'];
    json(['ok'=>true,'user'=>$user]);
}

if ($action === 'get_user' && $method === 'GET'){
    $id = intval($_GET['id'] ?? 0);
    if (!$id) json(['ok'=>false,'msg'=>'id required']);
    $res = mysqli_query($koneksi, "SELECT id,username,avatar,bio,link,email FROM users WHERE id=$id");
    $user = mysqli_fetch_assoc($res);
    json(['ok'=>true,'user'=>$user]);
}

if ($action === 'session_user' && $method === 'GET'){
    $uid = $_SESSION['user_id'] ?? 0;
    if (!$uid) json(['ok'=>false,'msg'=>'no session']);
    $stmt = mysqli_prepare($koneksi, "SELECT id,username,avatar,bio,link,email FROM users WHERE id=? LIMIT 1");
    mysqli_stmt_bind_param($stmt,'i',$uid);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($res);
    if (!$user) json(['ok'=>false,'msg'=>'not found']);
    json(['ok'=>true,'user'=>$user]);
}

if ($action === 'logout' && $method === 'POST'){
    unset($_SESSION['user_id']);
    session_destroy();
    json(['ok'=>true]);
}

// --- POSTS API ---
if ($action === 'get_posts' && $method === 'GET'){
    $user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
    $where = $user_id ? "WHERE p.user_id=$user_id" : "";
    $q = "SELECT p.id, p.type, p.media_url, p.caption, p.likes, p.created_at, p.user_id, u.username, u.avatar FROM posts p JOIN users u ON p.user_id=u.id $where ORDER BY p.created_at DESC";
    $res = mysqli_query($koneksi, $q);
    $out = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $row['mediaUrl'] = $row['media_url'];
        $row['username'] = $row['username'];
        $row['avatar'] = $row['avatar'];
        $row['likes'] = intval($row['likes']);
        $out[] = $row;
    }
    json(['ok'=>true,'posts'=>$out]);
}

if ($action === 'create_post' && $method === 'POST'){
    $user_id = intval($_POST['user_id'] ?? ($_SESSION['user_id'] ?? 0));
    $type = $_POST['type'] ?? 'image';
    $caption = $_POST['caption'] ?? '';
    if (!$user_id) json(['ok'=>false,'msg'=>'not authenticated']);

    // handle uploaded file if present
    $media_url = '';
    if (!empty($_FILES['media']) && $_FILES['media']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/public/uploads';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        $tmp = $_FILES['media']['tmp_name'];
        $orig = basename($_FILES['media']['name']);
        $ext = strtolower(pathinfo($orig, PATHINFO_EXTENSION));
        // basic validation: allow images, video, audio
        $allowed = ['jpg','jpeg','png','gif','webp','mp4','webm','mp3','wav','ogg'];
        if (!in_array($ext, $allowed)) json(['ok'=>false,'msg'=>'file type not allowed']);
        // size limit 8MB
        if ($_FILES['media']['size'] > 8 * 1024 * 1024) json(['ok'=>false,'msg'=>'file too large']);
        $fname = uniqid('m_') . ($ext ? '.' . $ext : '');
        $target = $uploadDir . DIRECTORY_SEPARATOR . $fname;
        if (move_uploaded_file($tmp, $target)){
            // store relative web path
            $media_url = 'public/uploads/' . $fname;
        }
    } else {
        // accept media_url string (e.g., dataURL or remote URL)
        $media_url = $_POST['media_url'] ?? '';
    }

    $id = (int)(microtime(true)*1000);
    $stmt = mysqli_prepare($koneksi, "INSERT INTO posts (id,user_id,type,media_url,caption) VALUES (?,?,?,?,?)");
    mysqli_stmt_bind_param($stmt,'iisss',$id,$user_id,$type,$media_url,$caption);
    if (mysqli_stmt_execute($stmt)){
        $res = mysqli_query($koneksi, "SELECT p.id, p.type, p.media_url, p.caption, p.likes, p.created_at, p.user_id, u.username, u.avatar FROM posts p JOIN users u ON p.user_id=u.id WHERE p.id=$id LIMIT 1");
        $post = mysqli_fetch_assoc($res);
        $post['mediaUrl'] = $post['media_url'];
        json(['ok'=>true,'post'=>$post]);
    } else json(['ok'=>false,'msg'=>mysqli_error($koneksi)]);
}

if ($action === 'delete_post' && $method === 'POST'){
    $id = intval($_POST['id'] ?? 0);
    $uid = $_SESSION['user_id'] ?? 0;
    if (!$id) json(['ok'=>false,'msg'=>'id required']);
    if (!$uid) json(['ok'=>false,'msg'=>'not authenticated']);
    // check ownership
    $r = mysqli_query($koneksi, "SELECT user_id FROM posts WHERE id=$id");
    $rr = mysqli_fetch_assoc($r);
    if (!$rr) json(['ok'=>false,'msg'=>'not found']);
    if (intval($rr['user_id']) !== intval($uid)) json(['ok'=>false,'msg'=>'forbidden']);
    $stmt = mysqli_prepare($koneksi, "DELETE FROM posts WHERE id=?");
    mysqli_stmt_bind_param($stmt,'i',$id);
    if (mysqli_stmt_execute($stmt)) json(['ok'=>true]);
    else json(['ok'=>false,'msg'=>mysqli_error($koneksi)]);
}

if ($action === 'update_post' && $method === 'POST'){
    $id = intval($_POST['id'] ?? 0);
    $caption = $_POST['caption'] ?? '';
    $uid = $_SESSION['user_id'] ?? 0;
    if (!$id) json(['ok'=>false,'msg'=>'id required']);
    if (!$uid) json(['ok'=>false,'msg'=>'not authenticated']);
    // check ownership
    $r = mysqli_query($koneksi, "SELECT user_id FROM posts WHERE id=$id");
    $rr = mysqli_fetch_assoc($r);
    if (!$rr) json(['ok'=>false,'msg'=>'not found']);
    if (intval($rr['user_id']) !== intval($uid)) json(['ok'=>false,'msg'=>'forbidden']);
    $stmt = mysqli_prepare($koneksi, "UPDATE posts SET caption=? WHERE id=?");
    mysqli_stmt_bind_param($stmt,'si',$caption,$id);
    if (mysqli_stmt_execute($stmt)) json(['ok'=>true]);
    else json(['ok'=>false,'msg'=>mysqli_error($koneksi)]);
}

if ($action === 'update_profile' && $method === 'POST'){
    $id = intval($_POST['id'] ?? 0);
    $username = $_POST['username'] ?? '';
    $bio = $_POST['bio'] ?? '';
    $link = $_POST['link'] ?? '';
    $avatar = $_POST['avatar'] ?? '';
    if (!$id) json(['ok'=>false,'msg'=>'id required']);

    $stmt = mysqli_prepare($koneksi, "UPDATE users SET username=?, bio=?, link=?, avatar=? WHERE id=?");
    mysqli_stmt_bind_param($stmt,'ssssi',$username,$bio,$link,$avatar,$id);
    if (mysqli_stmt_execute($stmt)) {
        // return updated user
        $res = mysqli_query($koneksi, "SELECT id,username,avatar,bio,link,email FROM users WHERE id=$id");
        $user = mysqli_fetch_assoc($res);
        json(['ok'=>true,'user'=>$user]);
    }
    else json(['ok'=>false,'msg'=>mysqli_error($koneksi)]);
}

if ($action === 'delete_profile' && $method === 'POST'){
    $id = intval($_POST['id'] ?? 0);
    if (!$id) json(['ok'=>false,'msg'=>'id required']);
    $stmt = mysqli_prepare($koneksi, "DELETE FROM users WHERE id=?");
    mysqli_stmt_bind_param($stmt,'i',$id);
    if (mysqli_stmt_execute($stmt)) json(['ok'=>true]);
    else json(['ok'=>false,'msg'=>mysqli_error($koneksi)]);
}

json(['ok'=>false,'msg'=>'unknown action']);
