<?php
$base_dir = __DIR__ . '/music';
$req_dir = isset($_GET['dir']) ? (string)$_GET['dir'] : '';
$req_dir = str_replace(['../', '..\\'], '', $req_dir);
$current_path = realpath($base_dir . '/' . $req_dir);

$data = ['folders' => [], 'mp3s' => []];
$items = @scandir($current_path) ?: [];

foreach ($items as $item) {
    if ($item === '.' || $item === '..') continue;
    $full_path = $current_path . '/' . $item;
    $rel_path = $req_dir !== '' ? $req_dir . '/' . $item : $item;

    if (is_dir($full_path)) {
        $img = 'img/' . md5($rel_path) . '.jpg';
        $data['folders'][] = [
            'name' => $item, 'path' => $rel_path, 
            'image' => (file_exists(__DIR__ . '/' . $img) && filesize(__DIR__ . '/' . $img) > 0) ? $img : null
        ];
    } elseif (strtolower(pathinfo($item, PATHINFO_EXTENSION)) === 'mp3') {
        $data['mp3s'][] = ['name' => $item, 'url' => 'music/' . rawurlencode($rel_path)];
    }
}
header('Content-Type: application/json');
echo json_encode($data);
