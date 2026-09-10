<?php
include __DIR__ . '/config/koneksi.php';

function getUserIdByUsername($koneksi, $username){
    $stmt = mysqli_prepare($koneksi, "SELECT id FROM users WHERE username = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, 's', $username);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $r = mysqli_fetch_assoc($res);
    return $r['id'] ?? 0;
}

function createUser($koneksi, $username, $email, $password, $avatar, $bio = '', $link = ''){
    $exists = getUserIdByUsername($koneksi, $username);
    if ($exists) return $exists;
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = mysqli_prepare($koneksi, "INSERT INTO users (username,email,password,avatar,bio,link) VALUES (?,?,?,?,?,?)");
    mysqli_stmt_bind_param($stmt, 'ssssss', $username, $email, $hash, $avatar, $bio, $link);
    if (mysqli_stmt_execute($stmt)){
        return mysqli_insert_id($koneksi);
    }
    return 0;
}

function createPost($koneksi, $user_id, $type, $media_url, $caption){
    // posts.id is BIGINT primary key (not auto increment) - use microtime
    $id = (int)(microtime(true) * 1000);
    $stmt = mysqli_prepare($koneksi, "INSERT INTO posts (id,user_id,type,media_url,caption) VALUES (?,?,?,?,?)");
    mysqli_stmt_bind_param($stmt, 'iiiss', $id, $user_id, $type, $media_url, $caption);
    mysqli_stmt_execute($stmt);
    return $id;
}

$demoUsers = [
    [
        'username' => 'demo_user',
        'email' => 'demo@example.com',
        'password' => 'password123',
        'avatar' => 'https://picsum.photos/id/1005/150/150',
        'bio' => 'Akun demo',
        'link' => 'https://example.com'
    ],
    [
        'username' => 'kreator_1',
        'email' => 'kreator1@example.com',
        'password' => 'secret',
        'avatar' => 'https://picsum.photos/id/1025/150/150',
        'bio' => 'Creator demo',
        'link' => ''
    ]
];

$created = [];
foreach ($demoUsers as $u){
    $id = createUser($koneksi, $u['username'], $u['email'], $u['password'], $u['avatar'], $u['bio'], $u['link']);
    $created[$u['username']] = $id;
}

// create sample posts for demo_user
$demoId = $created['demo_user'] ?? 0;
if ($demoId){
    createPost($koneksi, $demoId, 'image', 'https://picsum.photos/id/1015/600/600', 'Selamat datang di demo_user!');
    createPost($koneksi, $demoId, 'image', 'https://picsum.photos/id/1016/600/600', 'Postingan kedua dari demo_user');
}

$kreatorId = $created['kreator_1'] ?? 0;
if ($kreatorId){
    createPost($koneksi, $kreatorId, 'image', 'https://picsum.photos/id/1018/600/600', 'Halo dari kreator_1');
}

// additional seed: more posts and stories
if ($demoId){
    createPost($koneksi, $demoId, 'image', 'https://picsum.photos/id/1020/600/600', 'Pemandangan epic');
    createPost($koneksi, $demoId, 'image', 'https://picsum.photos/id/1021/600/600', 'Senja manis');
    // stories
    $sid = (int)(microtime(true)*1000);
    $stmt = mysqli_prepare($koneksi, "INSERT INTO stories (id,user_id,type,media_url,text,caption) VALUES (?,?,?,?,?,?)");
    $txt = 'Status singkat: Halo semua!';
    $murl = 'https://picsum.photos/id/1060/400/800';
    $stype = 'image';
    $caption_text = 'Cerita Demo';
    mysqli_stmt_bind_param($stmt,'iissss',$sid,$demoId,$stype,$murl,$txt,$caption_text);
    @mysqli_stmt_execute($stmt);
}

if ($kreatorId){
    createPost($koneksi, $kreatorId, 'image', 'https://picsum.photos/id/1035/600/600', 'Posting kreator 2');
}

echo "Seed selesai. Users created:\n";
foreach ($created as $name => $id) echo " - $name => $id\n";

// verify counts
$res = mysqli_query($koneksi, "SELECT COUNT(*) as c FROM users");
$r = mysqli_fetch_assoc($res);
echo "Total users: " . ($r['c'] ?? 0) . "\n";
$res = mysqli_query($koneksi, "SELECT COUNT(*) as c FROM posts");
$r = mysqli_fetch_assoc($res);
echo "Total posts: " . ($r['c'] ?? 0) . "\n";

?>